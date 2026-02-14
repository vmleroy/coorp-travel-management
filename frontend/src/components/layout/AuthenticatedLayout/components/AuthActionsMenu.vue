<script setup lang="ts">
import { useLogout } from '@/composables/useAuthQueries'
import { useRouter } from 'vue-router'
import { ref } from 'vue'
import { Button } from '@/components/button'
import Menu from 'primevue/menu'

const menuRef = ref()
const router = useRouter()
const logoutMutation = useLogout()

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
      :model="[
        {
          label: 'Perfil',
          icon: 'pi pi-user',
          command: () => router.push({ name: 'user-profile' }),
        },
        { label: 'Sair', icon: 'pi pi-sign-out', command: handleLogout },
      ]"
      :popup="true"
      :autoFocus="true"
      panelClass="menu-popup-gap"
    />
  </div>
</template>
