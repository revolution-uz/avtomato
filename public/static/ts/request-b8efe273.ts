import { stringify } from 'qs'
import { refreshToken } from '/@/api/refreshToken'
import { contentType, debounce, messageName, statusName, successCode, timeout } from '/@/config'
import router from '/@/router'
import { useUserStore } from '/@/store/modules/user'
import { isArray } from '/@/utils/validate'
import { addErrorLog, needErrorLog } from '/@vab/plugins/errorLog'
import { gp } from '/@vab/plugins/vab'

let loadingInstance: any

let refreshToking = false

let requests: any[] = []

// Operate normal Code array
const codeVerificationArray = isArray(successCode) ? [...successCode] : [...[successCode]]

const CODE_MESSAGE: any = {
  200: 'The server successfully returns the requested data',
  201: 'Create or modify data successfully',
  202: 'A request has entered the background queue (asynchronous task)',
  204: 'Data deleted successfully',
  400: 'Incorrect message sent',
  401: 'User does not have permission (token invalid, username, password incorrect, login expired)',
  402: 'Token expired',
  403: 'The user is authorized, but access is prohibited',
  404: 'The accessed resource does not exist',
  406: 'The requested format is not available',
  410: 'The requested resource has been permanently deleted and will not be seen',
  500: 'Server error',
  502: 'Gateway error',
  503: 'Service is unavailable, the server is temporarily overloaded or under maintenance',
  504: 'Gateway timeout',
}

/**
 * axios请求拦截器配置
 * @param config
 * @returns {any}
 */
const requestConf = (config: any) => {
  const userStore = useUserStore()
  const { token } = userStore
  // Non-standard writing method. You can customize headers according to the setting.config.js tokenName configuration.
  // if (token) config.headers[tokenName] = token

  // 规范写法 不可随意自定义
  if (token) config.headers['Authorization'] = `Bearer ${token}`

  if (config.data && config.headers['Content-Type'] === 'application/x-www-form-urlencoded;charset=UTF-8')
    config.data = stringify(config.data)

  if (debounce.some((item: string) => config.url.includes(item))) loadingInstance = gp.$baseLoading()
  return config
}

/**
 * refresh token
 * @param config Expired request configuration
 * @returns {any} Return results
 */
const tryRefreshToken = async (config: any) => {
  if (!refreshToking) {
    refreshToking = true
    try {
      const {
        data: { token },
      } = await refreshToken()
      if (token) {
        const { setToken } = useUserStore()
        setToken(token)
        // 已经刷新了token，将所有队列中的请求进行重试
        requests.forEach((cb) => cb(token))
        requests = []
        return instance(requestConf(config))
      }
    } catch (error) {
      console.error('refreshToken error =>', error)
      router.push({ path: '/login', replace: true }).then(() => {})
    } finally {
      refreshToking = false
    }
  } else {
    return new Promise((resolve) => {
      // Put the resolve into the queue, save it in the form of a function, and execute it directly after the token is refreshed.
      requests.push(() => {
        resolve(instance(requestConf(config)))
      })
    })
  }
}

/**
 * axios response interceptor
 * @param config {any} Request configuration
 * @param data {any} response data
 * @param status {any} HTTP status
 * @param statusText {any} HTTP status text
 * @returns {Promise<*|*>}
 */
const handleData = async ({ config, data, status, statusText }: { config: any; data: any; status: any; statusText: any }) => {
  const { resetAll } = useUserStore()
  if (loadingInstance) loadingInstance.close()
  // If data.code exists, overwrite the default code
  let code = data && data[statusName] ? data[statusName] : status
  // If the code is a normal operation code, the status is modified to 200
  if (codeVerificationArray.indexOf(data[statusName]) + 1) code = 200
  switch (code) {
    case 200:
      // Business level error handling, the following is handled assuming that restful has a unified output format (meaning there is a corresponding data format regardless of success or failure)
      // For example, response content:
      // Error content: { code: 1, msg: 'Illegal parameter' }
      // Correct content: { code: 200, data: { }, msg: 'Operation is normal' }
      // return data
      return data
    case 401:
      resetAll().then(() => {
        router.push({ path: '/login', replace: true }).then(() => {})
      })
      break
    case 402:
      return await tryRefreshToken(config)
    case 403:
      router.push({ path: '/403' }).then(() => {})
      break
  }
  //Exception handling
  // If data.msg exists, overwrite the default reminder message
  const errMsg = `${data && data[messageName] ? data[messageName] : CODE_MESSAGE[code] ? CODE_MESSAGE[code] : statusText}`
  // Whether to display highlighted errors (consistent with errorHandler hook triggering logic)
  gp.$baseMessage(errMsg, 'error', 'hey')
  if (needErrorLog()) addErrorLog({ message: errMsg, stack: data, isRequest: true })
  return Promise.reject(data)
}
/**
 * @description axios initialization
 */
const instance = axios.create({
  baseURL: `${import.meta.env.VITE_APP_BASE_URL}`,
  timeout,
  headers: {
    'Content-Type': contentType,
  },
})

/**
 * @description axios request interceptor
 */
instance.interceptors.request.use(requestConf, (error) => {
  return Promise.reject(error)
})

/**
 * @description axios response interceptor
 */
instance.interceptors.response.use(
  (response) => handleData(response),
  (error) => {
    const { response } = error
    if (response === undefined) {
      if (loadingInstance) loadingInstance.close()
      gp.$baseMessage(
        'Failure to connect to the backend interface may be caused by the following reasons: the backend does not support cross-domain CORS, the interface address does not exist, the request times out, etc. Please contact the administrator to troubleshoot the backend interface problem.',
        'error',
        'hey'
      )
      return {}
    } else return handleData(response)
  }
)

export default instance
