import { createMemoryHistory, createRouter } from 'vue-router'
import { setupAuthGuard } from './guards'

import { LandingPage } from '@/pages/Landing'
import { UserProfile } from '@/pages/UserProfile'
import { DashboardPage } from '@/pages/Dashboard'

const routes = [
  {
    path: '/',
    name: 'landing',
    component: LandingPage,
  },
  {
    path: '/user',
    name: 'user-profile',
    component: UserProfile,
    meta: { requiresAuth: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardPage,
    meta: { requiresAuth: true },
  },
]

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

// Setup auth guard
setupAuthGuard(router)
