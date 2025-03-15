import { createRouter, createWebHistory } from 'vue-router';
import admin from './admin';
import HomeView from '../views/index.vue'

const routes = [...admin];
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    ]
});

export default router;