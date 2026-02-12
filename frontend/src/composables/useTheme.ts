import { ref, onMounted, onBeforeUnmount } from 'vue'

export type Theme = 'light' | 'dark' | 'system'

const THEME_STORAGE_KEY = 'app-theme'

export function useTheme() {
  const theme = ref<Theme>('system')
  const isDark = ref(false)

  const getSystemTheme = (): 'light' | 'dark' => {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }

  const applyTheme = (newTheme: Theme) => {
    const effectiveTheme = newTheme === 'system' ? getSystemTheme() : newTheme
    isDark.value = effectiveTheme === 'dark'

    if (effectiveTheme === 'dark') {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }

  const setTheme = (newTheme: Theme) => {
    theme.value = newTheme
    localStorage.setItem(THEME_STORAGE_KEY, newTheme)
    applyTheme(newTheme)
  }

  const toggleTheme = () => {
    const newTheme = isDark.value ? 'light' : 'dark'
    setTheme(newTheme)
  }

  onMounted(() => {
    const savedTheme = localStorage.getItem(THEME_STORAGE_KEY) as Theme | null
    theme.value = savedTheme || 'system'
    applyTheme(theme.value)

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const handleChange = () => {
      if (theme.value === 'system') {
        applyTheme('system')
      }
    }

    mediaQuery.addEventListener('change', handleChange)

    // Cleanup do listener quando o componente for desmontado
    onBeforeUnmount(() => {
      mediaQuery.removeEventListener('change', handleChange)
    })
  })

  return {
    theme,
    isDark,
    setTheme,
    toggleTheme,
  }
}
