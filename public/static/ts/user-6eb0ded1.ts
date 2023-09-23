/**
 * @description Login, obtain user information, log out, clear token logic, modification is not recommended
 */
import { useAclStore } from './acl'
import { useSettingsStore } from './settings'
import { useTabsStore } from './tabs'
import { getUserInfo, login, logout } from '/@/api/user'
import { tokenName } from '/@/config'
import { getToken, removeToken, setToken } from '/@/utils/token'
import { isArray, isString } from '/@/utils/validate'
import { gp } from '/@vab/plugins/vab'

export const useUserStore = defineStore('user', {
  state: (): UserModuleType => ({
    token: getToken() as string,
    username: 'tourists',
    avatar: './static/svg/avatar.svg',
    userInfo: {},
  }),
  getters: {
    getToken: (state) => state.token,
    getUsername: (state) => state.username,
    getAvatar: (state) => state.avatar,
  },
  actions: {
    /**
     * @description Set token
     * @param {*} token
     */
    setToken(token: string) {
      this.token = token
      setToken(token)
    },
    /**
     * @description Set user info
     * @param info
     */
    setUserInfo(info: object) {
      this.userInfo = info
    },
    /**
     * @description Set username
     * @param {*} username
     */
    setUsername(username: string) {
      this.username = username
    },
    /**
     * @description Set Avatar
     * @param {*} avatar
     */
    setAvatar(avatar: string) {
      this.avatar = avatar
    },
    /**
     * @description When logging in to intercept and release, set a virtual role
     */
    setVirtualRoles() {
      const aclStore = useAclStore()
      aclStore.setFull(true)
      this.setUsername('admin(Login blocking is not enabled)')
      this.setAvatar('./static/svg/avatar.svg')
    },
    /**
     * @description Set token and send reminder
     * @param {string} token update token
     * @param {string} tokenName Token name
     */
    afterLogin(token: string, tokenName: string) {
      const settingsStore = useSettingsStore()
      if (token) {
        this.setToken(token)
        const hour = new Date().getHours()
        const thisTime =
          hour < 8
            ? 'Good morning'
            : hour <= 11
            ? 'Good morning'
            : hour <= 13
            ? 'Good afternoon'
            : hour < 18
            ? 'Good evening'
            : 'Good night'
        gp.$baseNotify(`Welcome to ${settingsStore.title}`, `${thisTime}!`)
      } else {
        const err = `The login interface is abnormal and ${tokenName} is not returned correctly...`
        gp.$baseMessage(err, 'error', 'hey')
        throw err
      }
    },
    /**
     * @description Log in
     * @param {*} userInfo
     */
    async login(userInfo: any) {
      const { data } = await login(userInfo)
      this.afterLogin(data, tokenName)
    },
    /**
     * @description Obtain User Information Interface This interface is very, very important. If the underlying logic is not clear, modification of this method is prohibited. Wrong modification may cause the entire framework to fail to function normally.
     * @returns
     */
    async getUserInfo() {
      const { data } = await getUserInfo()
      const { username, avatar, roles, permissions } = data
      /**
       * Check whether the returned data is normal. If there are no corresponding parameters, the default username, avatar, Roles and Permissions will be used.
       * username {String}
       * avatar {String}
       * roles {List}
       * ability {List}
       */
      if (
        (username && !isString(username)) ||
        (avatar && !isString(avatar)) ||
        (roles && !isArray(roles)) ||
        (permissions && !isArray(permissions))
      ) {
        const err = 'The core interface of getUserInfo is abnormal. Please check whether the returned JSON format is correct.'
        gp.$baseMessage(err, 'error', 'hey')
        throw err
      } else {
        const aclStore = useAclStore()
        // If you do not use username, you can delete the following code
        if (username) this.setUsername(username)
        // If you do not use username, you can delete the following code
        if (data) this.setUserInfo(data)
        // If you do not use avatar, you can delete the following code
        if (avatar) this.setAvatar(avatar)
        // If you do not use roles permission control, you can delete the following code
        if (roles) aclStore.setRole(roles)
        // If you do not use permissions permission control, you can delete the following code
        if (permissions) aclStore.setPermission(permissions)
      }
    },
    /**
     * @description sign out
     */
    async logout() {
      await logout()
      await this.resetAll()
      await location.reload()
    },
    /**
     * @description Reset token, roles, permission, router, tabsBar, etc.
     */
    async resetAll() {
      const aclStore = useAclStore()
      const tabsStore = useTabsStore()
      await removeToken()
      this.setToken('')
      this.setUsername('tourists')
      this.setAvatar('./static/svg/avatar.svg')
      await aclStore.setPermission([])
      await aclStore.setFull(false)
      await aclStore.setRole([])
      await tabsStore.delAllVisitedRoutes()
    },
  },
})
