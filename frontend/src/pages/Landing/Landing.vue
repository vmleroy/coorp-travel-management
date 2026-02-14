<script setup lang="ts">
defineOptions({
  name: 'LandingPage',
})
import { ref } from 'vue'
import { ThemeToggle } from '@/components'
import { LandingHero, LandingFeatures, LandingFooter } from './components'
import { Button } from '@/components/button'
import { LoginModal } from '@/components/login-modal'

const isLoginModalVisible = ref(false)
const loginModalMode = ref<'login' | 'register'>('login')

const openLoginModal = () => {
  loginModalMode.value = 'login'
  isLoginModalVisible.value = true
}

const openRegisterModal = () => {
  loginModalMode.value = 'register'
  isLoginModalVisible.value = true
}

const handleLoginSuccess = () => {
  // TODO: Navigate to dashboard
  console.log('Login successful!')
}
</script>

<template name="landing-page">
  <div class="relative w-full h-full p-4">
    <!-- Theme Toggle -->
    <div class="relative w-full flex justify-end items-center gap-2 top-0">
      <Button
        label="Entrar"
        icon="pi-sign-in"
        color="indigo"
        size="md"
        @click="openLoginModal"
      />
      <ThemeToggle />
    </div>

    <LandingHero @open-login="openRegisterModal" />
    <LandingFeatures />
    <LandingFooter />

    <LoginModal v-model:visible="isLoginModalVisible" v-model:mode="loginModalMode" @login-success="handleLoginSuccess" />
  </div>
</template>
