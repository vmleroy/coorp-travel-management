<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { getUsers, createUser, updateUser, type User, type CreateUserPayload } from '@/api/user'
import { getErrorMessage } from '@/utils/errorHandler'
import { AuthLayout } from '@/components/layout/AuthenticatedLayout'
import { Button } from '@/components/button'
import { Input } from '@/components/input'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Tag from 'primevue/tag'
import Select from 'primevue/select'
import Toast from 'primevue/toast'

const toast = useToast()
const users = ref<User[]>([])
const loading = ref(false)
const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const creating = ref(false)
const updating = ref(false)
const errorMessage = ref('')
const selectedUser = ref<User | null>(null)

const roleOptions = [
  { label: 'Usuário', value: 'user' },
  { label: 'Administrador', value: 'admin' },
]

const createForm = ref<CreateUserPayload>({
  name: '',
  email: '',
  password: '',
  role: 'user',
})

const editForm = ref({
  name: '',
  email: '',
  role: 'user' as 'admin' | 'user',
})

async function loadUsers() {
  loading.value = true
  try {
    users.value = await getUsers()
  } catch (e) {
    console.error('Erro ao carregar usuários:', e)
    toast.add({
      severity: 'error',
      summary: 'Erro',
      detail: 'Não foi possível carregar os usuários',
      life: 3000,
    })
  } finally {
    loading.value = false
  }
}

async function submitCreate() {
  errorMessage.value = ''
  creating.value = true
  try {
    await createUser(createForm.value)
    showCreateDialog.value = false
    createForm.value = {
      name: '',
      email: '',
      password: '',
      role: 'user',
    }
    toast.add({
      severity: 'success',
      summary: 'Sucesso',
      detail: 'Usuário criado com sucesso',
      life: 3000,
    })
    await loadUsers()
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

function openEditDialog(user: User) {
  selectedUser.value = user
  editForm.value = {
    name: user.name,
    email: user.email,
    role: user.role,
  }
  showEditDialog.value = true
}

async function submitEdit() {
  if (!selectedUser.value) return
  errorMessage.value = ''
  updating.value = true
  try {
    await updateUser(selectedUser.value.id, editForm.value)
    showEditDialog.value = false
    selectedUser.value = null
    toast.add({
      severity: 'success',
      summary: 'Sucesso',
      detail: 'Usuário atualizado com sucesso',
      life: 3000,
    })
    await loadUsers()
  } catch (e) {
    errorMessage.value = getErrorMessage(e)
    toast.add({
      severity: 'error',
      summary: 'Erro',
      detail: errorMessage.value,
      life: 3000,
    })
  } finally {
    updating.value = false
  }
}

function getRoleLabel(role: string) {
  return role === 'admin' ? 'Administrador' : 'Usuário'
}

function getRoleSeverity(role: string) {
  return role === 'admin' ? 'success' : 'info'
}

onMounted(() => {
  loadUsers()
})
</script>

<template>
  <AuthLayout>
    <Toast />
    <div class="container mx-auto py-8 px-2 md:px-0">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestão de Usuários</h1>
        <Button
          label="Novo Usuário"
          icon="pi pi-plus"
          @click="showCreateDialog = true"
          class="bg-primary-500 text-white"
        />
      </div>

      <DataTable
        :value="users"
        :loading="loading"
        dataKey="id"
        class="shadow rounded-lg"
        responsiveLayout="scroll"
      >
        <Column field="id" header="ID" class="w-20" />
        <Column field="name" header="Nome" />
        <Column field="email" header="Email" />
        <Column field="role" header="Função">
          <template #body="{ data }">
            <Tag :value="getRoleLabel(data.role)" :severity="getRoleSeverity(data.role)" />
          </template>
        </Column>
        <Column header="Ações" class="w-32">
          <template #body="{ data }">
            <Button
              icon="pi pi-pencil"
              severity="secondary"
              text
              @click="openEditDialog(data)"
              title="Editar"
            />
          </template>
        </Column>
      </DataTable>

      <!-- Dialog Criar Usuário -->
      <Dialog
        v-model:visible="showCreateDialog"
        modal
        header="Novo Usuário"
        :style="{ width: '450px' }"
      >
        <form @submit.prevent="submitCreate">
          <div class="mb-4">
            <label class="block mb-1 font-medium">Nome</label>
            <Input v-model="createForm.name" required class="w-full" />
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-medium">Email</label>
            <Input v-model="createForm.email" type="email" required class="w-full" />
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-medium">Senha</label>
            <Input v-model="createForm.password" type="password" required class="w-full" />
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-medium">Função</label>
            <Select
              v-model="createForm.role"
              :options="roleOptions"
              optionLabel="label"
              optionValue="value"
              class="w-full"
            />
          </div>
          <p v-if="errorMessage" class="text-red-500 mb-2 text-sm">{{ errorMessage }}</p>
          <div class="flex justify-end gap-2">
            <Button label="Cancelar" text @click="showCreateDialog = false" type="button" />
            <Button label="Criar" type="submit" :loading="creating" />
          </div>
        </form>
      </Dialog>

      <!-- Dialog Editar Usuário -->
      <Dialog
        v-model:visible="showEditDialog"
        modal
        header="Editar Usuário"
        :style="{ width: '450px' }"
      >
        <form @submit.prevent="submitEdit">
          <div class="mb-4">
            <label class="block mb-1 font-medium">Nome</label>
            <Input v-model="editForm.name" required class="w-full" />
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-medium">Email</label>
            <Input v-model="editForm.email" type="email" required class="w-full" />
          </div>
          <div class="mb-4">
            <label class="block mb-1 font-medium">Função</label>
            <Select
              v-model="editForm.role"
              :options="roleOptions"
              optionLabel="label"
              optionValue="value"
              class="w-full"
            />
          </div>
          <p v-if="errorMessage" class="text-red-500 mb-2 text-sm">{{ errorMessage }}</p>
          <div class="flex justify-end gap-2">
            <Button label="Cancelar" text @click="showEditDialog = false" type="button" />
            <Button label="Salvar" type="submit" :loading="updating" />
          </div>
        </form>
      </Dialog>
    </div>
  </AuthLayout>
</template>
