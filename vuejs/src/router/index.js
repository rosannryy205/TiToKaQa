import { createRouter, createWebHistory } from 'vue-router';
import admin from './admin';
import client from './client';

const routes = [...admin, ...client];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  }
});

router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'TITOKAQA RESTAURANT';
  next();
});

export default router;
