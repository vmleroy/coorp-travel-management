<script setup lang="ts">
import { ref, computed } from 'vue'
import { useNotificationStore } from '@/stores/notificationStore'
import { Button } from '@/components/button'
import Menu from 'primevue/menu'
import Badge from 'primevue/badge'
import { formatDate } from '@/utils/formatters'
import type { MenuItem } from 'primevue/menuitem'

const notificationStore = useNotificationStore()
const menuRef = ref()

const hasUnread = computed(() => notificationStore.unreadCount > 0)

const notificationItems = computed((): MenuItem[] => {
  const items: MenuItem[] = notificationStore.recentNotifications.map((notification) => ({
    label: notification.message,
    icon: notification.read ? 'pi pi-circle' : 'pi pi-circle-fill',
    class: notification.read ? 'text-gray-400' : 'text-primary-500',
    command: () => {
      notificationStore.markAsRead(notification.id)
    },
    items: [
      {
        label: formatDate(notification.timestamp.toISOString()),
        disabled: true,
        class: 'text-xs text-gray-400',
      },
    ],
  }))

  if (items.length === 0) {
    return [
      {
        label: 'Nenhuma notificação',
        disabled: true,
        class: 'text-gray-400',
      },
    ]
  }
  items.push({
    separator: true,
  })

  items.push({
    label: 'Marcar todas como lidas',
    icon: 'pi pi-check',
    command: () => notificationStore.markAllAsRead(),
    class: '',
    items: []
  })

  return items
})

function toggleMenu(event: Event) {
  menuRef.value.toggle(event)
}
</script>

<template>
  <div class="relative">
    <div class="relative inline-block">
      <Button
        :icon="'pi-bell'"
        :label="''"
        size="sm"
        variant="ghosted"
        class="w-8 h-8"
        @click="toggleMenu"
        aria-label="Notificações"
      />
      <Badge
        v-if="hasUnread"
        :value="notificationStore.unreadCount.toString()"
        severity="danger"
        class="absolute -top-1 -right-1 min-w-5 h-5"
      />
    </div>
    <Menu
      ref="menuRef"
      :model="notificationItems"
      :popup="true"
      :autoFocus="true"
      panelClass="menu-popup-gap w-80"
    />
  </div>
</template>

<style scoped>
:deep(.p-menu-item-content) {
  white-space: normal !important;
  word-wrap: break-word !important;
}
</style>
