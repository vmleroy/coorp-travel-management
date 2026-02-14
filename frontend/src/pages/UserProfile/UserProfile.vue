<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useCurrentUser } from '@/composables/useAuthQueries'
import { AuthLayout } from '@/components/layout/AuthenticatedLayout'
import { UserProfileSkeleton } from '@/components/skeleton'

const authStore = useAuthStore()
const { data: currentUser, isLoading } = useCurrentUser()

const user = computed(() => currentUser.value || authStore.user)

const roleLabels: Record<string, string> = {
  admin: 'Administrador',
  manager: 'Gerenciador',
  user: 'Usuário',
}
</script>

<template>
  <AuthLayout>
    <template v-if="isLoading || !user">
      <UserProfileSkeleton />
    </template>
    <template v-else>
      <!-- User Profile Card -->
      <div
        class="rounded-lg p-8 border"
        style="background-color: var(--color-background); border-color: var(--color-border)"
      >
        <!-- Profile Header -->
        <div
          class="flex items-center gap-6 mb-8 pb-8 border-b"
          style="border-color: var(--color-border)"
        >
          <div
            class="w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold"
            style="background-color: var(--color-background-muted); color: var(--color-foreground)"
          >
            {{ user?.name ? user.name.charAt(0).toUpperCase() : '' }}
          </div>
          <div>
            <h1 class="text-3xl font-bold mb-2" style="color: var(--color-foreground)">
              {{ user?.name || '' }}
            </h1>
            <p style="color: var(--color-foreground-muted)">{{ user.email }}</p>
          </div>
        </div>

        <!-- User Details -->
        <div class="space-y-6">
          <!-- Role -->
          <div>
            <label
              class="block text-sm font-semibold mb-2"
              style="color: var(--color-foreground-muted)"
            >
              Cargo
            </label>
            <div
              class="inline-block px-4 py-2 rounded-md"
              style="
                background-color: var(--color-background-muted);
                color: var(--color-foreground);
              "
            >
              {{ user?.role ? roleLabels[user.role] || user.role : '' }}
            </div>
          </div>
        </div>
      </div>
    </template>
  </AuthLayout>
</template>
