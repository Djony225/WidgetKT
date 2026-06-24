import { createRouter, createWebHistory } from 'vue-router'
import AcceuilView from '../views/AcceuilView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'acceuil',
      component: AcceuilView,
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue'),
    },
    {
      path: '/dashbord',
      name: 'dashbord',
      component: () => import('../views/DashbordUser.vue'),
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/DashbordAdmin.vue'),
    },
  ],
})

export default router
