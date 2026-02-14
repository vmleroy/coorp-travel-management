<script setup lang="ts">
import { useLogout } from '@/composables/useAuthQueries'
import { useRouter } from 'vue-router'
import { ref, computed } from 'vue'
import { Button } from '@/components/button'
import { useAuthStore } from '@/stores/authStore'
import Menu from 'primevue/menu'

const menuRef = ref()
const router = useRouter()
const authStore = useAuthStore()
const logoutMutation = useLogout()

const menuItems = computed(() => {
  const items = [
    {
      label: 'Perfil',
      icon: 'pi pi-user',
      command: () => router.push({ name: 'user-profile' }),
    },
  ]

  if (authStore.isAdmin) {
    items.push({
      label: 'Gestão de Usuários',
      icon: 'pi pi-users',
      command: () => router.push({ name: 'user-management' }),
    })
  }

  items.push({
    label: 'Sair',
    icon: 'pi pi-sign-out',
    command: handleLogout,
  })

  return items
})

const handleLogout = async () => {
  await logoutMutation.mutateAsync()
  await router.push({ name: 'landing' })
}
</script>

<template>
  <div class="w-fit relative flex items-center gap-4">
    <Button
      ref="buttonRef"
      :icon="'pi-bars'"
      :label="''"
      size="sm"
      variant="ghosted"
      class="w-8 h-8"
      tabindex="0"
      @click="menuRef.toggle($event)"
      aria-label="Abrir menu de ações"
    />
    <Menu
      ref="menuRef"
      :model="menuItems"
      :popup="true"
      :autoFocus="true"
      panelClass="menu-popup-gap"
    />
  </div>
</template>
