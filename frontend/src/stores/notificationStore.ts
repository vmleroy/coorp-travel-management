import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { TravelOrder } from '@/api/travelOrder'

export interface Notification {
  id: string
  message: string
  timestamp: Date
  read: boolean
  order?: TravelOrder
  type: 'status_changed' | 'order_created'
}

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref<Notification[]>([])

  const unreadCount = computed(() => {
    return notifications.value.filter((n) => !n.read).length
  })

  const recentNotifications = computed(() => {
    return notifications.value.slice(0, 5)
  })

  function addNotification(notification: Omit<Notification, 'id' | 'timestamp' | 'read'>) {
    const newNotification: Notification = {
      ...notification,
      id: `${Date.now()}-${Math.random()}`,
      timestamp: new Date(),
      read: false,
    }
    notifications.value.unshift(newNotification)

    // Manter apenas as 50 notificações mais recentes
    if (notifications.value.length > 50) {
      notifications.value = notifications.value.slice(0, 50)
    }
  }

  function markAsRead(id: string) {
    const notification = notifications.value.find((n) => n.id === id)
    if (notification) {
      notification.read = true
    }
  }

  function markAllAsRead() {
    notifications.value.forEach((n) => {
      n.read = true
    })
  }

  function clearNotifications() {
    notifications.value = []
  }

  return {
    notifications,
    unreadCount,
    recentNotifications,
    addNotification,
    markAsRead,
    markAllAsRead,
    clearNotifications,
  }
})
