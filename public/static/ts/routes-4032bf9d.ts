import { stringify } from 'qs'
import { recordRoute } from '/@/config'
import type { VabRoute, VabRouteRecordRaw } from '/@/router/types'
import { hasPermission } from '/@/utils/permission'
import { isExternal } from '/@/utils/validate'

/**
 * @description The all mode rendering backend returns the route and supports all pages containing views paths.
 * @param asyncRoutes
 * @returns {*}
 */
export function convertRouter(asyncRoutes: VabRouteRecordRaw[]) {
  const routeAllPathToCompMap = import.meta.glob(`../**/*.vue`)
  return asyncRoutes.map((route: VabRouteRecordRaw) => {
    if (route.component)
      if (route.component === 'Layout') route.component = () => import('/@vab/layouts/index.vue')
      else {
        const index = route.component.indexOf('views')
        const path = index > 0 ? route.component.slice(index) : `${route.component}`
        route.component = routeAllPathToCompMap[`../${path}`]
      }
    if (route.children && route.children.length) route.children = convertRouter(route.children)
    if (route.children && route.children.length === 0) delete route.children
    return route
  })
}

/**
 * @description Intercept routing based on roles array
 * @param routes routing
 * @param rolesControl Whether to perform permission control
 * @param baseUrl basic routing
 * @returns {[]}
 */
export function filterRoutes(routes: VabRouteRecordRaw[], rolesControl: boolean, baseUrl = '/') {
  return routes
    .filter((route: VabRouteRecordRaw) => (rolesControl && route.meta && route.meta.guard ? hasPermission(route.meta.guard) : true))
    .map((route: VabRouteRecordRaw) => {
      route = { ...route }
      if (route.path !== '*' && !isExternal(route.path)) {
        if (baseUrl.slice(-1) === '/') route.path = baseUrl + (route.path[0] === '/' ? route.path.slice(1) : route.path)
        else route.path = baseUrl + (route.path[0] === '/' ? route.path : `/${route.path}`)
      }
      if (route.children && route.children.length > 0) {
        route.children = filterRoutes(route.children, rolesControl, route.path)
        if (route.children.length > 0) {
          route.childrenPathList = route.children.flatMap((item: VabRouteRecordRaw) => item.childrenPathList)
          if (!route.redirect) route.redirect = route.children[0].redirect ? route.children[0].redirect : route.children[0].path
        }
      } else route.childrenPathList = [route.path]
      return route
    })
}

/**
 * Get matched based on path
 * @param routes menu routes
 * @param name Route name
 * @returns {*} matched
 */
/**
 * Get matched based on path
 * @param routes menu routes
 * @param path path
 * @returns {*} matched
 */
export function handleMatched(routes: VabRouteRecordRaw[], path: string): VabRouteRecordRaw[] {
  return routes
    .filter((route) => route.childrenPathList.indexOf(path) + 1)
    .flatMap((route) => (route.children ? [route, ...handleMatched(route.children, path)] : [route]))
}

/**
 * Generates a single multi-label element that can be used to add multi-labels synchronously/asynchronously
 * @param tag route page information
 */
export function handleTabs(tag: VabRoute) {
  let parentIcon = null
  if (tag.matched)
    for (let i = tag.matched.length - 2; i >= 0; i--) if (!parentIcon && tag.matched[i].meta.icon) parentIcon = tag.matched[i].meta.icon
  if (!parentIcon) parentIcon = 'menu-line'
  const path = handleActivePath(tag, true)
  if (tag.name && tag.meta && tag.meta.tabHidden !== true) {
    return {
      path: path,
      query: tag.query,
      params: tag.params,
      name: tag.name,
      parentIcon,
      meta: { ...tag.meta },
    }
  }
}

/**
 * Get activation menu based on current route
 * @param route current route
 * @param isTab Is it a label
 * @returns {string|*}
 */
export function handleActivePath(route: VabRoute, isTab = false) {
  const { meta, path } = route
  const rawPath = route.matched ? route.matched[route.matched.length - 1].path : path
  const fullPath = route.query && Object.keys(route.query).length ? `${route.path}?${stringify(route.query)}` : route.path
  if (isTab) return meta.dynamicNewTab ? fullPath : rawPath
  if (meta.activeMenu) return meta.activeMenu
  return fullPath
}

/**
 * Get the Route that currently jumps to the login page
 * @param currentPath Current page address
 */
export function toLoginRoute(currentPath: string) {
  if (recordRoute && currentPath !== '/')
    return {
      path: '/login',
      query: { redirect: currentPath },
      replace: true,
    }
  else return { path: '/login', replace: true }
}

/**
 * Get all Names in the route
 * @param routes Route array
 * @returns {*} Name array
 */
export function getNames(routes: VabRouteRecordRaw[]): string[] {
  return routes.flatMap((route: VabRouteRecordRaw) => {
    const names = []
    if (route.name) names.push(route.name)
    if (route.children) names.push(...getNames(route.children))
    return names
  })
}
