<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  icon: { type: String, default: undefined },
  color: { type: String, default: 'indigo' }, // indigo, gray, green, etc.
  size: { type: String, default: 'md' }, // sm, md, lg
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  variant: { type: String, default: undefined }, // e.g. 'ghosted'
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
}

const sizeMap: Record<string, string> = {
  sm: 'px-3 py-1 text-sm',
  md: 'px-4 py-2 text-base',
  lg: 'px-6 py-3 text-lg',
}

const variantMap: Record<string, string> = {
  default: '',
  ghosted:
    'bg-transparent hover:bg-gray-800 dark:hover:bg-gray-100 text-gray-700 dark:text-gray-200 border border-transparent hover:border-gray-800 dark:hover:border-gray-100 active:scale-90',
}

const baseClass =
  'group rounded-md font-medium border-0 transition-colors disabled:opacity-60 disabled:pointer-events-none flex items-center justify-center gap-2 cursor-pointer disabled:cursor-not-allowed'
</script>

<template>
  <button
    ref="buttonRef"
    :class="[
      baseClass,
      props.variant && variantMap[props.variant]
        ? variantMap[props.variant]
        : (colorMap[props.color] ?? colorMap.indigo),
      sizeMap[props.size] ?? sizeMap.md,
    ]"
    :disabled="props.disabled || props.loading"
    @click="emit('click', $event)"
    type="button"
  >
    <span class="flex items-center justify-center gap-2 w-full">
      <span
        v-if="props.icon"
        :class="[
          'pi',
          props.icon,
          'text-lg',
          'flex',
          'items-center',
          'justify-center',
          ...(props.variant === 'ghosted'
            ? [
                'text-gray-700',
                'dark:text-gray-200',
                'group-hover:text-white',
                'dark:group-hover:text-gray-900',
              ]
            : []),
        ]"
        style="min-width: 1.25em"
      ></span>
      <span v-if="props.loading" class="animate-spin pi pi-spin text-lg"></span>
      <span v-if="props.label">{{ props.label }}</span>
    </span>
  </button>
</template>
