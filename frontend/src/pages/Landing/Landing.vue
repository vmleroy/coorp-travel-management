<script setup lang="ts">
defineOptions({
  name: 'LandingPage',
})
import { ref } from 'vue'
import { ThemeToggle } from '@/components'
import { LandingHero, LandingFeatures, LandingFooter, LoginModal } from './components'
import AppButton from '@/components/AppButton.vue'

const isLoginModalVisible = ref(false)

const openLoginModal = () => {
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
      <AppButton
        label="Entrar"
        icon="pi-sign-in"
        color="indigo"
        size="md"
        @click="openLoginModal"
      />
      <ThemeToggle />
    </div>

    <LandingHero @open-login="openLoginModal" />
    <LandingFeatures />
    <LandingFooter />

    <LoginModal v-model:visible="isLoginModalVisible" @login-success="handleLoginSuccess" />
  </div>
</template>
