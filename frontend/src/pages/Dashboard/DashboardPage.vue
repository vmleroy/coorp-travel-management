<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import { defineAsyncComponent } from 'vue'

const authStore = useAuthStore()

const AdminDashboard = defineAsyncComponent(() => import('./AdminDashboard.vue'))
const UserDashboard = defineAsyncComponent(() => import('./UserDashboard.vue'))
</script>

<template>
	<div class="container mx-auto py-8 px-2 md:px-0">
		<h1 class="text-2xl font-bold mb-6">Dashboard</h1>
		<div v-if="authStore.isAdmin">
			<!-- Admin View -->
			<AdminDashboard />
		</div>
		<div v-else>
			<!-- User View -->
			<UserDashboard />
		</div>
	</div>
</template>
