import Echo from 'laravel-echo'
import { useAuthStore, type User } from '@/stores/authStore'

export interface OrderUpdateEvent {
  id: number
  user_id: number
  user: User
  destination: string
  departure_date: string
  return_date: string
  status: 'pending' | 'approved' | 'rejected' | 'cancelled'
  reason?: string | null
  updated_at: string
}

let echoInstance: Echo<'reverb'> | null = null

export function initializeEcho(): Echo<'reverb'> {
  if (echoInstance) {
    console.log('[Echo] Já inicializado, reutilizando instância')
    return echoInstance
  }

  try {
    const authStore = useAuthStore()
    const token = authStore.token

    const wsHost = import.meta.env.VITE_WS_HOST || 'localhost'
    const wsPort = import.meta.env.VITE_WS_PORT ? Number(import.meta.env.VITE_WS_PORT) : 8080
    const appKey = import.meta.env.VITE_REVERB_APP_KEY || 'coorp-travel'

    echoInstance = new Echo({
      broadcaster: 'reverb',
      key: appKey,
      wsHost: wsHost,
      wsPort: wsPort ?? 80,
      wssPort: import.meta.env.VITE_WSS_PORT ? Number(import.meta.env.VITE_WSS_PORT) : 443,
      forceTLS: import.meta.env.VITE_WS_ENCRYPTED === 'true',
      enabledTransports: ['ws', 'wss'],
      authEndpoint: `${import.meta.env.VITE_API_URL?.replace('/api', '')}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: token ? `Bearer ${token}` : '',
        },
      },
    })

    console.log('[Echo] Inicializado com sucesso', {
      wsHost,
      wsPort,
      appKey,
      apiUrl: import.meta.env.VITE_API_URL,
      token: token ? token.substring(0, 10) + '...' : null,
    })

    return echoInstance
  } catch (e) {
    console.error('[Echo] Erro ao inicializar:', e)
    throw e
  }
}

export function getEcho(): Echo<'reverb'> | null {
  return echoInstance
}

export function disconnectEcho(): void {
  if (echoInstance) {
    echoInstance.disconnect()
    echoInstance = null
  }
  console.log('✅ Echo disconnected')
}

export function subscribeToOrderUpdates(
  userId: number,
  callback: (data: OrderUpdateEvent) => void,
): void {
  try {
    const echo = initializeEcho()
    const channelName = `notifications.${userId}`
    const channel = echo.private(channelName)
    console.log(`[Echo] Subscribing to user order updates on PRIVATE channel: ${channelName}`)

    channel.listen('.order.status.changed', (data: OrderUpdateEvent) => {
      console.log('[Echo] RECEIVED: order.status.changed (USER)', data)
      callback(data)
    })

  } catch (e) {
    console.error('[Echo] Erro ao se inscrever em atualizações de pedido:', e)
  }
}

export function subscribeToAllOrderUpdates(callback: (data: OrderUpdateEvent) => void): void {
  try {
    const echo = initializeEcho()
    const channelName = 'admin-notifications'
    const channel = echo.channel(channelName)
    console.log(`[Echo] Subscribing to all order updates on channel: ${channelName}`)

    channel.listen('.travel.order.created', (data: OrderUpdateEvent) => {
      console.log('[Echo] RECEIVED: travel.order.created', data)
      callback(data)
    })

    channel.listen('.travel-order.deleted', (data: OrderUpdateEvent) => {
      console.log('[Echo] RECEIVED: travel-order.deleted', data)
      callback(data)
    })

    channel.listen('.order.status.changed', (data: OrderUpdateEvent) => {
      console.log('[Echo] RECEIVED: order.status.changed (ADMIN)', data)
      callback(data)
    })
  } catch (e) {
    console.error('[Echo] Erro ao se inscrever em atualizações de todas os pedidos:', e)
  }
}
