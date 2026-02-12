<script setup lang="ts">
import { TravelHubIcon } from '@/components/icons'
</script>

<template>
  <footer
    class="py-12 px-6 border-t"
    style="background-color: var(--color-background); border-color: var(--color-border)"
  >
    <div class="max-w-6xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Brand Column -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <TravelHubIcon class="w-6 h-6" />
            <span class="font-semibold text-lg" style="color: var(--color-foreground)"
              >TravelHub</span
            >
          </div>
          <p class="text-sm" style="color: var(--color-foreground-muted)">
            Gestão inteligente de viagens corporativas
          </p>
        </div>

        <!-- Contact Column -->
        <div>
          <h3 class="font-semibold mb-3" style="color: var(--color-foreground)">Contato</h3>
          <p class="text-sm mb-1" style="color: var(--color-foreground-muted)">
            suporte@travelhub.com
          </p>
          <p class="text-sm" style="color: var(--color-foreground-muted)">+55 31 1234-5678</p>
        </div>
      </div>

      <div
        class="pt-8 border-t text-center text-sm"
        style="border-color: var(--color-border); color: var(--color-foreground-muted)"
      >
        © {{ new Date().getFullYear() }} TravelHub. Todos os direitos reservados.
      </div>
    </div>
  </footer>
</template>
