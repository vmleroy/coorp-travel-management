<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { defineAsyncComponent } from 'vue'
import { AuthLayout } from '@/components/layout/AuthenticatedLayout'

const authStore = useAuthStore()

const AdminDashboard = defineAsyncComponent(() => import('./AdminDashboard.vue'))
const UserDashboard = defineAsyncComponent(() => import('./UserDashboard.vue'))
</script>

<template>
  <AuthLayout>
    <div class="container mx-auto py-8 px-2 md:px-0">
      <div class="w-fit mb-2">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-lg font-light italic">
          Bem-vindo, {{ authStore.user?.name }}
        </p>
      </div>
      <div v-if="authStore.isAdmin">
        <!-- Admin View -->
        <AdminDashboard />
      </div>
      <div v-else>
        <!-- User View -->
        <UserDashboard />
      </div>
    </div>
  </AuthLayout>
</template>
