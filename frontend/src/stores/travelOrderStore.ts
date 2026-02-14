import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { TravelOrder, TravelOrderFilters } from '@/api/travelOrder'
import * as travelOrderApi from '@/api/travelOrder'
import { subscribeToOrderUpdates, subscribeToAllOrderUpdates } from '@/utils/echo'
import { useAuthStore } from '@/stores/authStore'
import { useNotificationStore } from '@/stores/notificationStore'
import { statusLabel } from '@/utils/formatters'

export const useTravelOrderStore = defineStore('travelOrder', () => {
  const orders = ref<TravelOrder[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const authStore = useAuthStore()
  const notificationStore = useNotificationStore()

  async function fetchUserOrders(filters?: TravelOrderFilters) {
    loading.value = true
    error.value = null
    try {
      const data = await travelOrderApi.getUserTravelOrders(filters)
      orders.value = data
    } catch (e) {
      error.value = 'Erro ao buscar solicitações.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function fetchAllOrders(filters?: TravelOrderFilters) {
    loading.value = true
    error.value = null
    try {
      const data = await travelOrderApi.getAllTravelOrders(filters)
      orders.value = data
    } catch (e) {
      error.value = 'Erro ao buscar todas solicitações.'
      throw e
    } finally {
      loading.value = false
    }
  }

  function listenToUserOrderUpdates() {
    if (!authStore.user?.id) {
      return
    }

    subscribeToOrderUpdates(Number(authStore.user.id), async (eventData) => {
      await notificationStore.loadNotifications()
      await notificationStore.updateNotifications(
        `Sua solicitação para ${eventData.destination} foi ${statusLabel(eventData.status).toLowerCase()}.`,
      )
      fetchUserOrders()
    })
  }

  function listenToAllOrderUpdates() {
    const notificationStore = useNotificationStore()
    subscribeToAllOrderUpdates(async (eventData) => {
      await notificationStore.loadNotifications()
      await notificationStore.updateNotifications(
        `Solicitação de ${eventData.user.name} foi criada.`,
      )
      fetchAllOrders()
    })
  }

  return {
    orders,
    loading,
    error,
    fetchUserOrders,
    fetchAllOrders,
    listenToUserOrderUpdates,
    listenToAllOrderUpdates,
  }
})
