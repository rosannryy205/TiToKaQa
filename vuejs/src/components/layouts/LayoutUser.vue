<template>
  <div class="client">
    <TheHeader />
    <df-messenger intent="WELCOME" chat-title="ReservationBot" agent-id="f34540b8-b6bd-4300-b28b-74f05c2afd32"
      language-code="vi"></df-messenger>
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
  name: "ChatBot",
  mounted() {
    const script = document.createElement('script');
    script.src = "https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1";
    script.async = true;
    document.body.appendChild(script);
  }
}
</script>
<style>
df-messenger {
  --df-messenger-button-titlebar-color: #cb2320; /* đỏ đô */
  --df-messenger-chat-background-color: #ffff;
  --df-messenger-font-color: #000;
}
.message-content {
    white-space: pre-wrap; /* Giữ nguyên khoảng trắng và ngắt dòng */
}
</style>
