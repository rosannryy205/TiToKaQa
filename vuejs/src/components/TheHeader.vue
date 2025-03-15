<template>
  <div class="container-fluild">
    <div class="row text-white" style="background-color: #0c713d; padding: 1rem;">
      <div class="col-1 d-flex d-sm-none align-items-center justify-content-center">
        <span @click="showDrawer">X</span>
      </div>
      <div class="col-10 col-sm-9 d-flex align-items-center justify-content-center justify-content-sm-start">
        <img src="../assets/logo.jpg" alt="Logo" height="30px" width="34px" style="border-radius: 30%" class="ms-3 me-3">
        <span class="d-none d-sm-flex">Quản Trị</span>
      </div>
      <div class="col-sm-3 d-none d-sm-flex align-items-center justify-content-sm-end">
        <span>Admin</span>
      </div>
      <div class="col-1 d-flex d-sm-none align-items-center justify-content-center">
        <span @click="showDrawerUser">X</span>
      </div>
    </div>
  </div>

  <a-drawer v-model:open="open" title="DANH MỤC" placement="left">
<TheMenu/>
  </a-drawer>
  <a-drawer v-model:open="openUser" title="TÀI KHOẢN" placement="right">
    <TheMenu/>
  </a-drawer>
</template>

<script setup>
import { ref } from 'vue';
import TheMenu from './TheMenu.vue';

const open = ref(false);
const openUser = ref(false);

const showDrawer = () => {
  open.value = true;
};

const showDrawerUser = () => {
  openUser.value = true;
};
</script>
