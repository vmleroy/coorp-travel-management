import apiClient from './client'

export interface Notification {
  id: string
  user_id: number | null
  type: string
  message: string
  data: Record<string, unknown> | null
  read: boolean
  created_at: string
  updated_at: string
}

export async function fetchNotifications(): Promise<Notification[]> {
  const { data } = await apiClient.get<{ data: Notification[] }>('/notifications')
  return data.data
}

export async function markNotificationsAsRead(id: string): Promise<void> {
  await apiClient.put(`/notifications/${encodeURIComponent(id)}/read`)
}

export async function markAllNotificationsAsRead(): Promise<void> {
  await apiClient.put('/notifications/read-all')
}

export async function addMessageToNotification(id: string, message: string): Promise<void> {
  await apiClient.put(`/notifications/${encodeURIComponent(id)}/update`, { message })
}
