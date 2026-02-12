<script setup lang="ts">
import Button from 'primevue/button'
import Card from 'primevue/card'
import { TravelHubIcon } from '@/components/icons'
import ThemeToggle from '@/components/ThemeToggle.vue'

const emit = defineEmits<{
  openLogin: []
}>()

const handleLogin = () => {
  emit('openLogin')
}
</script>

<template>
  <header class="relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgb(99 102 241) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <!-- Theme Toggle -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-end">
      <ThemeToggle />
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
      <div class="text-center">
        <!-- Logo/Brand -->
        <div class="flex justify-center mb-8">
          <div class="inline-flex items-center gap-3 px-6 py-3 bg-white rounded-2xl shadow-lg">
            <TravelHubIcon :size="32" color="#4F46E5" />
            <span class="text-2xl font-bold text-gray-900">TravelHub</span>
          </div>
        </div>

        <!-- Heading -->
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-gray-900 tracking-tight mb-6">
          Gerencie suas
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
            viagens corporativas
          </span>
          <br />
          de forma simples
        </h1>

        <!-- Subheading -->
        <p class="max-w-2xl mx-auto text-xl text-gray-600 mb-12">
          Sistema completo para solicitação, aprovação e acompanhamento de viagens corporativas.
          Notificações em tempo real e controle total.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <Button
            @click="handleLogin"
            label="Fazer Login"
            size="large"
            severity="primary"
            class="w-full sm:w-auto"
          />
          <Button
            label="Saber Mais"
            size="large"
            severity="secondary"
            outlined
            class="w-full sm:w-auto"
          />
        </div>

        <!-- Stats -->
        <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-3xl mx-auto">
          <Card class="text-center">
            <template #content>
              <div class="text-3xl font-bold text-indigo-600 mb-2">100%</div>
              <div class="text-gray-600">Digital</div>
            </template>
          </Card>
          <Card class="text-center">
            <template #content>
              <div class="text-3xl font-bold text-indigo-600 mb-2">Real-time</div>
              <div class="text-gray-600">Notificações</div>
            </template>
          </Card>
          <Card class="text-center">
            <template #content>
              <div class="text-3xl font-bold text-indigo-600 mb-2">24/7</div>
              <div class="text-gray-600">Disponível</div>
            </template>
          </Card>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped></style>
