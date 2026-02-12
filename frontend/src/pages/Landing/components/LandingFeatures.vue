<script setup lang="ts">
import Card from 'primevue/card'
</script>

<template>
  <section class="py-20" style="background-color: var(--color-background)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4" style="color: var(--color-foreground)">
          Tudo que você precisa em um só lugar
        </h2>
        <p class="text-lg max-w-2xl mx-auto" style="color: var(--color-foreground-muted)">
          Simplifique o processo de gestão de viagens corporativas com nossas funcionalidades completas
        </p>
      </div>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <Card class="bg-gradient-to-br from-blue-50 to-indigo-50">
          <template #content>
            <div class="flex flex-col">
              <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mb-4">
                <i class="pi pi-plus text-white text-xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-3" style="color: var(--color-foreground)">Criação de Solicitações</h3>
              <p style="color: var(--color-foreground-muted)">
                Crie solicitações de viagem de forma rápida e intuitiva. Informe destino, datas e outros detalhes necessários.
              </p>
            </div>
          </template>
        </Card>

        <!-- Feature 2 -->
        <Card class="bg-gradient-to-br from-purple-50 to-pink-50">
          <template #content>
            <div class="flex flex-col">
              <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mb-4">
                <i class="pi pi-check-circle text-white text-xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-3" style="color: var(--color-foreground)">Aprovação Rápida</h3>
              <p style="color: var(--color-foreground-muted)">
                Administradores podem aprovar ou rejeitar solicitações com apenas um clique. Processo ágil e eficiente.
              </p>
            </div>
          </template>
        </Card>

        <!-- Feature 3 -->
        <Card class="bg-gradient-to-br from-green-50 to-emerald-50">
          <template #content>
            <div class="flex flex-col">
              <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mb-4">
                <i class="pi pi-bell text-white text-xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-3" style="color: var(--color-foreground)">Notificações em Tempo Real</h3>
              <p style="color: var(--color-foreground-muted)">
                Receba notificações instantâneas sobre mudanças de status, aprovações e atualizações importantes.
              </p>
            </div>
          </template>
        </Card>

        <!-- Feature 4 -->
        <Card class="bg-gradient-to-br from-orange-50 to-red-50">
          <template #content>
            <div class="flex flex-col">
              <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center mb-4">
                <i class="pi pi-history text-white text-xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-3" style="color: var(--color-foreground)">Histórico Completo</h3>
              <p style="color: var(--color-foreground-muted)">
                Acompanhe todas as suas solicitações, aprovações e histórico de viagens em um só lugar.
              </p>
            </div>
          </template>
        </Card>

        <!-- Feature 5 -->
        <Card class="bg-gradient-to-br from-teal-50 to-cyan-50">
          <template #content>
            <div class="flex flex-col">
              <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center mb-4">
                <i class="pi pi-lock text-white text-xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-3" style="color: var(--color-foreground)">Segurança Total</h3>
              <p style="color: var(--color-foreground-muted)">
                Autenticação segura, controle de permissões e proteção de dados sensíveis da sua empresa.
              </p>
            </div>
          </template>
        </Card>

        <!-- Feature 6 -->
        <Card class="bg-gradient-to-br from-yellow-50 to-amber-50">
          <template #content>
            <div class="flex flex-col">
              <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center mb-4">
                <i class="pi pi-bolt text-white text-xl"></i>
              </div>
              <h3 class="text-xl font-bold mb-3" style="color: var(--color-foreground)">Performance Otimizada</h3>
              <p style="color: var(--color-foreground-muted)">
                Interface rápida e responsiva. Acesse de qualquer dispositivo, em qualquer lugar, a qualquer hora.
              </p>
            </div>
          </template>
        </Card>
      </div>
    </div>
  </section>
</template>
