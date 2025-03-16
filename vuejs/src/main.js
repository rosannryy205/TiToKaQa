
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import { 
        Menu,
        List,
        Drawer,
        Button, 
        message
 } from 'ant-design-vue';
import App from './App.vue'


// import 'ant-design-vue/dist/antd.css';
import 'ant-design-vue/dist/reset.css';
import "bootstrap-icons/font/bootstrap-icons.css";
import 'bootstrap/dist/css/bootstrap-grid.min.css'
import 'bootstrap/dist/css/bootstrap-utilities.min.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import "bootstrap/dist/js/bootstrap.bundle.min.js";

import './assets/style.css'; // Import CSS toàn cục



// Thêm dòng này để import Font Awesome 
import "@fortawesome/fontawesome-free/css/all.min.css"





const app = createApp(App);
const pinia = createPinia();
app.use(pinia)
app.use(router);
app.use(Button);
app.use(Drawer);
app.use(List);
app.use(Menu);
app.config.globalProperties.$message = message;
app.mount('#app');
