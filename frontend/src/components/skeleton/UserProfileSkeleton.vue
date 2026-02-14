<template>
  <div class="animate-pulse space-y-6">
    <div class="flex items-center gap-6 mb-8 pb-8 border-b">
      <div class="w-20 h-20 rounded-full bg-gray-300" />
      <div class="flex flex-col gap-2">
        <div class="h-6 w-32 bg-gray-300 rounded" />
        <div class="h-4 w-48 bg-gray-200 rounded" />
      </div>
    </div>
    <div class="h-4 w-24 bg-gray-200 rounded mb-2" />
    <div class="h-6 w-40 bg-gray-300 rounded mb-6" />
    <div class="h-4 w-24 bg-gray-200 rounded mb-2" />
    <div class="h-6 w-40 bg-gray-300 rounded mb-6" />
    <div class="h-4 w-24 bg-gray-200 rounded mb-2" />
    <div class="h-6 w-40 bg-gray-300 rounded mb-6" />
    <div class="h-4 w-24 bg-gray-200 rounded mb-2" />
    <div class="h-6 w-40 bg-gray-300 rounded mb-6" />
    <div class="mt-12 pt-8 border-t flex gap-4">
      <div class="h-10 w-32 bg-gray-300 rounded" />
      <div class="h-10 w-32 bg-gray-200 rounded" />
    </div>
  </div>
</template>

<script setup lang="ts">
// No script needed for skeleton
</script>

<style scoped>
.animate-pulse {
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.4; }
  100% { opacity: 1; }
}
</style>
