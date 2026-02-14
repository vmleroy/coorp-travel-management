<script setup lang="ts">
</script>

<template>
  <section class="py-24 px-6" style="background-color: var(--color-background-muted)">
    <div class="max-w-6xl mx-auto">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <h2 class="text-4xl sm:text-5xl font-bold mb-4" style="color: var(--color-foreground)">
          Funcionalidades
        </h2>
        <p class="text-xl max-w-2xl mx-auto" style="color: var(--color-foreground-muted)">
          Tudo que você precisa para gerenciar viagens corporativas
        </p>
      </div>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Feature 1 -->
        <div class="p-8 rounded-xl transition-all duration-200 hover:shadow-lg" style="background-color: var(--color-background); border: 1px solid var(--color-border)">
          <div class="w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
            <i class="pi pi-plus text-indigo-600 text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3" style="color: var(--color-foreground)">Criação de Solicitações</h3>
          <p class="text-base leading-relaxed" style="color: var(--color-foreground-muted)">
            Crie solicitações de viagem de forma rápida e intuitiva
          </p>
        </div>

        <!-- Feature 2 -->
        <div class="p-8 rounded-xl transition-all duration-200 hover:shadow-lg" style="background-color: var(--color-background); border: 1px solid var(--color-border)">
          <div class="w-12 h-12 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
            <i class="pi pi-check-circle text-purple-600 text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3" style="color: var(--color-foreground)">Aprovação Rápida</h3>
          <p class="text-base leading-relaxed" style="color: var(--color-foreground-muted)">
            Aprove ou rejeite solicitações com apenas um clique
          </p>
        </div>

        <!-- Feature 3 -->
        <div class="p-8 rounded-xl transition-all duration-200 hover:shadow-lg" style="background-color: var(--color-background); border: 1px solid var(--color-border)">
          <div class="w-12 h-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-4">
            <i class="pi pi-bell text-green-600 text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3" style="color: var(--color-foreground)">Notificações em Tempo Real</h3>
          <p class="text-base leading-relaxed" style="color: var(--color-foreground-muted)">
            Receba notificações instantâneas sobre mudanças de status
          </p>
        </div>

        <!-- Feature 4 -->
        <div class="p-8 rounded-xl transition-all duration-200 hover:shadow-lg" style="background-color: var(--color-background); border: 1px solid var(--color-border)">
          <div class="w-12 h-12 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center mb-4">
            <i class="pi pi-history text-orange-600 text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3" style="color: var(--color-foreground)">Histórico Completo</h3>
          <p class="text-base leading-relaxed" style="color: var(--color-foreground-muted)">
            Acompanhe todas as suas solicitações em um só lugar
          </p>
        </div>

        <!-- Feature 5 -->
        <div class="p-8 rounded-xl transition-all duration-200 hover:shadow-lg" style="background-color: var(--color-background); border: 1px solid var(--color-border)">
          <div class="w-12 h-12 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center mb-4">
            <i class="pi pi-lock text-teal-600 text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3" style="color: var(--color-foreground)">Segurança Total</h3>
          <p class="text-base leading-relaxed" style="color: var(--color-foreground-muted)">
            Autenticação segura e proteção de dados sensíveis
          </p>
        </div>

        <!-- Feature 6 -->
        <div class="p-8 rounded-xl transition-all duration-200 hover:shadow-lg" style="background-color: var(--color-background); border: 1px solid var(--color-border)">
          <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-4">
            <i class="pi pi-bolt text-blue-600 text-xl"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3" style="color: var(--color-foreground)">Performance Otimizada</h3>
          <p class="text-base leading-relaxed" style="color: var(--color-foreground-muted)">
            Interface rápida e responsiva em qualquer dispositivo
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
