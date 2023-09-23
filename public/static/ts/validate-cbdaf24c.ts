/**
 * @description Determine whether it is an external link
 * @param value
 * @returns {boolean}
 */
export const isExternal = (value: string) => {
  return /^(https?:|mailto:|tel:|\/\/)/.test(value)
}

/**
 * @description Verify that the password is less than 6 characters
 * @param value
 * @returns {boolean}
 */
export const isPassword = (value: string) => {
  return value.length >= 6
}

/**
 * @description Determine whether it is a number
 * @param value
 * @returns {boolean}
 */
export const isNumber = (value: string) => {
  const reg = /^-?\d+(\.\d+)?$/
  return reg.test(value)
}

/**
 * @description Determine whether it is a name
 * @param value
 * @returns {boolean}
 */
export const isName = (value: string) => {
  const reg = /^[\u4e00-\u9fa5a-zA-Z0-9]+$/
  return reg.test(value)
}

/**
 * @description Determine whether it is an IP
 * @param value
 * @returns {boolean}
 */
export const isIP = (value: string) => {
  const reg =
    /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/
  return reg.test(value)
}

/**
 * @description Determine whether it is a URL
 * @param value
 * @returns {boolean}
 */
export const isUrl = (value: string) => {
  const reg = /^(http|https):\/\/[a-zA-Z-9]+([-.]\w+)*\.[a-zA-Z]{2,20}(:[-\d]{1,5})?(\/.*)?$/
  return reg.test(value)
}

/**
 * @description Determine whether it is a lowercase letter
 * @param value
 * @returns {boolean}
 */
export const isLowerCase = (value: string) => {
  const reg = /^[a-z]+$/
  return reg.test(value)
}

/**
 * @description Determine whether it is an uppercase letter
 * @param value
 * @returns {boolean}
 */
export const isUpperCase = (value: string) => {
  const reg = /^[A-Z]+$/
  return reg.test(value)
}

/**
 * @description Determine whether it starts with a capital letter
 * @param value
 * @returns {boolean}
 */
export const isAlphabets = (value: string) => {
  const reg = /^[A-Za-z]+$/
  return reg.test(value)
}

/**
 * @description Determine whether it is a string
 * @param value
 * @returns {boolean}
 */
export const isString = (value: any) => {
  return typeof value === 'string' || value instanceof String
}

/**
 * @description Determine whether it is an array
 * @param value
 */
export const isArray = (value: any) => {
  if (typeof Array.isArray === 'undefined') return Object.prototype.toString.call(value) === '[object Array]'
  return Array.isArray(value)
}

/**
 * @description Determine whether it is a port number
 * @param value
 * @returns {boolean}
 */
export const isPort = (value: string) => {
  const reg = /^([0-9]|[1-9]\d|[1-9]\d{2}|[1-9]\d{3}|[1-5]\d{4}|6[0-4]\d{3}|65[0-4]\d{2}|655[0-2]\d|6553[0-5])$/
  return reg.test(value)
}

/**
 * @description Determine whether it is a mobile phone number
 * @param value
 * @returns {boolean}
 */
export const isPhone = (value: string) => {
  const reg = /^1[3456789]\d{9}$/
  return reg.test(value)
}

/**
 * @description Determine whether it is an ID number
 * @param value
 * @returns {boolean}
 */
export const isIdCard = (value: string) => {
  const reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/
  return reg.test(value)
}

/**
 * @description Determine whether it is an email address
 * @param value
 * @returns {boolean}
 */
export const isEmail = (value: string) => {
  const reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
  return reg.test(value)
}

/**
 * @description Determine whether it is Chinese
 * @param value
 * @returns {boolean}
 */
export const isChina = (value: string) => {
  const reg = /^[\u4E00-\u9FA5]{2,4}$/
  return reg.test(value)
}

/**
 * @description Determine whether it is empty
 * @param value
 * @returns {boolean}
 */
export const isBlank = (value: string) => {
  return value === null || false || value === '' || value.trim() === '' || value.toLocaleLowerCase().trim() === 'null'
}

/**
 * @description Determine whether it is a fixed line
 * @param value
 * @returns {boolean}
 */
export const isTel = (value: string) => {
  const reg = /^(400|800)([0-9\\-]{7,10})|(([0-9]{4}|[0-9]{3})([- ])?)?([0-9]{7,8})(([- 转])*([0-9]{1,4}))?$/
  return reg.test(value)
}

/**
 * @description Determine whether it is json
 * @param value
 * @returns {boolean}
 */
export const isJson = (value: any) => {
  if (typeof value === 'string') {
    const obj = JSON.parse(value)
    return !!(typeof obj === 'object' && obj)
  }
  return false
}

/**
 * @description Determine whether it is Chinese
 * @param value
 * @returns {boolean}
 */
export const isChinese = (value: string) => {
  const reg = /^[\u4e00-\u9fa5]+$/
  return reg.test(value)
}

/**
 * @description Determine whether it is English
 * @param value
 * @returns {boolean}
 */
export const isEnglish = (value: string) => {
  const reg = /^[a-zA-Z]+$/
  return reg.test(value)
}
