<script setup lang="ts">
import { ref } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useTravelOrderStore } from '@/stores/travelOrderStore'
import {
  createTravelOrder,
  type TravelOrder,
  type TravelOrderFilters,
  deleteTravelOrder,
} from '@/api/travelOrder'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { Button } from '@/components/button'
import Dialog from 'primevue/dialog'
import { Input } from '@/components/input'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import Tag from 'primevue/tag'
import Toast from 'primevue/toast'
import { getErrorMessage } from '@/utils/errorHandler'
import {
  formatDateToString,
  formatDate,
  statusLabel,
  statusSeverity,
  getTodayAtMidnight,
} from '@/utils/formatters'

interface TravelOrderForm {
  destination: string
  departure_date: Date | null
  return_date: Date | null
}

interface DateRange extends Array<Date | null> {
  0: Date | null
  1: Date | null
}

const toast = useToast()
const travelOrderStore = useTravelOrderStore()

const showCreateDialog = ref(false)
const creating = ref(false)

const statusOptions = [
  { label: 'Todos', value: '' },
  { label: 'Pendente', value: 'pending' },
  { label: 'Aprovado', value: 'approved' },
  { label: 'Rejeitado', value: 'rejected' },
  { label: 'Cancelado', value: 'cancelled' },
]
const statusFilter = ref('')
const destinationFilter = ref('')
const departureDateRange = ref<DateRange | null>(null)
const returnDateRange = ref<DateRange | null>(null)

const form = ref<TravelOrderForm>({
  destination: '',
  departure_date: null,
  return_date: null,
})

const errorMessage = ref('')
const minDate = getTodayAtMidnight()

function applyFilters() {
  const filters: TravelOrderFilters = {}

  if (statusFilter.value) {
    filters.status = statusFilter.value
  }

  if (destinationFilter.value) {
    filters.destination = destinationFilter.value
  }

  if (departureDateRange.value?.[0]) {
    filters.departure_date_from = formatDateToString(departureDateRange.value[0])
    filters.departure_date_to = departureDateRange.value[1]
      ? formatDateToString(departureDateRange.value[1])
      : formatDateToString(departureDateRange.value[0])
  }

  if (returnDateRange.value?.[0]) {
    filters.return_date_from = formatDateToString(returnDateRange.value[0])
    filters.return_date_to = returnDateRange.value[1]
      ? formatDateToString(returnDateRange.value[1])
      : formatDateToString(returnDateRange.value[0])
  }

  travelOrderStore.fetchUserOrders(filters)
}

function clearFilters() {
  statusFilter.value = ''
  destinationFilter.value = ''
  departureDateRange.value = null
  returnDateRange.value = null
  travelOrderStore.fetchUserOrders()
}

async function submitCreate() {
  errorMessage.value = ''
  creating.value = true
  try {
    await createTravelOrder({
      destination: form.value.destination,
      departure_date: form.value.departure_date
        ? formatDateToString(form.value.departure_date)
        : undefined,
      return_date: form.value.return_date ? formatDateToString(form.value.return_date) : undefined,
    })
    showCreateDialog.value = false
    form.value.destination = ''
    form.value.departure_date = null
    form.value.return_date = null
    toast.add({
      severity: 'success',
      summary: 'Sucesso',
      detail: 'Solicitação de viagem criada com sucesso',
      life: 3000,
    })
    applyFilters()
  } catch (e) {
    errorMessage.value = getErrorMessage(e)
    toast.add({
      severity: 'error',
      summary: 'Erro',
      detail: errorMessage.value,
      life: 3000,
    })
  } finally {
    creating.value = false
  }
}

async function cancelOrder(order: TravelOrder) {
  try {
    await deleteTravelOrder(order.id)
    toast.add({
      severity: 'success',
      summary: 'Sucesso',
      detail: 'Solicitação deletada com sucesso',
      life: 3000,
    })
    applyFilters()
  } catch (e) {
    const error = getErrorMessage(e)
    toast.add({
      severity: 'error',
      summary: 'Erro',
      detail: error,
      life: 3000,
    })
  }
}

// Inicial
travelOrderStore.fetchUserOrders()
travelOrderStore.listenToUserOrderUpdates()
</script>

<template>
  <div>
    <Toast />
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Minhas Solicitações de Viagem</h2>
      <Button
        label="Nova Solicitação"
        icon="pi pi-plus"
        @click="showCreateDialog = true"
        class="bg-primary-500 text-white"
      />
    </div>
    <div class="mb-4 flex flex-wrap gap-2">
      <Select
        v-model="statusFilter"
        :options="statusOptions"
        optionLabel="label"
        optionValue="value"
        placeholder="Status"
        class="w-36"
      />
      <Input v-model="destinationFilter" placeholder="Destino" class="w-48" />
      <DatePicker
        v-model="departureDateRange"
        selectionMode="range"
        dateFormat="dd/mm/yy"
        placeholder="Período de Partida"
        class="w-56"
      />
      <DatePicker
        v-model="returnDateRange"
        selectionMode="range"
        dateFormat="dd/mm/yy"
        placeholder="Período de Retorno"
        class="w-56"
      />
      <Button label="Filtrar" icon="pi pi-filter" @click="applyFilters" outlined />
      <Button label="Limpar" icon="pi pi-times" @click="clearFilters" text />
    </div>
    <DataTable
      :value="travelOrderStore.orders"
      :loading="travelOrderStore.loading"
      dataKey="id"
      class="shadow rounded-lg"
      responsiveLayout="scroll"
    >
      <template #empty>
        <div class="text-center py-8 text-gray-500">
          Nenhuma solicitação de viagem encontrada
        </div>
      </template>
      <template #loading>
        <div class="text-center py-8">
          <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
          <p class="mt-2 text-gray-500">Carregando solicitações...</p>
        </div>
      </template>
      <Column field="destination" header="Destino" />
      <Column field="departure_date" header="Partida">
        <template #body="{ data }">
          {{ formatDate(data.departure_date) }}
        </template>
      </Column>
      <Column field="return_date" header="Retorno">
        <template #body="{ data }">
          {{ formatDate(data.return_date) }}
        </template>
      </Column>
      <Column field="status" header="Status">
        <template #body="{ data }">
          <Tag :value="statusLabel(data.status)" :severity="statusSeverity(data.status)" />
        </template>
      </Column>
      <Column field="reason" header="Motivo" />
      <Column header="Ações">
        <template #body="{ data }">
          <Button
            v-if="data.status === 'pending'"
            icon="pi pi-times"
            severity="danger"
            text
            @click="cancelOrder(data)"
            title="Cancelar"
          />
        </template>
      </Column>
    </DataTable>

    <Dialog
      v-model:visible="showCreateDialog"
      modal
      header="Nova Solicitação"
      :style="{ width: '400px' }"
    >
      <form @submit.prevent="submitCreate">
        <div class="mb-4">
          <label class="block mb-1 font-medium">Destino</label>
          <Input v-model="form.destination" required class="w-full" />
        </div>
        <div class="mb-4">
          <label class="block mb-1 font-medium">Data de Partida</label>
          <DatePicker
            v-model="form.departure_date"
            dateFormat="dd/mm/yy"
            :minDate="minDate"
            required
            class="w-full"
          />
        </div>
        <div class="mb-4">
          <label class="block mb-1 font-medium">Data de Retorno</label>
          <DatePicker
            v-model="form.return_date"
            dateFormat="dd/mm/yy"
            :minDate="form.departure_date || minDate"
            required
            class="w-full"
          />
        </div>
        <div class="flex justify-end gap-2">
          <Button label="Cancelar" text @click="showCreateDialog = false" type="button" />
          <Button label="Criar" type="submit" :loading="creating" />
        </div>
      </form>
    </Dialog>
  </div>
</template>
