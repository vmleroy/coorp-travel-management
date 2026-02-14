import apiClient from './client'

export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'user'
  created_at?: string
  updated_at?: string
}

export interface CreateUserPayload {
  name: string
  email: string
  password: string
  role?: 'admin' | 'user'
}

export interface UpdateUserPayload {
  name?: string
  email?: string
  password?: string
  role?: 'admin' | 'user'
}

export async function getUsers() {
  const response = await apiClient.get<{ data: { users: User[] } }>('/auth/users')
  return response.data.data.users
}

export async function getUserById(id: number) {
  const response = await apiClient.get<{ data: { user: User } }>(`/auth/users/${id}`)
  return response.data.data.user
}

export async function createUser(payload: CreateUserPayload) {
  const response = await apiClient.post<{ data: { user: User } }>('/auth/create-user', payload)
  return response.data.data.user
}

export async function updateUser(id: number, payload: UpdateUserPayload) {
  const response = await apiClient.put<{ data: { user: User } }>(`/auth/users/${id}`, payload)
  return response.data.data.user
}
