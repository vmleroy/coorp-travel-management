import './globals.css'
import 'primeicons/primeicons.css'

import { createApp } from 'vue'
import App from './App.vue'

import { createPinia } from 'pinia'
import { router } from './router.ts'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'

const app = createApp(App)

app.use(createPinia())

app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      cssLayer: {
        name: 'primevue',
        order: 'theme, base, primevue',
      },
    },
  },
})

app.use(router)

app.mount('#app')
