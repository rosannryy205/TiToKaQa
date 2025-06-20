<template>
  <div class="chat-container">
    <!-- Sidebar -->
    <div class="chat-list">
      <!-- Tabs for filtering -->

      <div class="p-3">
        <input type="text" class="form-control" placeholder="Search" />
      </div>
      <ul class="nav px-3 gap-2 small border-bottom" id="chat-tabs">
        <li class="nav-item" v-for="status in ['Chờ xử lý', 'Đang xử lý', 'Đã xử lý']" :key="status">
          <a class="nav-link" :class="{
            active: selectedStatus === status,
            'text-secondary': selectedStatus !== status,
          }" @click.prevent="() => changeStatus(status)">
            {{ status }}
          </a>
        </li>
      </ul>

      <div
        class="chat-list-item d-flex justify-content-between align-items-center px-3 py-2 border-bottom position-relative"
        v-for="conversation in conversations" :key="conversation.id" @click="selectConversation(conversation)">
        <!-- Bên trái -->
        <div class="d-flex align-items-center gap-2">
          <div class="position-relative">
            <img :src="conversation.sender_avatar" class="user-avatar" />
          </div>
          <div>
            <strong class="d-block fw-normal">{{ conversation.sender_name }}</strong>
            <small :class="{
              'text-muted': conversation.is_read === 1,
              'fw-bold': conversation.is_read === 0,
            }">
              {{ conversation.last_message.message }}
            </small>
          </div>
        </div>

        <!-- Bên phải -->
        <div class="text-muted small text-nowrap d-flex align-items-center gap-1">
          {{ formatTime(conversation.last_message.updated_at) }}
          <span v-if="conversation.is_read == 0" title="Chưa đọc"><i
              class="bi bi-bell-fill text-danger fs-5"></i></span>
        </div>
      </div>
    </div>

    <div class="chat-content" v-if="selectedConversation">
      <!-- Header -->
      <div class="chat-header">
        <img :src="selectedConversation.sender_avatar" class="user-avatar" />
        <div>
          <strong>{{ selectedConversation.sender_name }}</strong><br />
        </div>
      </div>

      <!-- Chat messages -->
      <div class="chat-messages d-flex flex-column position-relative" ref="messageBox">
        <div v-if="isLoading" class="d-flex justify-content-center align-items-center" style="height: 100%">
          <div class="spinner-border text-secondary" role="status" style="width: 2rem; height: 2rem">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        <template v-else>
          <div v-for="msg in messages" :key="msg.id" class="d-flex flex-column">
            <div class="small text-muted my-1" :class="(user.isGuest && msg.sender_guest_id === user.id) || (!user.isGuest && msg.sender_user_id === user.id)
              ? 'text-end me-3'
              : 'text-start ms-3'">
              {{ formatTime(msg.created_at) }}
            </div>
            <div class="message" :class="(user.isGuest && msg.sender_guest_id === user.id) || (!user.isGuest && msg.sender_user_id === user.id)
              ? 'my-message'
              : 'other-message'">
              {{ msg.message }}
            </div>
          </div>

        </template>
      </div>

      <!-- Input -->
      <div class="chat-input d-flex align-items-center">
        <button class="btn btn-outline-secondary me-2">
          <i class="bi bi-paperclip"></i>
        </button>

        <input type="text" class="form-control rounded" placeholder="Nhập tin nhắn tại đây..." v-model="messageInput"
          @keyup.enter="sendMessage" />

        <button class="btn btn-send ms-2" @click="sendMessage">➤</button>
      </div>
    </div>
    <div class="chat-header text-muted text-center py-3" v-else>
      <em>Chọn một cuộc hội thoại để bắt đầu</em>
    </div>
  </div>
</template>
<script>
import Pusher from 'pusher-js'
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { nextTick } from 'vue'

export default {
  setup() {
    const conversations = ref([])
    const user = JSON.parse(localStorage.getItem('user'))
    const selectedStatus = ref('Đang xử lý')
    const selectedConversation = ref(null)
    const messages = ref([])
    const messageBox = ref(null)
    const messageInput = ref('')
    const received = ref(null)
    const isGuestReceiver = ref(false)
    let socketId = ''

    const formatTime = (dateStr) => {
      if (!dateStr) return ''
      const date = new Date(dateStr)
      return date.toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit',
      })
    }

    const sendMessage = async () => {
      const messageContent = messageInput.value.trim()
      if (messageContent === '') return

      const tempId = Date.now() * -1 // ID âm cho tin nhắn tạm thời
      const tempMessage = {
        id: tempId,
        receiver_id: received.value,
        sender_name: user.username,
        message: messageContent,
        created_at: new Date().toISOString(),
        isTemp: true,
        sender_guest_id: user.isGuest ? user.id : null, // Nếu user hiện tại là khách, gán ID vào sender_guest_id
        sender_user_id: !user.isGuest ? user.id : null, // Nếu user hiện tại là admin, gán ID vào sender_user_id
      }

      messages.value.push(tempMessage)
      scrollToBottom()
      messageInput.value = ''

      try {
        const res = await axios.post(
          'http://127.0.0.1:8000/api/messages/send',
          {
            sender_id: user.id,
            sender_name: user.username,
            is_guest: user.isGuest,
            conversation_id: selectedConversation.value.id,
            message: messageContent,
            receiver_user_id: isGuestReceiver.value ? null : received.value,
            receiver_guest_id: isGuestReceiver.value ? received.value : null,
          },
          {
            headers: {
              'X-Socket-ID': socketId,
            },
          },
        )
      } catch (error) {
        console.error('lỗi khi gửi tin:', error)
        const index = messages.value.findIndex((msg) => msg.id === tempId)
        if (index !== -1) {
          messages.value.splice(index, 1)
        }
        alert('Không thể gửi tin nhắn. Vui lòng thử lại.')
      }
    }

    const getConversation = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/conversations', {
          params: {
            status: selectedStatus.value,
            receiver_id: selectedStatus.value !== 'Chờ xử lý' ? user.id : null,
          },
        })
        conversations.value = res.data
      } catch (error) {
        console.error('Lỗi khi lấy danh sách cuộc hội thoại:', error);
      }
    }

    const changeStatus = (status) => {
      selectedStatus.value = status
      getConversation()
    }

    const isLoading = ref(false)

    const selectConversation = async (conversation) => {
      isLoading.value = true
      selectedConversation.value = conversation

      const isSender = user.id === conversation.sender_user_id
      const isReceiver = user.id === conversation.receiver_user_id

      if (isSender) {
        received.value = conversation.receiver_guest_id || conversation.receiver_user_id
        isGuestReceiver.value = conversation.receiver_guest_id !== null
      } else if (isReceiver) {
        received.value = conversation.sender_guest_id || conversation.sender_user_id
        isGuestReceiver.value = conversation.sender_guest_id !== null
      } else {
        received.value = null
        isGuestReceiver.value = null
      }

      try {
        await axios.put(`http://127.0.0.1:8000/api/mark-read/${conversation.id}`)
        const currentConversationInList = conversations.value.find(c => c.id === conversation.id);
        if (currentConversationInList) {
          currentConversationInList.is_read = 1; // Đánh dấu là đã đọc
        }

        await axios.put(`http://127.0.0.1:8000/api/assign-conversation-admin/${conversation.id}`, {
          receiver_id: user.id,
        })

        await getConversation()

        const res1 = await axios.get('http://127.0.0.1:8000/api/messages', {
          params: { conversation_id: conversation.id },
        })
        messages.value = res1.data
      } catch (error) {
        console.error('Lỗi khi chọn cuộc hội thoại:', error);
      } finally {
        isLoading.value = false
        scrollToBottom()
      }
    }

    const scrollToBottom = () => {
      nextTick(() => {
        const el = messageBox.value
        if (el) {
          el.scrollTo({
            top: el.scrollHeight,
            behavior: 'smooth',
          })
        }
      })
    }

    onMounted(() => {
      getConversation()

      const pusher = new Pusher('332ba71732c3fee91421', {
        cluster: 'ap1',
      })

      socketId = pusher.connection.socket_id

      const channel = pusher.subscribe('chat')
      channel.bind('App\\Events\\MessageSent', async (data) => {
        await getConversation()

        if (
          selectedConversation.value &&
          data.message.conversation_id === selectedConversation.value.id
        ) {
          const tempMessageIndex = messages.value.findIndex(
            (msg) => msg.isTemp &&
              msg.message === data.message.message &&
              ((user.isGuest && msg.sender_id === data.message.sender_guest_id) ||
                (!user.isGuest && msg.sender_id === data.message.sender_user_id))
          );

          if (tempMessageIndex !== -1) {
            messages.value.splice(tempMessageIndex, 1, data.message);
          } else {
            messages.value.push(data.message);
          }

          scrollToBottom()

          try {
            const res = await axios.put(
              `http://127.0.0.1:8000/api/mark-read/${data.message.conversation_id}`,
            )
            const current = conversations.value.find((c) => c.id === data.message.conversation_id)
            if (current) {
              Object.assign(current, res.data.conversation)
            }
          } catch (err) {
            console.log(err)
          }
        }
      })
    })

    return {
      conversations,
      formatTime,
      user,
      selectedStatus,
      changeStatus,
      selectConversation,
      messages,
      messageBox,
      scrollToBottom,
      isLoading,
      selectedConversation,
      messageInput,
      sendMessage,
      received,
      isGuestReceiver,
    }
  },
}
</script>
<style scoped>
/* ============ Khung chính ============ */
.chat-container {
  height: 500px;
  display: flex;
  border: 1px solid #ddd;
}

/* ============ Sidebar cuộc hội thoại ============ */
.chat-list {
  width: 30%;
  background-color: #fff;
  border-right: 1px solid #ddd;
  overflow-y: auto;
}

.chat-list-item {
  padding: 15px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
}

.chat-list-item:hover {
  background-color: #f0f0f0;
}

/* ============ Nội dung chat ============ */
.chat-content {
  width: 70%;
  display: flex;
  flex-direction: column;
}

/* Header của khung chat */
.chat-header {
  padding: 15px;
  background-color: #fff;
  border-bottom: 1px solid #ddd;
  display: flex;
  align-items: center;
  gap: 10px;
}

/* ============ Tin nhắn ============ */
.chat-messages {
  flex-grow: 1;
  padding: 20px;
  overflow-y: auto;
  background-color: #fafafa;
  display: flex;
  flex-direction: column;
  scroll-behavior: smooth;
}

/* Tin nhắn chung */
.message {
  max-width: 75%;
  padding: 10px 15px;
  margin-bottom: 10px;
  word-break: break-word;
}

/* Tin nhắn của mình */
.message.my-message {
  background-color: #cc2c40;
  color: #fff;
  align-self: flex-end;
  border-radius: 10px 10px 0px 10px;
}

/* Tin nhắn của người khác */
.message.other-message {
  background-color: #ececec;
  color: #000;
  align-self: flex-start;
  border-radius: 10px 10px 10px 0px;
}

/* ============ Input tin nhắn ============ */
.chat-input {
  padding: 15px;
  background-color: #fff;
  border-top: 1px solid #ddd;
  display: flex;
  align-items: center;
}

.chat-input input {
  padding: 10px 15px;
  flex-grow: 1;
}

.chat-input button {
  margin-left: 10px;
}

.btn-send {
  padding: 10px 20px;
  color: #fff;
  background-color: #cc2c40;
}

/* ============ Avatar ============ */
.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
}

/* Dot thông báo chưa đọc */
.unread-dot {
  width: 10px;
  height: 10px;
  display: inline-block;
}

/* ============ Tab menu ============ */
.nav-link.active {
  font-weight: bold;
  color: #cc2c40 !important;
  border-bottom: 2px solid #cc2c40;
}
</style>
