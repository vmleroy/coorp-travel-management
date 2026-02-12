<script setup lang="ts">
import { useTheme } from '@/composables/useTheme'
import Button from 'primevue/button'

defineOptions({
  name: 'ThemeToggle',
})

const { isDark, toggleTheme } = useTheme()
</script>

<template>
  <Button
    :icon="isDark ? 'pi pi-sun' : 'pi pi-moon'"
    rounded
    text
    severity="secondary"
    aria-label="Alternar tema"
    @click="toggleTheme"
  />
</template>

<style scoped></style>
