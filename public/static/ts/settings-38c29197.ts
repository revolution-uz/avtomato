/**
 * @description Status management of all global configurations, please do not modify them unless necessary
 */
import { round } from 'lodash-es'
import {
  isCatchedTabs as _isCatchedTabs,
  color,
  columnStyle,
  fixedHeader,
  foldSidebar,
  i18n,
  isFollow,
  layout,
  logo,
  menuWidth,
  pageTransition,
  radius,
  showColorPicker,
  showDark,
  showFooter,
  showFullScreen,
  showLanguage,
  showLock,
  showNotice,
  showProgressBar,
  showRefresh,
  showSearch,
  showTabs,
  showTabsIcon,
  showTheme,
  showThemeSetting,
  tabsBarStyle,
  themeName,
  title,
} from '/@/config'
import { lightenColor } from '/@/utils/lightenColor'
import { isJson } from '/@/utils/validate'

const defaultTheme: ThemeType = {
  color,
  columnStyle,
  fixedHeader,
  foldSidebar,
  isFollow,
  layout,
  menuWidth,
  pageTransition,
  radius,
  showColorPicker,
  showDark,
  showFooter,
  showFullScreen,
  showLanguage,
  showLock,
  showNotice,
  showProgressBar,
  showRefresh,
  showSearch,
  showTabs,
  showTabsIcon,
  showTheme,
  showThemeSetting,
  tabsBarStyle,
  themeName,
}

const getLocalStorage = (key: string) => {
  const value: any = localStorage.getItem(key)
  if (value && isJson(value)) {
    return JSON.parse(value)
  } else {
    return false
  }
}

const getRgbNum = (sColor: string) => {
  if (sColor.length === 4) {
    let sColorNew = '#'
    for (let i = 1; i < 4; i += 1) {
      sColorNew += sColor.slice(i, i + 1).concat(sColor.slice(i, i + 1))
    }
    sColor = sColorNew
  }
  const sColorChange = []
  for (let i = 1; i < 7; i += 2) {
    sColorChange.push(parseInt(`0x${sColor.slice(i, i + 2)}`))
  }
  return sColorChange
}

const colorRgba = (str: any, n = 1) => {
  const reg = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/
  const sColor = str.toLowerCase()
  if (sColor && reg.test(sColor)) return `rgba(${getRgbNum(sColor).join(',')},${round(n, 1)})`
  else return sColor
}

const { collapse = foldSidebar } = getLocalStorage('collapse')
const { isCatchedTabs = _isCatchedTabs } = getLocalStorage('isCatchedTabs')

export const useSettingsStore = defineStore('settings', {
  state: (): SettingsModuleType => ({
    collapse,
    color: getLocalStorage('color').color || color,
    device: 'desktop',
    isCatchedTabs,
    language: getLocalStorage('language').language || i18n,
    lock: getLocalStorage('lock').lock || false,
    logo: getLocalStorage('logo').logo || logo,
    mode: localStorage.getItem('vueuse-color-scheme') || 'light',
    theme: { ...defaultTheme, ...getLocalStorage('shop-vite-theme') } || {
      ...defaultTheme,
    },
    title: getLocalStorage('title').title || title,
  }),
  getters: {
    getCollapse: (state) => state.collapse,
    getColor: (state) => state.color,
    getDevice: (state) => state.device,
    getIsCatchedTabs: (state) => state.isCatchedTabs,
    getLanguage: (state) => state.language,
    getLock: (state) => state.lock,
    getLogo: (state) => state.logo,
    getMode: (state) => state.mode,
    getTheme: (state) => state.theme,
    getTitle: (state) => state.title,
  },
  actions: {
    updateState(obj: any) {
      Object.getOwnPropertyNames(obj).forEach((key) => {
        // @ts-ignore
        this[key] = obj[key]
        localStorage.setItem(key, typeof obj[key] == 'string' ? `{"${key}":"${obj[key]}"}` : `{"${key}":${obj[key]}}`)
      })
    },
    updateMode(value: any) {
      this.mode = value
    },
    saveTheme() {
      localStorage.setItem('shop-vite-theme', JSON.stringify(this.theme))
    },
    resetTheme() {
      this.theme = { ...defaultTheme }
      if (this.device === 'mobile') this.theme = { ...defaultTheme, ...{ layout: 'vertical' } }
      localStorage.removeItem('shop-vite-theme')
      this.updateTheme()
    },
    updateTheme() {
      document.getElementsByTagName('body')[0].className = `vab-theme-${this.theme.themeName}`

      if (this.theme.themeName !== 'default') {
        document.getElementsByTagName('html')[0].className = ''
        localStorage.setItem('vueuse-color-scheme', 'light')
        this.mode = 'light'
      } else {
        const colorScheme = localStorage.getItem('vueuse-color-scheme')
        const htmlElement = document.getElementsByTagName('html')[0]
        htmlElement.className += ` ${colorScheme}`
        this.mode = colorScheme as string
      }

      this.setCssVar()
    },
    setCssVar() {
      const el = ref<any>(null)

      if (this.theme.menuWidth && this.theme.menuWidth.endsWith('px')) useCssVar('--el-left-menu-width', el).value = this.theme.menuWidth
      else useCssVar('--el-left-menu-width', el).value = '266px'

      if (!this.theme.showTabs) useCssVar('--el-tabs-height', el).value = '0px'
      else useCssVar('--el-tabs-height', el).value = '50px'

      if (!this.theme.showFooter) useCssVar('--el-footer-height', el).value = '-20px'
      else useCssVar('--el-footer-height', el).value = '50px'

      if (!this.theme.radius) useCssVar('--el-border-radius-base', el).value = '5px'
      else useCssVar('--el-border-radius-base', el).value = `${this.theme.radius}px`

      if (!this.theme.isFollow) useCssVar('--el-menu-background-color', el).value = '#282c34'
      else useCssVar('--el-menu-background-color', el).value = lightenColor(this.color, 18)
    },
    toggleCollapse() {
      this.collapse = !this.collapse
      localStorage.setItem('collapse', `{"collapse":${this.collapse}}`)
    },
    toggleDevice(device: string) {
      this.updateState({ device })
    },
    openSideBar() {
      this.updateState({ collapse: false })
    },
    foldSideBar() {
      this.updateState({ collapse: true })
    },
    changeLanguage(language: string) {
      this.updateState({ language })
    },
    handleLock() {
      this.updateState({ lock: true })
    },
    handleUnLock() {
      this.updateState({ lock: false })
    },
    updateCatchedTabs(value: any) {
      this.updateState({ isCatchedTabs: value })
      if (!value) localStorage.removeItem('catchedRoutes')
    },
    changeLogo(logo: string) {
      this.updateState({ logo })
    },
    changeTitle(title: string) {
      this.updateState({ title })
    },
    changeColor() {
      this.setCssVar()
      const el = ref<any>(null)
      useCssVar('--el-color-primary-dark-2', el).value = this.color
      useCssVar('--el-color-primary', el).value = this.color
      for (let index = 1; index < 10; index++) {
        useCssVar(`--el-color-primary-light-${index}`, el).value = colorRgba(this.color, 1 - index * 0.1)
      }
      this.updateState({ color: this.color })
    },
  },
})
