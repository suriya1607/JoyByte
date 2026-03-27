import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { Toaster } from 'vue-sonner'
import 'vue-sonner/style.css'
import './style.css'
import App from './App.vue'
import router from './router'
import branding from './config/branding'

const app = createApp(App)
app.component('AppToaster', Toaster)
app.use(createPinia())
app.use(router)
document.title = `${branding.appName} - ${branding.documentTitleSuffix}`
app.mount('#app')
