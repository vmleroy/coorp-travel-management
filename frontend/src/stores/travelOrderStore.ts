import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { TravelOrder, TravelOrderFilters } from '@/api/travelOrder'
import * as travelOrderApi from '@/api/travelOrder'
import { subscribeToOrderUpdates, subscribeToAllOrderUpdates } from '@/utils/echo'
import { useAuthStore } from '@/stores/authStore'

export const useTravelOrderStore = defineStore('travelOrder', () => {
  const orders = ref<TravelOrder[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

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
    console.log('Fetching all orders with filters:', filters)
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
    const authStore = useAuthStore()
    if (!authStore.user?.id) {
      return
    }

    subscribeToOrderUpdates(Number(authStore.user.id), () => {
      fetchUserOrders()
    })
  }

  function listenToAllOrderUpdates() {
    console.log('Subscribing to all order updates...')
    subscribeToAllOrderUpdates(() => {
      console.log('Atualização de pedido recebida, atualizando lista...')
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
