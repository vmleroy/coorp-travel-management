<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useTravelOrderStore } from '@/stores/travelOrderStore'
import {
  createTravelOrder,
  changeTravelOrderStatus,
  cancelTravelOrder,
  type TravelOrder,
} from '@/api/travelOrder'
import { getErrorMessage } from '@/utils/errorHandler'
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
const dateRange = ref<DateRange | null>(null)

const orders = computed(() => travelOrderStore.orders)
const loading = computed(() => travelOrderStore.loading)

function statusLabel(status: string) {
  switch (status) {
    case 'pending':
      return 'Pendente'
    case 'approved':
      return 'Aprovado'
    case 'rejected':
      return 'Rejeitado'
    case 'cancelled':
      return 'Cancelado'
    default:
      return status
  }
}
function statusSeverity(status: string) {
  switch (status) {
    case 'pending':
      return 'warning'
    case 'approved':
      return 'success'
    case 'rejected':
      return 'danger'
    case 'cancelled':
      return 'info'
    default:
      return ''
  }
}

function applyFilters() {
  travelOrderStore.fetchAllOrders({
    user_id: userFilter.value ? Number(userFilter.value) : undefined,
    status: statusFilter.value || undefined,
    destination: destinationFilter.value || undefined,
    departure_date_from: dateRange.value?.[0]
      ? dateRange.value[0].toISOString().slice(0, 10)
      : undefined,
    departure_date_to: dateRange.value?.[1]
      ? dateRange.value[1].toISOString().slice(0, 10)
      : undefined,
  })
}
function clearFilters() {
  statusFilter.value = ''
  destinationFilter.value = ''
  userFilter.value = ''
  dateRange.value = null
  applyFilters()
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
      departure_date: form.value.departure_date?.toISOString().slice(0, 10),
      return_date: form.value.return_date?.toISOString().slice(0, 10),
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

watch([statusFilter, destinationFilter, userFilter, dateRange], applyFilters)

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
        v-model="dateRange"
        selectionMode="range"
        dateFormat="dd/mm/yy"
        placeholder="Período"
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
      <Column field="departure_date" header="Partida" />
      <Column field="return_date" header="Retorno" />
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
          <DatePicker v-model="form.departure_date" dateFormat="dd/mm/yy" required class="w-full" />
        </div>
        <div class="mb-4">
          <label class="block mb-1 font-medium">Data de Retorno</label>
          <DatePicker v-model="form.return_date" dateFormat="dd/mm/yy" required class="w-full" />
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
