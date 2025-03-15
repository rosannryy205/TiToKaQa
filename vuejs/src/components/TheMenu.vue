<template>
  <a-menu v-model:openKeys="openKeys" v-model:selectedKeys="selectedKeys" mode="inline">
    <a-menu-item key="admin-users">
      <router-link :to="{ name: 'admin-users' }">
        <span>User</span>
      </router-link>
    </a-menu-item>
    <a-menu-item key="admin-roles">
      <router-link :to="{ name: 'admin-roles' }">
        <span>Role</span>
      </router-link>
    </a-menu-item>
    <a-menu-item key="admin-settings">
      <router-link :to="{ name: 'admin-settings' }">
        <span>Setting</span>
      </router-link>
    </a-menu-item>
  </a-menu>
</template>
<script>
import { defineComponent} from 'vue'
import { storeToRefs } from 'pinia';
import { useMenu } from '@/stores/use-menu';
export default defineComponent({
  setup() {
    const stores = useMenu();
   return {
   ...storeToRefs(stores),
   };
  },
});
</script>
