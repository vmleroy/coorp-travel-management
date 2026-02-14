<script setup lang="ts">
import AuthHeader from './AuthHeader.vue'
</script>

<template>
  <div class="min-h-screen w-full" style="background-color: var(--color-background)">
    <AuthHeader>
      <template #actions>
        <slot name="header-actions" />
      </template>
    </AuthHeader>
    <main>
      <slot />
    </main>
  </div>
</template>
