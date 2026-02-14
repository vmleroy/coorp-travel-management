<script setup lang="ts">
import { useTheme } from '@/composables/useTheme'
import { Button } from '@/components/button'

defineOptions({
  name: 'ThemeToggle',
})

const { isDark, toggleTheme } = useTheme()
</script>

<template>
  <Button
    :icon="isDark ? 'pi-sun' : 'pi-moon'"
    :label="''"
    variant="ghosted"
    class="w-8 h-8"
    aria-label="Alternar tema"
    @click="toggleTheme"
  />
</template>

<style scoped></style>
