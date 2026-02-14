<script setup lang="ts">
import { ref } from 'vue'

const showPassword = ref(false)

const emit = defineEmits(['update:modelValue'])
defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  id: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  invalid: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  error: { type: String, default: '' },
  toggleMask: { type: Boolean, default: true },
})
</script>

<template>
  <div class="flex flex-col gap-2">
    <label v-if="label" :for="id" class="font-semibold">{{ label }}</label>
    <div class="relative">
      <input
        :id="id"
        :type="showPassword ? 'text' : 'password'"
        :placeholder="placeholder"
        :value="modelValue"
        @input="emit('update:modelValue', ($event.target as HTMLInputElement)?.value)"
        :required="required"
        class="w-full border rounded-md px-4 py-2 text-base bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white focus:border-indigo-600 focus:ring-0 pr-10"
        :class="invalid ? 'border-red-500' : 'border-gray-300 dark:border-zinc-700'"
      />
      <button
        v-if="toggleMask"
        type="button"
        @click="showPassword = !showPassword"
        class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
      >
        <i :class="['pi', showPassword ? 'pi-eye-slash' : 'pi-eye']"></i>
      </button>
    </div>
    <small v-if="error" class="text-red-500">{{ error }}</small>
  </div>
</template>
