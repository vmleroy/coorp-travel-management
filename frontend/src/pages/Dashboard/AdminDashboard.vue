<script setup lang="ts">
import { ref, computed } from 'vue'
import { useTravelOrderStore } from '@/stores/travelOrderStore'
import {
  createTravelOrder,
  changeTravelOrderStatus,
  cancelTravelOrder,
  type TravelOrder,
  type TravelOrderFilters,
} from '@/api/travelOrder'
import { getErrorMessage } from '@/utils/errorHandler'
import {
  formatDateToString,
  formatDate,
  statusLabel,
  statusSeverity,
  getTodayAtMidnight,
} from '@/utils/formatters'
import { Input } from '@/components/input'
import { Button } from '@/components/button'
import apiClient from '@/api/client'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Select from 'primevue/select'
import DatePicker from 'primevue/datepicker'
import Tag from 'primevue/tag'

interface AdminTravelOrderForm {
  user_id: string | number
  destination: string
  departure_date: Date | null
  return_date: Date | null
}

interface DateRange extends Array<Date | null> {
  0: Date | null
  1: Date | null
}

interface UserOption {
  id: string | number
  name: string
  email?: string
}

const travelOrderStore = useTravelOrderStore()

const showCreateDialog = ref(false)
const creating = ref(false)
const showRejectDialog = ref(false)
const rejecting = ref(false)
const rejectOrderId = ref<number | null>(null)
const rejectReason = ref('')

const statusOptions = [
  { label: 'Todos', value: '' },
  { label: 'Pendente', value: 'pending' },
  { label: 'Aprovado', value: 'approved' },
  { label: 'Rejeitado', value: 'rejected' },
  { label: 'Cancelado', value: 'cancelled' },
]
const statusFilter = ref('')
const destinationFilter = ref('')
const userFilter = ref('')
const userOptions = ref<UserOption[]>([])
const departureDateRange = ref<DateRange | null>(null)
const returnDateRange = ref<DateRange | null>(null)

const orders = computed(() => travelOrderStore.orders)
const loading = computed(() => travelOrderStore.loading)
const minDate = getTodayAtMidnight()

function applyFilters() {
  const filters: TravelOrderFilters = {}

  if (statusFilter.value) {
    filters.status = statusFilter.value
  }

  if (destinationFilter.value) {
    filters.destination = destinationFilter.value
  }

  if (userFilter.value) {
    filters.user_id = Number(userFilter.value)
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

  travelOrderStore.fetchAllOrders(filters)
}

function clearFilters() {
  statusFilter.value = ''
  destinationFilter.value = ''
  userFilter.value = ''
  departureDateRange.value = null
  returnDateRange.value = null
  travelOrderStore.fetchAllOrders()
}

const form = ref<AdminTravelOrderForm>({
  user_id: '',
  destination: '',
  departure_date: null,
  return_date: null,
})
const errorMessage = ref('')

async function submitCreate() {
  errorMessage.value = ''
  creating.value = true
  try {
    await createTravelOrder({
      user_id:
        typeof form.value.user_id === 'string' ? Number(form.value.user_id) : form.value.user_id,
      destination: form.value.destination,
      departure_date: form.value.departure_date
        ? formatDateToString(form.value.departure_date)
        : undefined,
      return_date: form.value.return_date ? formatDateToString(form.value.return_date) : undefined,
    })
    showCreateDialog.value = false
    form.value.user_id = ''
    form.value.destination = ''
    form.value.departure_date = null
    form.value.return_date = null
    applyFilters()
  } catch (e) {
    errorMessage.value = getErrorMessage(e)
  } finally {
    creating.value = false
  }
}

async function approveOrder(order: TravelOrder) {
  try {
    await changeTravelOrderStatus(order.id, 'approved')
    applyFilters()
  } catch (e) {
    getErrorMessage(e)
  }
}

function rejectOrder(order: TravelOrder) {
  rejectOrderId.value = order.id
  showRejectDialog.value = true
  rejectReason.value = ''
}

async function submitReject() {
  if (!rejectOrderId.value) return
  rejecting.value = true
  try {
    await changeTravelOrderStatus(rejectOrderId.value, 'rejected', rejectReason.value)
    showRejectDialog.value = false
    applyFilters()
  } catch (e) {
    getErrorMessage(e)
  } finally {
    rejecting.value = false
  }
}

async function cancelOrder(order: TravelOrder) {
  try {
    await cancelTravelOrder(order.id)
    applyFilters()
  } catch (e) {
    getErrorMessage(e)
  }
}

async function fetchUsers() {
  const { data } = await apiClient.get<{ data: { users: UserOption[] } }>('/auth/users')
  userOptions.value = data.data.users
}

// Inicial
fetchUsers()
travelOrderStore.fetchAllOrders()
travelOrderStore.listenToAllOrderUpdates()
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Todas as Solicitações de Viagem</h2>
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
      <Select
        v-model="userFilter"
        :options="userOptions"
        optionLabel="name"
        optionValue="id"
        placeholder="Usuário"
        class="w-48"
      />
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
      :value="orders"
      :loading="loading"
      dataKey="id"
      class="shadow rounded-lg"
      responsiveLayout="scroll"
    >
      <Column header="Usuário">
        <template #body="{ data }">
          {{ data.user?.name || 'N/A' }}
        </template>
      </Column>
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
            icon="pi pi-check"
            severity="success"
            text
            @click="approveOrder(data)"
            title="Aprovar"
            class="mr-1"
          />
          <Button
            v-if="data.status === 'pending'"
            icon="pi pi-times"
            severity="danger"
            text
            @click="rejectOrder(data)"
            title="Rejeitar"
            class="mr-1"
          />
          <Button
            v-if="data.status === 'pending'"
            icon="pi pi-ban"
            severity="warning"
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
          <label class="block mb-1 font-medium">Usuário</label>
          <Select
            v-model="form.user_id"
            :options="userOptions"
            optionLabel="name"
            optionValue="id"
            required
            class="w-full"
          />
        </div>
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
        <div v-if="errorMessage" class="text-red-600 text-sm mb-2">{{ errorMessage }}</div>
        <div class="flex justify-end gap-2">
          <Button label="Cancelar" text @click="showCreateDialog = false" type="button" />
          <Button label="Criar" type="submit" :loading="creating" />
        </div>
      </form>
    </Dialog>
    <Dialog
      v-model:visible="showRejectDialog"
      modal
      header="Rejeitar Solicitação"
      :style="{ width: '400px' }"
    >
      <form @submit.prevent="submitReject">
        <div class="mb-4">
          <label class="block mb-1 font-medium">Motivo da rejeição</label>
          <Input v-model="rejectReason" required class="w-full" />
        </div>
        <div class="flex justify-end gap-2">
          <Button label="Cancelar" text @click="showRejectDialog = false" type="button" />
          <Button label="Rejeitar" type="submit" :loading="rejecting" severity="danger" />
        </div>
      </form>
    </Dialog>
  </div>
</template>
