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

    const token = localStorage.getItem('token');
    const userString = localStorage.getItem('user');

    let user = null;
    user = JSON.parse(userString);

    if (to.meta.requiresAdmin) {
        if (token && user && user.role && ['quanly', 'nhanvien', 'nhanvienkho'].includes(user.role)) {
            next();
        } else {
            next('/');
        }
    } else {
        next();
    }
});

export default router;
