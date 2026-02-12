<script setup lang="ts">
import { ref, computed } from 'vue'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

const props = defineProps<{
  visible: boolean
}>()

const emit = defineEmits<{
  'update:visible': [value: boolean]
  'login-success': []
}>()

const isVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value),
})

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')
const errors = ref<{ email?: string; password?: string }>({})

const handleLogin = async () => {
  // Reset errors
  errors.value = {}
  errorMessage.value = ''

  // Basic validation
  if (!email.value) {
    errors.value.email = 'E-mail é obrigatório'
    return
  }
  if (!password.value) {
    errors.value.password = 'Senha é obrigatória'
    return
  }

  loading.value = true

  try {
    // TODO: Integrate with backend API
    // For now, just simulate a login
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Simulate successful login
    emit('login-success')
    isVisible.value = false

    // Reset form
    email.value = ''
    password.value = ''
  } catch (error: Error | unknown) {
    if (error instanceof Error) {
      errorMessage.value = error.message || 'Erro ao fazer login. Tente novamente.'
    } else {
      errorMessage.value = 'Erro ao fazer login. Tente novamente.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <Dialog
    v-model:visible="isVisible"
    modal
    header="Login"
    :style="{ width: '25rem' }"
    :draggable="false"
  >
    <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
      <!-- Email -->
      <div class="flex flex-col gap-2">
        <label for="email" class="font-semibold">E-mail</label>
        <InputText
          id="email"
          v-model="email"
          type="email"
          placeholder="seu@email.com"
          :invalid="!!errors.email"
          required
        />
        <small v-if="errors.email" class="text-red-500">{{ errors.email }}</small>
      </div>

      <!-- Password -->
      <div class="flex flex-col gap-2">
        <label for="password" class="font-semibold">Senha</label>
        <Password
          id="password"
          v-model="password"
          :feedback="false"
          placeholder="Sua senha"
          :invalid="!!errors.password"
          toggleMask
          required
        />
        <small v-if="errors.password" class="text-red-500">{{ errors.password }}</small>
      </div>

      <!-- Error Message -->
      <Message v-if="errorMessage" severity="error" :closable="false">
        {{ errorMessage }}
      </Message>

      <!-- Submit Button -->
      <Button type="submit" label="Entrar" :loading="loading" class="w-full" />
    </form>
  </Dialog>
</template>
