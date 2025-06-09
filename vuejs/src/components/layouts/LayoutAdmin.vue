<template>
  <a-layout style="min-height: 100vh">
    <AppSidebar :collapsed="collapsed" :is-mobile="isMobile" @toggle-collapse="toggleCollapse" />
    <a-layout>
      <AppHeader :collapsed="collapsed" :is-mobile="isMobile" @toggle-collapse="toggleCollapse" />
      <a-layout-content style="margin: 0 16px">
        <a-breadcrumb style="margin: 16px 0">
          <a-breadcrumb-item v-for="item in breadcrumbItems" :key="item.path">
            <router-link v-if="item.path" :to="item.path">{{ item.name }}</router-link>
            <span v-else>{{ item.name }}</span>
          </a-breadcrumb-item>
        </a-breadcrumb>
        <div :style="{ padding: '24px', background: 'var(--background-light)', minHeight: '360px' }">
          <router-view />
        </div>
      </a-layout-content>
      <AppFooter />
    </a-layout>
  </a-layout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import AppSidebar from '../TheSidebar.vue';
import AppHeader from '../TheHeader.vue';
import AppFooter from '../TheFooter.vue';

const collapsed = ref(false);
const isMobile = ref(false);

const toggleCollapse = () => {
  collapsed.value = !collapsed.value;
};

const handleResize = () => {
  isMobile.value = window.innerWidth < 768;
  if (isMobile.value) collapsed.value = true;
};

onMounted(() => {
  handleResize();
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});

const route = useRoute();
const breadcrumbItems = computed(() => {
  const matchedRoutes = route.matched;
  let path = '';
  const crumbs = [{ name: 'Admin', path: '/admin/dashboard' }];

  matchedRoutes.forEach(r => {
    if (r.meta && r.meta.breadcrumb) {
      if (typeof r.meta.breadcrumb === 'function') {
        const dynamicBreadcrumb = r.meta.breadcrumb(route);
        if (dynamicBreadcrumb) {
          crumbs.push({ name: dynamicBreadcrumb, path: r.path });
        }
      } else {
        crumbs.push({ name: r.meta.breadcrumb, path: r.path });
      }
    }
  });

  return crumbs.filter((crumb, index, self) =>
    index === self.findIndex((c) => (
      c.path === crumb.path && c.name === crumb.name
    ))
  );
});
</script>

<style scoped>
.ant-layout-content > div {
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.09);
}
</style>
