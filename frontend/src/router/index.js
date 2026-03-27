import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/auth/LoginView.vue'
import OtpVerifyView from '../views/auth/OtpVerifyView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProfileView from '../views/ProfileView.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/auth/login' },
    { path: '/auth/login', name: 'login', component: LoginView, meta: { guest: true } },
    { path: '/auth/verify', name: 'verify', component: OtpVerifyView, meta: { guest: true } },
    { path: '/app/home', name: 'home', component: DashboardView, meta: { auth: true } },
    { path: '/app/profile', name: 'profile', component: ProfileView, meta: { auth: true } }
  ]
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  if (to.meta.auth && !authStore.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    return authStore.isProfileCompleted ? { name: 'home' } : { name: 'profile' }
  }

  if (to.meta.auth && authStore.isAuthenticated && !authStore.user) {
    try {
      await authStore.fetchMe()
    } catch {
      return { name: 'login' }
    }
  }

  if (
    to.meta.auth &&
    authStore.isAuthenticated &&
    authStore.user &&
    !authStore.isProfileCompleted &&
    to.name !== 'profile'
  ) {
    return { name: 'profile' }
  }

  return true
})

export default router
