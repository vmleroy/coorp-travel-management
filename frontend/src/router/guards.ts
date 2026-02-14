import type { Router } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

export const setupAuthGuard = (router: Router) => {
  router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()

    // Carregar autenticação do localStorage
    authStore.loadFromStorage()

    const isAuthenticated = authStore.isAuthenticated
    const requiresAuth = to.meta.requiresAuth as boolean | undefined
    const requiresAdmin = to.meta.requiresAdmin as boolean | undefined

    const isAuthPage = ['login', 'register'].includes(to.name as string)
    const isLandingPage = to.name === 'landing'

    const redirectPagesIfAuthenticated = isAuthPage || isLandingPage

    // Se a rota requer autenticação e não está autenticado
    if (requiresAuth && !isAuthenticated) {
      return next({ name: 'landing', query: { redirect: to.fullPath } })
    }

    // Se a rota requer admin e não é admin
    if (requiresAdmin && !authStore.isAdmin) {
      return next({ name: 'landing' })
    }

    // Se está autenticado e tenta acessar pagina de login ou landing, redirecionar para dashboard
    if (isAuthenticated && redirectPagesIfAuthenticated) {
      return next({ name: 'dashboard' })
    }

    next()
  })
}
