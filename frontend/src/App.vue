<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useTheme } from '@/composables/useTheme'

// Inicializa o sistema de tema
useTheme()
</script>

<template>
  <div class="min-h-screen w-full">
    <RouterView />
  </div>
</template>
