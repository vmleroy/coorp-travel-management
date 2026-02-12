<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useTheme } from '@/composables/useTheme'
import { onMounted } from 'vue'

const { applyTheme, theme } = useTheme()

onMounted(() => {
  applyTheme(theme.value)
})
</script>


<template>
  <div class="min-h-screen w-full">
    <RouterView />
  </div>
</template>

<style scoped></style>
