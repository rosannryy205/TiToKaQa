<template>
  <div class="client">
    <TheHeader />

    <!-- Vùng input date -->
    <div v-if="showDatePicker" class="date-picker-wrapper">
      <label>Chọn ngày đặt bàn:</label>
      <input type="date" @change="sendDateToBot" />
    </div>

    <!-- Dialogflow Chatbot -->
    <df-messenger intent="WELCOME" chat-title="ReservationBot"
      agent-id="f34540b8-b6bd-4300-b28b-74f05c2afd32" language-code="vi">
    </df-messenger>

    <TheFooter />
  </div>
</template>

<script>
import TheHeader from './Header.vue';
import TheFooter from './Footer.vue';
export default {
  name: "ChatBot",
  components: {
    TheHeader,
    TheFooter,
  },
  data() {
    return {
      showDatePicker: false,
    };
  },
  mounted() {
    const script = document.createElement('script');
    script.src = "https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1";
    script.async = true;
    document.body.appendChild(script);

    // Đợi load xong messenger mới add event listener
    window.addEventListener('df-response-received', this.handleBotResponse);
  },
  methods: {
    handleBotResponse(event) {
      const messages = event.detail.response.queryResult.fulfillmentMessages;

      // Kiểm tra nếu bot hỏi ngày đặt bàn thì hiển thị input date
      this.showDatePicker = messages.some(msg =>
        msg.text &&
        msg.text.text.some(text =>
          text.includes('ngày đặt bàn') || text.includes('ngày bạn muốn đặt')
        )
      );
    },
    sendDateToBot(event) {
      const selectedDate = event.target.value;
      const dfMessenger = document.querySelector("df-messenger");

      if (dfMessenger) {
        dfMessenger.renderCustomText(selectedDate); // Gửi ngày về bot
      }

      this.showDatePicker = false; // Ẩn lại sau khi gửi
    }
  },
  beforeUnmount() {
    window.removeEventListener('df-response-received', this.handleBotResponse);
  }
};
</script>

<style>
df-messenger {
  --df-messenger-button-titlebar-color: #cb2320;
  /* đỏ đô */
  --df-messenger-chat-background-color: #ffff;
  --df-messenger-font-color: #000;
}

.message-content {
  white-space: pre-wrap;
  /* Giữ nguyên khoảng trắng và ngắt dòng */
}
.date-picker-wrapper {
  padding: 1rem;
  background: #f5f5f5;
  margin: 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
}
</style>
