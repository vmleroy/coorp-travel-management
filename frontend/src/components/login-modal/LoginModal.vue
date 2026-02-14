<script setup lang="ts">
import { ref, computed } from 'vue'
import Dialog from 'primevue/dialog'
import Message from 'primevue/message'
import { Input } from '@/components/input'
import { Password } from '@/components/password'
import { Button } from '@/components/button'
import { useLogin, useRegister } from '@/composables/useAuthQueries'
import { getErrorMessage } from '@/utils/errorHandler'

const props = defineProps<{ visible: boolean; mode?: 'login' | 'register' }>()
const emit = defineEmits<{
  'update:visible': [value: boolean]
  'update:mode': [value: 'login' | 'register']
  'login-success': []
}>()

const isVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value),
})

const mode = computed({
  get: () => props.mode ?? 'login',
  set: (value) => emit('update:mode', value),
})

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const errorMessage = ref('')
const errors = ref<{ name?: string; email?: string; password?: string; confirmPassword?: string }>(
  {},
)

const loginMutation = useLogin()
const registerMutation = useRegister()

const isLoading = computed(() => loginMutation.isPending.value || registerMutation.isPending.value)

const validateLogin = (): boolean => {
  const validations = [
    { field: 'email', value: email.value, message: 'E-mail é obrigatório' },
    { field: 'password', value: password.value, message: 'Senha é obrigatória' },
  ]

  return validateFields(validations)
}

const validateRegister = (): boolean => {
  const validations = [
    { field: 'name', value: name.value, message: 'Nome é obrigatório' },
    { field: 'email', value: email.value, message: 'E-mail é obrigatório' },
    { field: 'password', value: password.value, message: 'Senha é obrigatória' },
    { field: 'confirmPassword', value: confirmPassword.value, message: 'Confirme sua senha' },
  ]

  if (!validateFields(validations)) return false

  if (password.value !== confirmPassword.value) {
    errors.value.confirmPassword = 'As senhas não coincidem'
    return false
  }

  return true
}

const validateFields = (
  validations: Array<{ field: string; value: string; message: string }>,
): boolean => {
  errors.value = {}

  for (const { field, value, message } of validations) {
    if (!value) {
      errors.value[field as keyof typeof errors.value] = message
      return false
    }
  }

  return true
}

const handleApiError = (error: unknown): void => {
  errorMessage.value = getErrorMessage(error)
}

const resetForm = (): void => {
  name.value = ''
  email.value = ''
  password.value = ''
  confirmPassword.value = ''
  errors.value = {}
  errorMessage.value = ''
}

const handleSubmit = async () => {
  errors.value = {}
  errorMessage.value = ''

  if (mode.value === 'login') {
    if (!validateLogin()) return

    try {
      await loginMutation.mutateAsync({ email: email.value, password: password.value })
      emit('login-success')
      isVisible.value = false
      resetForm()
    } catch (error) {
      handleApiError(error)
    }
  } else {
    if (!validateRegister()) return

    try {
      await registerMutation.mutateAsync({
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: confirmPassword.value,
        role: 'admin',
      })
      emit('login-success')
      isVisible.value = false
      resetForm()
    } catch (error) {
      handleApiError(error)
    }
  }
}
</script>

<template>
  <Dialog
    v-model:visible="isVisible"
    modal
    :header="mode === 'login' ? 'Login' : 'Registro'"
    :style="{ width: '25rem' }"
    :draggable="false"
  >
    <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
      <div v-if="mode === 'register'" class="flex flex-col gap-2">
        <Input
          id="name"
          label="Nome"
          v-model="name"
          placeholder="Seu nome completo"
          :invalid="!!errors.name"
          :error="errors.name"
          required
        />
      </div>
      <Input
        id="email"
        label="E-mail"
        v-model="email"
        type="email"
        placeholder="seu@email.com"
        :invalid="!!errors.email"
        :error="errors.email"
        required
      />
      <Password
        id="password"
        label="Senha"
        v-model="password"
        placeholder="Sua senha"
        :invalid="!!errors.password"
        :error="errors.password"
        required
      />
      <div v-if="mode === 'register'" class="flex flex-col gap-2">
        <Password
          id="confirmPassword"
          label="Confirme a senha"
          v-model="confirmPassword"
          placeholder="Repita sua senha"
          :invalid="!!errors.confirmPassword"
          :error="errors.confirmPassword"
          required
        />
      </div>
      <Message v-if="errorMessage" severity="error" :closable="false">
        {{ errorMessage }}
      </Message>
      <Button
        type="submit"
        :label="mode === 'login' ? 'Entrar' : 'Registrar'"
        :loading="isLoading"
        class="w-full"
      />
      <div class="text-center mt-2">
        <span v-if="mode === 'login'">
          Não tem uma conta?
          <button
            type="button"
            class="underline text-indigo-600 hover:text-indigo-700 font-medium ml-1"
            @click="mode = 'register'"
          >
            Registrar
          </button>
        </span>
        <span v-else>
          Já tem uma conta?
          <button
            type="button"
            class="underline text-indigo-600 hover:text-indigo-700 font-medium ml-1"
            @click="mode = 'login'"
          >
            Entrar
          </button>
        </span>
      </div>
    </form>
  </Dialog>
</template>
