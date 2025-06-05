<template>
  <a-layout style="min-height: 100vh">
    <!-- Sidebar -->
    <AppSidebar
      :collapsed="collapsed"
      :is-mobile="isMobile"
      @toggle-collapse="toggleCollapse"
    />

    <a-layout :class="{ 'mobile-layout': isMobile }">
      <!-- Header -->
      <AppHeader
        :collapsed="collapsed"
        :is-mobile="isMobile"
        @toggle-collapse="toggleCollapse"
      />

      <!-- Content -->
      <a-layout-content style="margin: 0 16px">
        <a-breadcrumb style="margin: 16px 0">
          <a-breadcrumb-item v-for="item in breadcrumbItems" :key="item.path || item.name">
            <router-link v-if="item.path" :to="item.path">{{ item.name }}</router-link>
            <span v-else>{{ item.name }}</span>
          </a-breadcrumb-item>
        </a-breadcrumb>

        <div class="site-layout-background">
          <router-view />
        </div>
      </a-layout-content>

      <!-- Footer -->
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

// Sidebar toggle
const toggleCollapse = () => {
  collapsed.value = !collapsed.value;
};

// Handle responsive
const handleResize = () => {
  const mobile = window.innerWidth < 768;
  isMobile.value = mobile;
  collapsed.value = mobile;
};

onMounted(() => {
  handleResize();
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});

// Breadcrumb logic
const route = useRoute();
const breadcrumbItems = computed(() => {
  const crumbs = [{ name: 'Admin', path: '/admin/dashboard' }];
  route.matched.forEach(r => {
    if (r.meta?.breadcrumb) {
      const name = typeof r.meta.breadcrumb === 'function'
        ? r.meta.breadcrumb(route)
        : r.meta.breadcrumb;
      if (name) crumbs.push({ name, path: r.path });
    }
  });

  return crumbs.filter((crumb, index, self) =>
    index === self.findIndex(c => c.name === crumb.name && c.path === crumb.path)
  );
});
</script>

<style scoped>
.site-layout-background {
  padding: 24px;
  background: var(--background-light, #fff);
  min-height: 360px;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.09);
}

@media (max-width: 768px) {
  .mobile-layout {
    margin-left: 0 !important;
  }
}

</style>
