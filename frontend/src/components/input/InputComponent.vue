<script setup lang="ts">
import Input from 'primevue/inputtext'

defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  id: { type: String, default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  invalid: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="flex flex-col gap-2">
    <label v-if="label" :for="id" class="font-semibold">{{ label }}</label>
    <Input
      :id="id"
      :type="type"
      :placeholder="placeholder"
      :value="modelValue"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement)?.value)"
      :required="required"
      class="w-full"
      :class="invalid ? 'border-red-500' : 'border-gray-300 dark:border-zinc-700'"
    />
    <small v-if="error" class="text-red-500">{{ error }}</small>
  </div>
</template>
