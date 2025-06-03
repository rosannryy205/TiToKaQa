<template>
    <a-layout style="min-height: 100vh">
      <AppSidebar :collapsed="collapsed" />
      <a-layout>
        <AppHeader :collapsed="collapsed" @toggle-collapse="toggleCollapse" />
        <a-layout-content style="margin: 0 16px">
          <a-breadcrumb style="margin: 16px 0">
            <a-breadcrumb-item v-for="item in breadcrumbItems" :key="item.path">
              <router-link v-if="item.path" :to="item.path">{{ item.name }}</router-link>
              <span v-else>{{ item.name }}</span>
            </a-breadcrumb-item>
          </a-breadcrumb>
          <div :style="{ padding: '24px', background: 'var(--background-light)', minHeight: '360px' }">
            <router-view /> </div>
        </a-layout-content>
        <AppFooter />
      </a-layout>
    </a-layout>
  </template>

  <script setup>
  import { ref, watch, computed } from 'vue';
  import { useRoute } from 'vue-router';
  import AppSidebar from '../components/AppSidebar.vue';
  import AppHeader from '../components/AppHeader.vue';
  import AppFooter from '../components/AppFooter.vue';

  const collapsed = ref(false);

  const toggleCollapse = () => {
    collapsed.value = !collapsed.value;
  };

  // Breadcrumb logic
  const route = useRoute();
  const breadcrumbItems = computed(() => {
    const matchedRoutes = route.matched;
    let path = '';
    const crumbs = [{ name: 'Admin', path: '/admin/dashboard' }]; // Trang chủ admin

    matchedRoutes.forEach(r => {
      if (r.meta && r.meta.breadcrumb) {
        // Nếu route có meta.breadcrumb là một function, gọi nó
        if (typeof r.meta.breadcrumb === 'function') {
          const dynamicBreadcrumb = r.meta.breadcrumb(route);
          if (dynamicBreadcrumb) {
               crumbs.push({ name: dynamicBreadcrumb, path: r.path });
          }
        } else {
          // Nếu là string tĩnh
           crumbs.push({ name: r.meta.breadcrumb, path: r.path });
        }
      } else if (r.name && r.path !== '/admin') { // Bỏ qua route cha /admin
          // Tự động tạo breadcrumb từ tên route (tùy chỉnh nếu cần)
          // crumbs.push({ name: r.name.toString(), path: r.path });
      }
    });
    // Loại bỏ các breadcrumb trùng lặp hoặc không cần thiết (ví dụ: nếu /admin đã có)
    // Trong trường hợp này, chúng ta có thể muốn một logic đơn giản hơn hoặc cấu hình rõ ràng hơn trong router meta
    return crumbs.filter((crumb, index, self) =>
      index === self.findIndex((c) => (
        c.path === crumb.path && c.name === crumb.name
      ))
    );
  });

  </script>

  <style scoped>
  /* CSS riêng cho AdminLayout nếu cần */
  .ant-layout-content > div {
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.09);
  }
  </style>
