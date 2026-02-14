export interface CookieOptions {
  maxAge?: number
  path?: string
  domain?: string
  secure?: boolean
  sameSite?: 'Strict' | 'Lax' | 'None'
}

export const cookieUtils = {
  set: (name: string, value: string, options: CookieOptions = {}): void => {
    const {
      maxAge = 7 * 24 * 60 * 60, // 7 dias por padrão
      path = '/',
      domain = '',
      secure = false,
      sameSite = 'Lax',
    } = options

    let cookieString = `${name}=${value}`

    if (maxAge) {
      cookieString += `; Max-Age=${maxAge}`
    }

    if (path) {
      cookieString += `; Path=${path}`
    }

    if (domain) {
      cookieString += `; Domain=${domain}`
    }

    // Para ambiente local, não usar Secure
    if (secure && window.location.protocol === 'https:') {
      cookieString += '; Secure'
    }

    if (sameSite) {
      cookieString += `; SameSite=${sameSite}`
    }

    document.cookie = cookieString
  },

  get: (name: string): string | null => {
    const nameEQ = encodeURIComponent(name) + '='
    const cookies = document.cookie.split(';')

    for (const cookie of cookies) {
      const trimmedCookie = cookie.trim()
      if (trimmedCookie.startsWith(nameEQ)) {
        return decodeURIComponent(trimmedCookie.substring(nameEQ.length))
      }
    }

    return null
  },

  remove: (name: string, path: string = '/'): void => {
    document.cookie = `${encodeURIComponent(name)}=; Max-Age=0; Path=${path}`
  },
}
