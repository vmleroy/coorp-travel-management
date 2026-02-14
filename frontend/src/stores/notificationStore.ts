import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import {
  fetchNotifications,
  markNotificationsAsRead,
  markAllNotificationsAsRead,
  addMessageToNotification,
} from '@/api/notification'

export interface Notification {
  id: string
  message: string
  read: boolean
  created_at: string
  type: string
  // outros campos opcionais
}

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref<Notification[]>([])

  const unreadCount = computed(() => {
    return notifications.value.filter((n) => !n.read).length
  })

  const recentNotifications = computed(() => {
    return notifications.value.filter((n) => !n.read).slice(0, 5)
  })

  async function loadNotifications() {
    const apiNotifications = await fetchNotifications()
    notifications.value = apiNotifications
  }

  async function updateNotifications(message: string) {
    const notification = notifications.value[0]
    if (notification) {
      notification.message = message
      await addMessageToNotification(notification.id, message)
    }
  }

  async function markAsRead(id: string) {
    const notification = notifications.value.find((n) => n.id === id)
    if (notification && !notification.read) {
      try {
        await markNotificationsAsRead(id)
      } catch {
      } finally {
        notification.read = true
      }
    }
  }

  async function markAllAsRead() {
    try {
      await markAllNotificationsAsRead()
    } catch {
    } finally {
      clearNotifications()
    }
  }

  function clearNotifications() {
    notifications.value = []
  }

  return {
    notifications,
    unreadCount,
    recentNotifications,
    markAsRead,
    markAllAsRead,
    clearNotifications,
    loadNotifications,
    updateNotifications,
  }
})
