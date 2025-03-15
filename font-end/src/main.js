

// import { createApp } from 'vue'
// import App from './App.vue'
// import router from './router'

// const app = createApp(App)

// app.use(router)

// app.mount('#app')


import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

import './assets/main.css'


// Thêm dòng này để import Font Awesome 
import "@fortawesome/fontawesome-free/css/all.min.css"

const app = createApp(App)

app.use(router)

app.mount('#app')

