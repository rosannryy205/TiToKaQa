import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import {
  Menu,
  List,
  Drawer,
  Button,
  message,
  Image
} from 'ant-design-vue'
import App from './App.vue'
import 'leaflet/dist/leaflet.css';
import vSelect from 'vue-select'


import Antd from 'ant-design-vue';

// CSS import
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap/dist/css/bootstrap-grid.min.css'
import 'bootstrap/dist/css/bootstrap-utilities.min.css'
import "vue-select/dist/vue-select.css";
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'vue3-toastify/dist/index.css';
import 'sweetalert2/dist/sweetalert2.min.css'

import './assets/style.css';
import './assets/cart.css';
import './assets/base.css'

import "@fortawesome/fontawesome-free/css/all.min.css"
import 'vue3-toastify/dist/index.css';

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)
app.use(Button)
app.use(Drawer)
app.use(List)
app.use(Menu)
app.use(Image)
app.use(Antd);
app.component('v-select', vSelect)
app.config.globalProperties.$message = message

app.mount('#app')
