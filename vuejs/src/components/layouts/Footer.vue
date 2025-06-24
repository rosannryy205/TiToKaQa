<template>
  <div>
    <!-- Nút toggle -->
    <div class="chat-toggle-btn" @click="toggleChat">
      <span v-if="!showChat"><i class="bi bi-chat-dots"></i></span>
      <span v-else class="icon-x"><i class="bi bi-x"></i></span>
    </div>

    <!-- Chatbox -->
    <div v-if="showChat" class="chat-wrapper">
      <div class="d-flex justify-content-between">
        <div class="chat-header">
          <img src="/img/logonew.png" class="avatar" />
          <span class="fw-semibold">TIKTOKAQA</span>
        </div>
        <div class="pe-3 pt-3">
          <i class="bi bi-three-dots-vertical"></i>
        </div>
      </div>


      <div class="chat-body" ref="messageBox">
        <div v-for="(msg, index) in messages" :key="msg.id">
          <div v-if="shouldShowDate(index)" class="chat-date">
            {{ formatDateLabel(msg.created_at) }}
          </div>

          <div class="chat-message" :class="{
            'my-message':
              (user.isGuest && msg.sender_guest_id === user.id) ||
              (!user.isGuest && msg.sender_user_id === user.id),
            'other-message': !(
              (user.isGuest && msg.sender_guest_id === user.id) ||
              (!user.isGuest && msg.sender_user_id === user.id)
            ),
          }">
            <div class="text">{{ msg.message }}</div>
            <div :class="{
              time:
                (user.isGuest && msg.sender_guest_id === user.id) ||
                (!user.isGuest && msg.sender_user_id === user.id),
              time1: !(
                (user.isGuest && msg.sender_guest_id === user.id) ||
                (!user.isGuest && msg.sender_user_id === user.id)
              ),
            }">
              {{ formatTime(msg.created_at) }}
            </div>
          </div>
        </div>
      </div>

      <div class="chat-input">
        <button class="btn btn-outline-secondary me-2 rounded border" style="background-color: #fff; color: black">
          <i class="bi bi-paperclip"></i>
        </button>
        <input v-model="messageInput" @keyup.enter="sendMessage" placeholder="Nhập tin nhắn....." />
        <button @click="sendMessage" class="rounded">
          <span>➤</span>
        </button>
      </div>
    </div>
  </div>

  <footer class="bg-white pt-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 mb-4 text-start">
          <img src="/img/logonew.png" alt="logo-footer" width="150px" />
          <p class="text-start">
            Mỳ Cay TITOKAQA chuyên phục vụ các món mì cay chuẩn vị Hàn Quốc, kết hợp nguyên liệu
            tươi ngon và công thức đặc biệt.
          </p>
          <p>
            <strong style="color: #c92c3c">Email:</strong>
            <a href="mailto:support@titokaqa.com" class="text-dark fw-bold">support@titokaqa.com</a>
          </p>
          <p class="fw-bold"><strong style="color: #c92c3c">Hotline:</strong> +84 987 654 321</p>
        </div>
        <div class="col-lg-2 mb-4">
          <h5 class="fw-bold" style="color: #c92c3c">Liên Kết Nhanh</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-dark">Câu Hỏi Thường Gặp</a></li>
            <li><a href="#" class="text-dark">Điều Khoản & Điều Kiện</a></li>
            <li><a href="#" class="text-dark">Chính Sách Bảo Mật</a></li>
            <li><a href="#" class="text-dark">Liên Hệ</a></li>
          </ul>
        </div>
        <div class="col-lg-3 mb-4">
          <h5 class="fw-bold text-center" style="color: #c92c3c">Theo Dõi Chúng Tôi</h5>
          <div class="d-flex justify-content-center gap-3">
            <a href="#" class="text-dark"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-tiktok"></i></a>
            <a href="#" class="text-dark"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
        <div class="col-lg-3 mb-4">
          <h5 class="fw-bold" style="color: #c92c3c">Đăng Ký Nhận Tin</h5>
          <p>Nhận thông tin khuyến mãi và món mới từ Mỳ Cay TITOKAQA.</p>
          <div class="input-group">
            <input type="email" class="form-control" placeholder="Nhập email của bạn" />
            <button style="background-color: rgb(199, 11, 11)" class="btn btn-danger">
              Đăng Ký
            </button>
          </div>
        </div>
      </div>
      <hr class="text-secondary" />
      <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-start">
          <p class="mb-0">&copy; 2024 Mỳ Cay TITOKAQA. All Rights Reserved.</p>
        </div>
        <div class="col-lg-6 text-center text-lg-end">
          <img src="/img/visa.png" alt="Visa" class="me-2" style="height: 30px" />
          <img src="/img/mastercard.png" alt="MasterCard" class="me-2" style="height: 30px" />
          <img src="/img/momo.png" alt="Momo" style="height: 30px" />
        </div>
      </div>
    </div>
  </footer>
</template>
<script>
import Pusher from 'pusher-js'
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { nextTick } from 'vue'
import { v4 as uuidv4 } from 'uuid'
export default {
  setup() {
    const showChat = ref(false)
    const messageInput = ref('')
    const messages = ref([])
    const name = ref('')
    const messageBox = ref(null)
    const userId = ref(null)
    const user = ref(null)

    const getUser = () => {
      let local = JSON.parse(localStorage.getItem('user'))
      if (!local) {
        const randomName = 'Khách_' + Date.now()
        const userid = uuidv4()
        local = { id: userid, username: randomName, isGuest: true }
        localStorage.setItem('chat', JSON.stringify(local))
      }
      user.value = local
      name.value = local.username
      userId.value = local.id
      return local
    }

    const toggleChat = () => {
      showChat.value = !showChat.value
      if (showChat.value) getMessages()
    }

    const sendMessage = async () => {
      let currentUser = getUser()
      const messageContent = messageInput.value.trim()
      if (messageContent === '') return

      const conversationId = localStorage.getItem('conversation_id')

      const tempId = Date.now() * -1
      const tempMessage = {
        id: tempId,
        sender_guest_id: currentUser.isGuest ? currentUser.id : null,
        sender_user_id: !currentUser.isGuest ? currentUser.id : null,
        message: messageContent,
        created_at: new Date().toISOString(),
      }

      messages.value.push(tempMessage)
      scrollToBottom()
      messageInput.value = ''

      try {
        const res = await axios.post(
          'http://127.0.0.1:8000/api/messages/send',
          {
            sender_id: currentUser.id,
            sender_name: currentUser.username,
            is_guest: currentUser.isGuest,
            message: messageContent,
            conversation_id: conversationId,
          },
          {
            headers: {
              'X-Socket-ID': socketId,
            },
          },
        )

        const index = messages.value.findIndex((msg) => msg.id === tempId)
        if (index !== -1) {
          messages.value.splice(index, 1, { ...res.data })
        }

        if (res.data.sender_id && res.data.sender_id !== currentUser.id) {
          let updatedUser = { ...currentUser }
          updatedUser.id = res.data.sender_id
          if (typeof res.data.is_guest !== 'undefined') {
            updatedUser.isGuest = res.data.is_guest
          }

          localStorage.setItem('user', JSON.stringify(updatedUser))
          user.value = updatedUser
          userId.value = updatedUser.id // Cập nhật userId.value
        }

        if (res.data.conversation_id && res.data.conversation_id !== conversationId) {
          localStorage.setItem('conversation_id', res.data.conversation_id)
        }
      } catch (error) {
        console.error('lỗi khi gửi tin:', error)
        const index = messages.value.findIndex((msg) => msg.id === tempId)
        if (index !== -1) {
          messages.value.splice(index, 1)
        }
        alert('Không thể gửi tin nhắn. Vui lòng thử lại.')
      }
    }

    const getMessages = async () => {
      await getUser()
      const conversationId = localStorage.getItem('conversation_id')
      if (!conversationId) return

      try {
        const res = await axios.get('http://127.0.0.1:8000/api/messages', {
          params: {
            conversation_id: conversationId,
          },
        })
        messages.value = res.data
        scrollToBottom()
      } catch (error) {
        console.log(error)
      }
    }

    const scrollToBottom = () => {
      nextTick(() => {
        const el = messageBox.value
        if (el) el.scrollTop = el.scrollHeight
      })
    }

    const formatTime = (datetime) => {
      if (!datetime) return ''
      const date = new Date(datetime)
      if (isNaN(date)) return ''
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }

    const formatDateLabel = (datetime) => {
      const date = new Date(datetime)
      const now = new Date()

      const isSameDay = (d1, d2) =>
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()

      const yesterday = new Date()
      yesterday.setDate(now.getDate() - 1)

      if (isSameDay(date, now)) return 'Hôm nay'
      if (isSameDay(date, yesterday)) return 'Hôm qua'

      return date.toLocaleDateString('vi-VN', {
        weekday: 'long',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
      })
    }

    const shouldShowDate = (index) => {
      if (index === 0) return true
      const prev = new Date(messages.value[index - 1].created_at)
      const current = new Date(messages.value[index].created_at)
      return (
        prev.getFullYear() !== current.getFullYear() ||
        prev.getMonth() !== current.getMonth() ||
        prev.getDate() !== current.getDate()
      )
    }

    let pusher
    let socketId = ''

    function setupPusher() {
      pusher = new Pusher('332ba71732c3fee91421', {
        cluster: 'ap1',
      })

      pusher.connection.bind('connected', () => {
        socketId = pusher.connection.socket_id
      })

      const channel = pusher.subscribe('chat')

      channel.bind('App\\Events\\MessageSent', (data) => {
        getMessages()

        const currentConversationId = localStorage.getItem('conversation_id')
        if (!currentConversationId) return

        if (data.message.conversation_id === currentConversationId) {
          const exists = messages.value.some((msg) => msg.id === data.message.id)
          if (!exists) {
            messages.value.push(data.message)
            scrollToBottom()
          }
        }
      })
    }

    onMounted(async () => {
      await getUser()
      await getMessages()
      setupPusher()
    })

    return {
      showChat,
      toggleChat,
      sendMessage,
      getMessages,
      messageInput,
      messages,
      name,
      formatTime,
      messageBox,
      userId,
      setupPusher,
      user,
      scrollToBottom,
      shouldShowDate,
      formatDateLabel,
    }
  },
}
</script>

<style scoped>
.icon-x {
  color: #ffffff;
}

.chat-toggle-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #cc2c40;
  /* màu tím giống ảnh */
  color: white;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 22px;
  cursor: pointer;
  z-index: 9999;
}

.chat-wrapper {
  position: fixed;
  bottom: 90px;
  right: 24px;
  z-index: 9998;
}

.chat-wrapper {
  width: 360px;
  height: 500px;
  border-radius: 10px;
  box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  font-family: 'Segoe UI', sans-serif;
}

.chat-header {
  background: #fff;
  color: #cc2c40;
  padding: 12px 16px;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-header .avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.chat-body {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
  background-color: #f8f8f8;
  border-left: 1px solid #ffffff;
  border-right: 1px solid #ffffff;
}

.chat-date {
  text-align: center;
  color: gray;
  font-size: 12px;
  margin-bottom: 8px;
}

.chat-message {
  max-width: 70%;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.4;
  word-break: break-word;
  display: block;
  position: relative;
}

.my-message {
  background: #cc2c40;
  color: #fff;
  align-self: flex-end;
  margin-left: auto;
  display: table;
  border-bottom-right-radius: 0;
}

.other-message {
  background: #ececee;
  color: #000;
  align-self: flex-start;
  margin-right: auto;
  border-bottom-left-radius: 0;
  display: table;
}

.chat-message .text {
  white-space: pre-wrap;
}

.chat-message .time {
  font-size: 10px;
  opacity: 0.7;
  margin-top: 4px;
  color: inherit;
  text-align: right;
}

.chat-message .time1 {
  font-size: 10px;
  opacity: 0.7;
  margin-top: 4px;
  text-align: left;
  color: inherit;
}

.chat-input {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ddd;
  background: white;
}

.chat-input input {
  flex: 1;
  border: none;
  padding: 10px;
  background: #f0f0f0;
  outline: none;
}

.chat-input button {
  background: #cc2c40;
  border: none;
  color: white;
  padding: 0 16px;
  margin-left: 8px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 18px;
}
</style>
