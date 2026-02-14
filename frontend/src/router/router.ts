import { createMemoryHistory, createRouter } from 'vue-router'
import { setupAuthGuard } from './guards'

import { LandingPage } from '@/pages/Landing'
import { UserProfile } from '@/pages/UserProfile'
import { DashboardPage } from '@/pages/Dashboard'
import { UserManagementPage } from '@/pages/UserManagement'

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
  {
    path: '/users',
    name: 'user-management',
    component: UserManagementPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
]

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

// Setup auth guard
setupAuthGuard(router)
