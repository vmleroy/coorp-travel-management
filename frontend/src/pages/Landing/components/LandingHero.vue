<script setup lang="ts">
import AppButton from '@/components/AppButton.vue'
import { TravelHubIcon } from '@/components/icons'

const emit = defineEmits<{
  openLogin: []
}>()

const handleLogin = () => {
  emit('openLogin')
}
</script>

<template>
  <header class="min-h-screen flex flex-col items-center justify-center text-center">
    <!-- Logo/Brand -->
    <div class="flex justify-center mb-16">
      <div class="flex items-center gap-3">
        <TravelHubIcon :size="48" class="stroke-brand-icon" />
        <span class="text-4xl font-bold" style="color: var(--color-foreground)">TravelHub</span>
      </div>
    </div>

    <!-- Heading -->
    <h1
      class="text-5xl sm:text-6xl md:text-7xl font-bold tracking-tight mb-8 leading-tight text-center max-w-4xl mx-auto"
      style="color: var(--color-foreground)"
    >
      Gerencie viagens corporativas de forma simples
    </h1>

    <!-- Subheading -->
    <p
      class="max-w-2xl mx-auto text-xl sm:text-2xl mb-12 leading-relaxed text-center"
      style="color: var(--color-foreground-muted)"
    >
      Solicitação, aprovação e acompanhamento com notificações em tempo real
    </p>

    <!-- CTA Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
      <AppButton
        label="Começar agora"
        size="lg"
        color="indigo"
        @click="handleLogin"
      />
    </div>

    <!-- Stats -->
    <div class="mt-20 flex flex-wrap justify-center gap-12 max-w-3xl mx-auto">
      <div class="text-center">
        <div class="text-4xl font-bold text-indigo-600 mb-2">100%</div>
        <div class="text-base" style="color: var(--color-foreground-muted)">Digital</div>
      </div>
      <div class="text-center">
        <div class="text-4xl font-bold text-indigo-600 mb-2">Real-time</div>
        <div class="text-base" style="color: var(--color-foreground-muted)">Notificações</div>
      </div>
      <div class="text-center">
        <div class="text-4xl font-bold text-indigo-600 mb-2">24/7</div>
        <div class="text-base" style="color: var(--color-foreground-muted)">Disponível</div>
      </div>
    </div>
  </header>
</template>
