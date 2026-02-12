<script setup lang="ts">
defineOptions({
  name: 'LandingPage',
})
import { ref } from 'vue'
import { LandingHero, LandingFeatures, LandingFooter, LoginModal } from './components'

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
  <div class="min-h-screen bg-gray-50">
    <LandingHero @open-login="openLoginModal" />
    <LandingFeatures />
    <LandingFooter />

    <LoginModal v-model:visible="isLoginModalVisible" @login-success="handleLoginSuccess" />
  </div>
</template>
