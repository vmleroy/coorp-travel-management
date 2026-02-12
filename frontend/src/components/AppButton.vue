<script setup lang="ts">
const props = defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: undefined },
  color: { type: String, default: 'indigo' }, // indigo, gray, green, etc.
  size: { type: String, default: 'md' }, // sm, md, lg
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
})

const emit = defineEmits(['click'])

const colorMap: Record<string, string> = {
  indigo: 'bg-indigo-600 hover:bg-indigo-800 text-white',
  gray: 'bg-gray-200 hover:bg-gray-400 text-gray-900',
  green: 'bg-green-600 hover:bg-green-800 text-white',
}

const sizeMap: Record<string, string> = {
  sm: 'px-3 py-1 text-sm',
  md: 'px-4 py-2 text-base',
  lg: 'px-6 py-3 text-lg',
}

const baseClass = 'rounded-md font-medium border-0 transition-colors disabled:opacity-60 disabled:pointer-events-none flex items-center gap-2 cursor-pointer disabled:cursor-not-allowed';
</script>

<template>
  <button
    :class="[baseClass, colorMap[props.color] ?? colorMap.indigo, sizeMap[props.size] ?? sizeMap.md]"
    :disabled="props.disabled || props.loading"
    @click="emit('click')"
    type="button"
  >
    <span v-if="props.icon" :class="['pi', props.icon, 'text-lg']"></span>
    <span v-if="props.loading" class="animate-spin pi pi-spin text-lg"></span>
    <span>{{ props.label }}</span>
  </button>
</template>
