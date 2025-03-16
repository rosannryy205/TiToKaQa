import { createRouter, createWebHistory } from 'vue-router';
import admin from './admin';
import client from './client';

const routes = [...admin, ...client];
const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;