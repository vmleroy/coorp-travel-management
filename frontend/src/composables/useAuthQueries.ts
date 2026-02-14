import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { authAPI, type LoginRequest, type RegisterRequest } from '@/api/auth'
import { useAuthStore } from '@/stores/authStore'

export const useLogin = () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: LoginRequest) => {
      return authAPI.login(data)
    },
    onSuccess: (data) => {
      const user = data?.data?.user
      const token = data?.data?.token
      authStore.setUser(user, token)
      queryClient.invalidateQueries({ queryKey: ['user'] })
    },
  })
}

export const useRegister = () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: RegisterRequest) => {
      return authAPI.register(data)
    },
    onSuccess: (data) => {
      const user = data?.data?.user
      const token = data?.data?.token
      authStore.setUser(user, token)
      queryClient.invalidateQueries({ queryKey: ['user'] })
    },
  })
}

export const useLogout = () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async () => {
      return authAPI.logout()
    },
    onSuccess: () => {
      authStore.logout()
      queryClient.clear()
    },
  })
}

export const useCurrentUser = () => {
  const authStore = useAuthStore()

  return useQuery({
    queryKey: ['user'],
    queryFn: () => authAPI.getCurrentUser(),
    enabled: !!authStore.token,
    staleTime: 5 * 60 * 1000, // 5 minutos
    gcTime: 10 * 60 * 1000, // 10 minutos
  })
}

export const useRefreshToken = () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async () => {
      return authAPI.refreshToken()
    },
    onSuccess: (data) => {
      authStore.setUser(data.data.user, data.data.token)
      queryClient.invalidateQueries({ queryKey: ['user'] })
    },
  })
}
