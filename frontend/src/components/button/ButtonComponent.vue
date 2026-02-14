<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
  label: { type: String, default: '' },
  icon: { type: String, default: undefined },
  color: { type: String, default: 'indigo' },
  size: { type: String, default: 'md' },
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  variant: { type: String, default: undefined },
  type: { type: String as () => 'button' | 'submit' | 'reset', default: 'button' },
  severity: { type: String, default: 'indigo' },
  text: { type: Boolean, default: false },
  outlined: { type: Boolean, default: false },
})

const buttonRef = ref<HTMLButtonElement>()
defineExpose({
  buttonRef,
})

const emit = defineEmits(['click'])

const colorMap: Record<string, string> = {
  indigo: 'bg-indigo-600 hover:bg-indigo-800 text-white',
  gray: 'bg-gray-200 hover:bg-gray-400 text-gray-900',
  green: 'bg-green-600 hover:bg-green-800 text-white',
  success: 'bg-green-600 hover:bg-green-800 text-white',
  danger: 'bg-red-600 hover:bg-red-800 text-white',
  warning: 'bg-yellow-600 hover:bg-yellow-800 text-white',
}

const sizeMap: Record<string, string> = {
  sm: 'px-3 py-1 text-sm',
  md: 'px-4 py-2 text-base',
  lg: 'px-6 py-3 text-lg',
}

const variantMap: Record<string, string> = {
  default: '',
  ghosted:
    'bg-transparent text-gray-800 dark:text-gray-100 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white active:scale-90 focus-visible:ring-2 focus-visible:ring-primary-500',
}

const textColorMap: Record<string, string> = {
  indigo: 'text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/20',
  success: 'text-green-600 hover:text-green-800 hover:bg-green-50 dark:hover:bg-green-900/20',
  danger: 'text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/20',
  warning: 'text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 dark:hover:bg-yellow-900/20',
}

const outlinedColorMap: Record<string, string> = {
  indigo: 'border-indigo-600 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20',
  success: 'border-green-600 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20',
  danger: 'border-red-600 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20',
  warning: 'border-yellow-600 text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/20',
}

const baseClass =
  'group rounded-md font-medium border-0 transition-colors disabled:opacity-60 disabled:pointer-events-none flex items-center justify-center gap-2 cursor-pointer disabled:cursor-not-allowed'

const getButtonClass = () => {
  if (props.variant && variantMap[props.variant]) {
    return variantMap[props.variant]
  }

  const severityKey = props.severity || props.color

  if (props.text) {
    return textColorMap[severityKey] || textColorMap.indigo
  }

  if (props.outlined) {
    return `border ${outlinedColorMap[severityKey] || outlinedColorMap.indigo}`
  }

  return colorMap[severityKey] || colorMap.indigo
}
</script>

<template>
  <button
    ref="buttonRef"
    :class="[baseClass, getButtonClass(), sizeMap[props.size] ?? sizeMap.md]"
    :disabled="props.disabled || props.loading"
    :type="props.type"
    @click="emit('click', $event)"
  >
    <span class="flex items-center justify-center gap-2 w-full">
      <span
        v-if="props.icon"
        :class="['pi', props.icon, 'text-lg', 'flex', 'items-center', 'justify-center']"
        style="min-width: 1.25em"
      ></span>
      <span v-if="props.loading" class="animate-spin pi pi-spin text-lg"></span>
      <span v-if="props.label">{{ props.label }}</span>
    </span>
  </button>
</template>
