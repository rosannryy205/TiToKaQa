<template>
  <div class="client">
    <TheHeader />
    <Chatbot />
    <TheFooter />
  </div>
</template>


<script>
import TheHeader from './Header.vue';
import TheFooter from './Footer.vue';
import Chatbot from './Chatbot.vue';

export default {
  components: {
    TheHeader,
    TheFooter,
    Chatbot,
  },

}

</script>

<style></style>
