<script setup lang="ts">
import Divider from 'primevue/divider'
import { TravelHubIcon } from '@/components/icons'
</script>

<template>
  <footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Brand -->
        <div>
          <div class="flex items-center gap-2 mb-4">
            <TravelHubIcon color="#4F46E5" />
            <span class="text-xl font-bold text-white">TravelHub</span>
          </div>
          <p class="text-sm text-gray-400">
            Simplifique a gestão de viagens corporativas da sua empresa.
          </p>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-white font-semibold mb-4">Contato</h3>
          <ul class="space-y-2 text-sm">
            <li class="flex items-center gap-2">
              <i class="pi pi-envelope"></i>
              contato@travelhub.com
            </li>
            <li class="flex items-center gap-2">
              <i class="pi pi-phone"></i>
              +55 (31) 9999-9999
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <Divider class="my-8" />
      <div class="text-center text-sm text-gray-400">
        <p>&copy; 2026 TravelHub. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>
</template>
