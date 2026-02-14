import { useAuthStore } from '@/stores/authStore'
import { useCurrentUser } from '@/composables/useAuthQueries'
import { onMounted } from 'vue'

export const useAuthGuard = () => {
  const authStore = useAuthStore()
  const { data: currentUser, isLoading: isLoadingUser } = useCurrentUser()

  onMounted(() => {
    authStore.loadFromStorage()
  })

  return {
    isAuthenticated: authStore.isAuthenticated,
    user: currentUser,
    isLoadingUser,
  }
}
