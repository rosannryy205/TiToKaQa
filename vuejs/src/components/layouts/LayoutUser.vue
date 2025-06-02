<template>
    <div class="client">
      <TheHeader />
      <TheFooter />
    </div>
  </template>
  <script>
import TheHeader from './Header.vue';
import TheFooter from './Footer.vue';
  export default {
    components: {
        TheHeader,
        TheFooter,
    },
  }
  </script>

