<script setup lang="ts">
import { ref, computed } from 'vue'
import Dialog from 'primevue/dialog'
import Message from 'primevue/message'
import { Input } from '@/components/input'
import { Password } from '@/components/password'
import { Button } from '@/components/button'

const props = defineProps<{ visible: boolean; mode?: 'login' | 'register' }>()
const emit = defineEmits<{ 'update:visible': [value: boolean]; 'update:mode': [value: 'login' | 'register']; 'login-success': [] }>()

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
const loading = ref(false)
const errorMessage = ref('')
const errors = ref<{ name?: string; email?: string; password?: string; confirmPassword?: string }>(
  {},
)

const handleSubmit = async () => {
  errors.value = {}
  errorMessage.value = ''

  if (mode.value === 'login') {
    if (!email.value) {
      errors.value.email = 'E-mail é obrigatório'
      return
    }
    if (!password.value) {
      errors.value.password = 'Senha é obrigatória'
      return
    }
  } else {
    if (!name.value) {
      errors.value.name = 'Nome é obrigatório'
      return
    }
    if (!email.value) {
      errors.value.email = 'E-mail é obrigatório'
      return
    }
    if (!password.value) {
      errors.value.password = 'Senha é obrigatória'
      return
    }
    if (!confirmPassword.value) {
      errors.value.confirmPassword = 'Confirme sua senha'
      return
    }
    if (password.value !== confirmPassword.value) {
      errors.value.confirmPassword = 'As senhas não coincidem'
      return
    }
  }

  loading.value = true
  try {
    await new Promise((resolve) => setTimeout(resolve, 1000))
    emit('login-success')
    isVisible.value = false
    name.value = ''
    email.value = ''
    password.value = ''
    confirmPassword.value = ''
  } catch {
    errorMessage.value = 'Erro ao processar. Tente novamente.'
  } finally {
    loading.value = false
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
        :loading="loading"
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
