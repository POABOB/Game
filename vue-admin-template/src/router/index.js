import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * Note: sub-menu only appear when route children.length >= 1
 * Detail see: https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['admin','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * constantRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },

  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  },

  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [{
      path: 'dashboard',
      name: 'Dashboard',
      component: () => import('@/views/dashboard/index'),
      meta: { title: 'Dashboard', icon: 'dashboard' }
    }]
  },

  {
    path: '/home',
    component: Layout,
    redirect: '/home',
    children: [{
      path: 'home',
      name: '首頁',
      component: () => import('@/views/home/index'),
      meta: { title: '首頁', icon: 'home' }
    }]
  },

  {
    path: '/projects',
    component: Layout,
    redirect: 'noRedirect',
    name: '空間設計',
    meta: { title: '空間設計', icon: 'eye-open' },
    children: [
      {
        path: '/projects/type',
        name: '專案類型',
        component: () => import('@/views/projects/type'),
        meta: { title: '專案類型' }
      },

      {
        path: '/projects/index',
        name: '專案管理',
        component: () => import('@/views/projects/index'),
        meta: { title: '專案管理' }
      }

    ]
  },

  {
    path: '/aboutus',
    component: Layout,
    redirect: 'noRedirect',
    name: '關於我們',
    meta: { title: '關於我們', icon: 'user' },
    children: [
      {
        path: '/aboutus/introduction',
        name: '關於介紹',
        component: () => import('@/views/aboutus/introduction'),
        meta: { title: '關於介紹' }
      },

      {
        path: '/aboutus/members',
        name: '團隊介紹',
        component: () => import('@/views/aboutus/members'),
        meta: { title: '團隊介紹' }
      }

    ]
  },

  {
    path: '/service',
    component: Layout,
    redirect: '/service',
    children: [{
      path: 'service',
      name: '服務流程',
      component: () => import('@/views/service/index'),
      meta: { title: '服務流程', icon: 'table' }
    }]
  },

  {
    path: '/contactus',
    component: Layout,
    redirect: '/contactus',
    children: [{
      path: 'contactus',
      name: '聯絡我們',
      component: () => import('@/views/contactus/index'),
      meta: { title: '聯絡我們', icon: 'example' }
    }]
  },

  // 404 page must be placed at the end !!!
  { path: '*', redirect: '/404', hidden: true }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRoutes
})

const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
