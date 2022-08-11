import { createApp, h } from 'vue'
import './style.css'
import App from './App.vue'
import router from '@/router'
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"



// 5. Create and mount the root instance.
const app = createApp(App)

app.use(router)

app.mount('#app')

// Now the app has started!