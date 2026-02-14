import apiClient from './client'

export interface TravelOrder {
  id: number
  user_id: number
  user?: {
    id: number
    name: string
    email: string
  }
  destination: string
  departure_date: string
  return_date: string
  status: 'pending' | 'approved' | 'rejected' | 'cancelled'
  reason?: string | null
  created_at?: string
  updated_at?: string
}

export interface TravelOrderFilters {
  status?: string
  destination?: string
  user_id?: number
  page?: number
  per_page?: number
  departure_date_from?: string
  departure_date_to?: string
}

export async function getUserTravelOrders(filters?: TravelOrderFilters) {
  try {
    const response = await apiClient.get('/travel-orders/user', { params: filters })
    const orders = response.data.data.travel_orders as TravelOrder[]
    return orders
  } catch (error) {
    throw error
  }
}

export async function getAllTravelOrders(filters?: TravelOrderFilters) {
  try {
    const response = await apiClient.get('/travel-orders', { params: filters })
    const orders = response.data.data.travel_orders as TravelOrder[]
    return orders
  } catch (error) {
    throw error
  }
}

export async function createTravelOrder(payload: Partial<TravelOrder>) {
  const response = await apiClient.post('/travel-orders', payload)
  return response.data.data.travel_order as TravelOrder
}

export async function updateTravelOrder(id: number, payload: Partial<TravelOrder>) {
  const response = await apiClient.put(`/travel-orders/${id}`, payload)
  return response.data.data.travel_order as TravelOrder
}

export async function changeTravelOrderStatus(
  id: number,
  status: 'approved' | 'rejected',
  reason?: string,
) {
  const response = await apiClient.put(`/travel-orders/${id}/change-status`, { status, reason })
  return response.data.data.travel_order as TravelOrder
}

export async function cancelTravelOrder(id: number, reason?: string) {
  const response = await apiClient.put(`/travel-orders/${id}/cancel`, { reason })
  return response.data.data.travel_order as TravelOrder
}

export async function deleteTravelOrder(id: number) {
  const { data } = await apiClient.delete(`/travel-orders/${id}`)
  return data.data.travel_order as TravelOrder
}
