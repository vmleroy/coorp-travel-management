<script setup lang="ts">
defineProps({
  htmlFor: { type: String, default: '' },
  text: { type: String, required: true },
})
</script>
<template>
  <label :for="htmlFor" class="font-semibold">{{ text }}</label>
</template>
