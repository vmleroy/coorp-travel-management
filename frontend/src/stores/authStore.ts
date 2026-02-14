import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { cookieUtils } from '@/utils/cookieUtils'

export interface User {
  id: string
  name: string
  email: string
  role: 'admin' | 'manager' | 'user'
  createdAt: string
}

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  // Carregar estado do localStorage na inicialização
  const loadFromStorage = () => {
    // Tentar carregar do cookie primeiro (mais seguro)
    const cookieToken = cookieUtils.get('auth_token')
    if (cookieToken) {
      token.value = cookieToken
    }

    const savedUser = localStorage.getItem('auth_user')
    if (savedUser) {
      try {
        user.value = JSON.parse(savedUser)
      } catch {
        localStorage.removeItem('auth_user')
      }
    }
  }

  // Salvar estado no localStorage e cookies
  const saveToStorage = () => {
    if (token.value) {
      try {
        cookieUtils.set('auth_token', token.value, {
          maxAge: 7 * 24 * 60 * 60,
          path: '/',
          sameSite: 'Lax',
          secure: false,
        })
      } catch (e) {
        throw new Error('Failed to save JWT in cookie')
      }
    }

    if (user.value) {
      try {
        localStorage.setItem('auth_user', JSON.stringify(user.value))
      } catch (e) {
        throw new Error('Failed to save user data in localStorage')
      }
    }
    // Debug cookies
  }

  // Definir usuário após login/registro
  const setUser = (userData: User, authToken: string) => {
    // setUser called
    user.value = userData
    token.value = authToken
    saveToStorage()
  }

  // Limpar autenticação
  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    localStorage.removeItem('auth_user')
    cookieUtils.remove('auth_token')
  }

  // Atualizar dados do usuário
  const updateUser = (userData: Partial<User>) => {
    if (user.value) {
      user.value = { ...user.value, ...userData }
      saveToStorage()
    }
  }

  // Verificar se é administrador
  const isAdmin = computed(() => user.value?.role === 'admin')

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    loadFromStorage,
    setUser,
    logout,
    updateUser,
  }
})
