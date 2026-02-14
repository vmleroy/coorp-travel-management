<script setup lang="ts">
import { TravelHubIcon } from '@/components/icons'
import AuthActionsMenu from './components/AuthActionsMenu.vue'
import NotificationMenu from './components/NotificationMenu.vue'
import { ThemeToggle } from '@/components/theme-toggle'
import { router } from '@/router/router'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

const handleOnClickLogo = () => {
  router.push('/dashboard') // Redireciona para a página inicial
}
</script>

<template>
  <header class="border-b py-6 px-6" style="border-color: var(--color-border)">
    <div class="w-full flex items-center justify-between">
      <div class="flex items-center cursor-pointer hover:opacity-80" @click="handleOnClickLogo">
        <TravelHubIcon :size="32" class="stroke-brand-icon" />
        <span class="text-2xl font-bold" style="color: var(--color-foreground)"
          >TravelHub - {{ authStore.user?.role === 'admin' ? 'Administrador' : 'Usuário' }}</span
        >
      </div>
      <div class="flex flex-row items-center justify-center w-fit gap-2">
        <ThemeToggle />
        <NotificationMenu />
        <AuthActionsMenu />
      </div>
    </div>
  </header>
</template>
