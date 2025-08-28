<template>
  <div id="app">
    <div v-for="(msg, index) in messages" :key="index">
      <p :class="msg.role">{{ msg.content }}</p>
    </div>
    <input v-model="message" @keyup.enter="sendMessage" placeholder="Hỏi gì đi..." />
  </div>
</template>

<script>
import axios from 'axios';
import { API_URL } from '@/config';

export default {
  data() {
    return {
      message: '',
      messages: [],
    };
  },
  methods: {
    async sendMessage() {
  if (!this.message) return;

  this.messages.push({ role: 'user', content: this.message });

  try {
    const res = await axios.post(`${API_URL}/chatbot`, { message: this.message });
    this.messages.push({ role: 'bot', content: res.data.reply }); // Đúng key trả về từ API
  } catch (error) {
    console.error('Lỗi:', error);
    this.messages.push({ role: 'bot', content: 'Lỗi khi gửi tin nhắn.' });
  }

  this.message = '';
}

  },
};
</script>

<style>
.user {
  color: blue;
}
.bot {
  color: green;
}
</style>
