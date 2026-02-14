import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { TravelOrder, TravelOrderFilters } from '@/api/travelOrder'
import * as travelOrderApi from '@/api/travelOrder'

export const useTravelOrderStore = defineStore('travelOrder', () => {
  const orders = ref<TravelOrder[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchUserOrders(filters?: TravelOrderFilters) {
    loading.value = true
    error.value = null
    try {
      const data = await travelOrderApi.getUserTravelOrders(filters)
      console.log('✅ UserOrders fetched:', data)
      orders.value = data
    } catch (e) {
      console.error('❌ Error fetching user orders:', e)
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
      console.log('✅ AllOrders fetched:', data)
      orders.value = data
    } catch (e) {
      console.error('❌ Error fetching all orders:', e)
      error.value = 'Erro ao buscar todas solicitações.'
      throw e
    } finally {
      loading.value = false
    }
  }

  return {
    orders,
    loading,
    error,
    fetchUserOrders,
    fetchAllOrders,
  }
})
