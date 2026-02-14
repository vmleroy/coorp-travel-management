<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
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
    key: notification.id,
    notification,
    template: 'notification',
    class: 'p-0',
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
    key: 'mark-all',
    template: 'markAll',
    class: 'p-0',
  })

  return items
})

async function markAllAsRead() {
  await notificationStore.markAllAsRead()
}

function toggleMenu(event: Event) {
  menuRef.value.toggle(event)
}

onMounted(() => {
  notificationStore.loadNotifications()
})
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
    >
      <template #item="slotProps">
        <template v-if="slotProps.item.template === 'notification'">
          <div
            class="flex flex-col px-3 py-2 cursor-pointer hover:bg-primary-50 transition"
            @click="notificationStore.markAsRead(slotProps.item.notification.id)"
          >
            <div
              class="flex items-center gap-2"
              :class="
                slotProps.item.notification.read
                  ? 'text-gray-400'
                  : 'text-primary-700 font-semibold'
              "
            >
              <i
                :class="
                  slotProps.item.notification.read
                    ? 'pi pi-circle text-xs'
                    : 'pi pi-circle-fill text-primary-500 text-xs'
                "
              ></i>
              <span>{{ slotProps.item.notification.message }}</span>
            </div>
            <span
              v-if="slotProps.item.notification.created_at"
              class="text-xs text-gray-400 mt-0.5"
            >
              {{ formatDate(slotProps.item.notification.created_at) }}
            </span>
          </div>
        </template>
        <template v-else-if="slotProps.item.template === 'markAll'">
          <div
            class="flex items-center gap-2 px-3 py-2 font-semibold cursor-pointer hover:bg-primary-50 transition"
            @click="markAllAsRead"
          >
            <i class="pi pi-check"></i>
            <span>Marcar todas como lidas</span>
          </div>
        </template>
        <template v-else>
          <span v-if="slotProps.item.label">{{ slotProps.item.label }}</span>
        </template>
      </template>
    </Menu>
  </div>
</template>

<style scoped>
:deep(.p-menu-item-content) {
  white-space: normal !important;
  word-wrap: break-word !important;
}
</style>
