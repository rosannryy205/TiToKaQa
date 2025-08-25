<template>
  <div>
    <div class="chat-toggle-btn" @click="toggleChat">
      <span v-if="!showChat"><i class="bi bi-chat-dots"></i></span>
      <span v-else class="icon-x"><i class="bi bi-x"></i></span>
    </div>

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
            'my-message': msg.sender_type === 'user',
            'other-message': msg.sender_type === 'bot',
          }">
            <div class="text" v-if="msg.message">{{ msg.message }}</div>

            <div v-if="msg.cartSummaryData" class="cart-summary-container mt-2">
              <div class="list-product-scroll1">
                <div v-for="(item, itemIndex) in msg.cartSummaryData.items" :key="itemIndex"
                  class="d-flex cart-item-display">
                  <div class="flex-grow-1">
                    <div style="font-size: 12px;">{{ item.name }} - x{{ item.quantity }}</div>
                    <div class="text-muted small" style="font-size: 10px;">{{ item.spicyLevel }}</div>
                    <div v-if="item.toppings && item.toppings.length" class="text-muted small" style="font-size: 10px;">
                      <div v-for="(topping, i) in item.toppings" :key="i">
                        {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ
                      </div>
                    </div>

                    <div class="" style="font-size: 11px;">
                      <strong>{{ formatNumber(item.totalItemPrice) }} VNĐ</strong>
                    </div>
                    <hr class="my-1" />
                  </div>

                </div>
              </div>
              <div class="fw-semibold">
                Tổng cộng: {{ formatNumber(msg.cartSummaryData.total) }} VNĐ
              </div>

              <div class="chips-container">
                <button @click="sendMessage('✅ Hoàn tất chọn món')" class="chip-button">
                  ✅ Hoàn tất chọn món
                </button>
                <button @click="sendMessage('📋 Xem thực đơn')" class="chip-button">
                  📋 Xem thực đơn
                </button>
              </div>

            </div>

            <div v-if="msg.richContent && msg.richContent.length > 0">
              <div v-for="(contentBlock, blockIndex) in msg.richContent" :key="'block-' + blockIndex"
                class="rich-content-block">
                <div v-for="(item, itemIndex) in contentBlock" :key="'item-' + itemIndex">
                  <div v-if="item.type === 'chips' && item.options" class="chips-container">
                    <button v-for="(option, optionIndex) in item.options" :key="'chip-' + optionIndex"
                      @click="sendMessage(option.text)" class="chip-button">
                      {{ option.text }}
                    </button>
                  </div>

                  <div v-if="item.type === 'carousel' && item.cards" class="carousel-container">
                    <div v-for="(card, cardIndex) in item.cards" :key="'card-' + cardIndex" class="food-card">
                      <div class="card-content">
                        <div class="card-title">{{ card.title }}</div>
                        <div class="card-subtitle">{{ card.subtitle }}</div>
                        <div v-if="card.buttons && card.buttons.length > 0" class="card-buttons">
                          <button v-for="(button, buttonIndex) in card.buttons" :key="'button-' + buttonIndex"
                            @click="showProductDetail(card)" class="card-button">
                            {{ button.text }}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div :class="{
              time: msg.sender_type === 'user',
              time1: msg.sender_type === 'bot',
            }">
              {{ formatTime(msg.created_at) }}
            </div>
          </div>
        </div>
        <div class="loader chat-message other-message" v-if="isLoading">
          <div class="ball"></div>
          <div class="ball"></div>
          <div class="ball"></div>
        </div>
      </div>

      <div v-if="showProductDetailInChat" class="product-detail-in-chat-container">
        <div class="product-detail-header d-flex justify-content-between align-items-center mb-2">
          <div class="title">
            <i class="bi bi-bag-plus-fill"></i>
            Thêm món
          </div>
          <button @click="hideProductDetail" class="close-btn"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="p-2 border rounded shadow-sm mb-3">
          <div class="d-flex justify-content-between">
            <div class="text-dark fw-semibold mb-2">{{ foodDetail1.name }}</div>
            <div>
              <button class="btn me-1" @click="decreaseQuantity" type="button"
                style="background-color: #c92c3c; color: #fff; border: none">
                −
              </button>
              <span class="quantity-display">{{ quantity1 }}</span>
              <button class="btn ms-1" @click="increaseQuantity" type="button"
                style="background-color: #c92c3c; color: #fff; border: none">
                +
              </button>
            </div>
          </div>

          <div class="fw-semibold" style="font-size: 14px">
            {{ formatNumber(foodDetail1.price) }}VNĐ
          </div>
        </div>
        <form @submit.prevent="handleAddToCartClick">
          <div style="max-height: 200px; overflow-y: auto" class="pe-3 ps-3"
            v-if="toppingList1.length || spicyLevel1.length">
            <div class="mb-3" v-if="spicyLevel1.length">
              <label for="spicyLevel1" class="form-label fw-bold text-danger">
                🌶 Mức độ cay:</label>
              <select class="form-select rounded" id="spicyLevel1">
                <option v-for="item in spicyLevel1" :key="item.id" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
            </div>

            <label class="form-label fw-bold text-danger">🧀 Chọn Topping:</label>
            <div v-for="topping in toppingList1" :key="topping.id"
              class="d-flex justify-content-between align-items-center mb-2">
              <label class="d-flex align-items-center text-dark">
                <input type="checkbox" :value="topping.id" name="topping[]1" class="me-2" />
                {{ topping.name }}
              </label>
              <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
            </div>
          </div>
          <div v-else class="mt-3 text-center text-muted">
            <p>Không có topping hoặc mức độ cay cho món này.</p>
          </div>

          <div class="product-detail-footer border-0 p-1">
            <button class="btn btn-danger1 fw-bold w-100" type="submit"> Thêm món
            </button>
          </div>
        </form>
      </div>
      <div class="chat-input">
        <input v-if="expectingDateInput" type="text" ref="datetimePicker" class="rounded-0" v-model="datetime"
          placeholder="Chọn ngày và giờ" :disabled="isInputDisabled" />
        <input v-else-if="expectingCountInput" type="number" min="1" max="20" v-model="messageInput"
          @keyup.enter="sendMessage" required :disabled="isInputDisabled" />
        <input v-else-if="expectingEmailInput" type="email" v-model="messageInput" @keyup.enter="sendMessage" required
          :disabled="isInputDisabled" />

        <input v-else v-model="messageInput" @keyup.enter="sendMessage" placeholder="Nhập tin nhắn....."
          :disabled="isLoading || isInputDisabled" />

        <button @click="sendMessage()" class="rounded" :disabled="isLoading || isInputDisabled">
          <span>➤</span>
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios'
import { ref, onMounted, computed, nextTick, watch } from 'vue'
import { v4 as uuidv4 } from 'uuid'
import Pusher from 'pusher-js'
import { Cart } from '@/stores/cart'
import numeral from 'numeral'
import { toast } from 'vue3-toastify'
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Vietnamese } from 'flatpickr/dist/l10n/vn.js';
export default {
  setup() {
    const {
      cartItems,
      loadCart,
      totalPriceItem,
      clearCart,
      addToCart,
      isChatbotActive,
    } = Cart()

    const showChat = ref(false)
    const messageInput = ref('')
    const messages = ref([])
    const name = ref('')
    const messageBox = ref(null)
    const userId = ref(null)
    const user = ref(null)
    const expectingDateInput = ref(false)
    const expectingCountInput = ref(false)
    const expectingEmailInput = ref(false)
    const expectingTimeOption = ref(false)
    const selectedDate = ref('')
    const today = new Date().toISOString().split('T')[0]
    const date = ref(null)
    const timeOptions = ref([])
    const time = ref('')
    const now = new Date()
    const minDatetime = ref(now.toISOString().slice(0, 16))
    const datetime = ref('')
    const isLoading = ref(false)
    const datetimePicker = ref(null);
    const isInputDisabled = ref(true);

    let fpInstance = null;
    const showProductDetailInChat = ref(false)
    const hasSentWelcome = ref(false);
    const foodDetail1 = ref({})
    const toppings1 = ref([])
    const spicyLevel1 = ref([])
    const toppingList1 = ref([])
    const quantity1 = ref(1)
    const currentOrderId = ref(null)
    const currentCartIsChatbot = computed(() => {
      return Cart().cartKey.value.includes('_chatbot')
    })

    const formatNumber = (value) => {
      return numeral(value).format('0,0')
    }

    const getImageUrl = (image) => {
      return `/img/food/${image}`
    }

    const showProductDetail = async (item) => {
      foodDetail1.value = {}
      toppings1.value = []
      spicyLevel1.value = []
      toppingList1.value = []
      quantity1.value = 1
      try {
        if (item.type === 'food') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`)
          foodDetail1.value = { ...res.data, type: 'food' }

          const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`)
          toppings1.value = res1.data

          spicyLevel1.value = toppings1.value.filter((top) => top.category_id == 15)
          toppingList1.value = toppings1.value.filter((top) => top.category_id == 16)
          toppingList1.value.forEach((top) => {
            top.price = top.price || 0
          })
        } else if (item.type === 'combo') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`)
          foodDetail1.value = { ...res.data, type: 'combo' }
        }

        showProductDetailInChat.value = true
        isChatbotActive.value = true
        loadCart()
        scrollToBottom()
      } catch (error) {
        console.error("Lỗi khi hiển thị chi tiết sản phẩm:", error)
      }
    }

    const hideProductDetail = () => {
      isChatbotActive.value = false
      loadCart()
      showProductDetailInChat.value = false
    }

    const getUser = () => {
      let local = JSON.parse(localStorage.getItem('user'))
      if (!local) {
        const randomName = 'Khách_' + Date.now()
        const userid = uuidv4()
        local = { id: userid, username: randomName, isGuest: true }
        localStorage.setItem('user', JSON.stringify(local))
      }
      user.value = local
      name.value = local.username
      userId.value = local.id
      return local
    }

    const toggleChat = () => {
      showChat.value = !showChat.value
      if (showChat.value) {
        if (!hasSentWelcome.value) {
          sendWelcomeEvent();
        }
        isInputDisabled.value = true;
        scrollToBottom()
      } else {
        isInputDisabled.value = true;
      }
    }

    const sendWelcomeEvent = async () => {

      const currentUser = getUser();
      if (!currentUser || isLoading.value || hasSentWelcome.value) {
        return;
      }

      // isLoading.value = true;
      try {
        let botMessage = {
          id: Date.now(),
          session_id: currentUser.id,
          message: '👋 Xin chào! Mình có thể giúp gì cho bạn hôm nay?',
          richContent: [
            [
              {
                "type": "chips",
                "options": [
                  { "text": "🪑 Đặt bàn" },
                  { "text": "❌ Hủy đơn", }
                ]
              }
            ]
          ],
          sender_type: 'bot',
          sender_id: 'bot',
          created_at: new Date().toISOString(),
          cartSummaryData: null,
          outputContexts: null,
        }

        messages.value.push(botMessage)
        // console.log('Bot message pushed:', botMessage)
        scrollToBottom()
        hasSentWelcome.value = true;
      } catch (error) {
        console.error('Lỗi khi gửi sự kiện chào:', error);
        isLoading.value = false;
        messages.value.push({
          id: Date.now() + 1,
          session_id: currentUser.id,
          message: 'Không thể chào đón. Vui lòng thử lại.',
          sender_type: 'bot',
          sender_id: 'bot-error',
          created_at: new Date().toISOString(),
          cartSummaryData: null,
        });
        scrollToBottom();
      }
    };

    const filteredTimeOptions = computed(() => {
      if (!date.value) return timeOptions.value
      const selectedDateObj = new Date(date.value)
      const now = new Date()
      if (
        selectedDateObj.getFullYear() === now.getFullYear() &&
        selectedDateObj.getMonth() === now.getMonth() &&
        selectedDateObj.getDate() === now.getDate()
      ) {
        return timeOptions.value.filter((timeStr) => {
          const [hours, minutes] = timeStr.split(':').map(Number)
          const timeDate = new Date(selectedDateObj)
          timeDate.setHours(hours, minutes, 0)
          return timeDate > now
        })
      }
      return timeOptions.value
    })

    const isValidEmail = (email) => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    };

    const sendMessage = async (messageFromChip = null) => {
      const currentUser = getUser()
      let messageContent = ''

      if (messageFromChip !== null && typeof messageFromChip === 'string') {
        messageContent = messageFromChip
        expectingDateInput.value = false
        expectingTimeOption.value = false
        expectingEmailInput.value = false
        expectingCountInput.value = false

        if (messageFromChip === '🪑 Đặt bàn') {
          isInputDisabled.value = false;
          messageInput.value = messageFromChip;
        } else if (messageFromChip === '✅ Hoàn tất chọn món') {
          console.log('Calling submitCart with order ID:', currentOrderId.value);
          submitCart(currentOrderId.value);
          return;
        }


      } else if (expectingDateInput.value) {
        if (!datetime.value) {
          alert('Vui lòng chọn ngày và giờ!')
          return
        }
        messageContent = new Date(datetime.value).toLocaleString('vi-VN', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        })
        datetime.value = ''
        expectingDateInput.value = false
      } else if (expectingTimeOption.value) {
        if (!time.value) {
          alert('Vui lòng chọn giờ!')
          return
        }
        messageContent = time.value
        time.value = ''
        expectingTimeOption.value = false
      } else if (expectingEmailInput.value) {
        messageContent = messageInput.value;
        if (!isValidEmail(messageContent)) {
          alert('Email không đúng định dạng. Vui lòng nhập lại.');
          messageInput.value = '';
          return;
        }
        messageInput.value = '';
        expectingEmailInput.value = false;
      } else {
        messageContent = messageInput.value
        if (messageContent === '') return
        messageInput.value = ''
      }

      if (isLoading.value) {
        return
      }

      const userMessage = {
        id: Date.now(),
        session_id: currentUser.id,
        message: messageContent,
        sender_type: 'user',
        sender_id: currentUser.id,
        created_at: new Date().toISOString(),
        cartSummaryData: null,
      }

      messages.value.push(userMessage)
      scrollToBottom()

      isLoading.value = true

      try {
        await axios.post('http://127.0.0.1:8000/api/messages/send', {
          session_id: currentUser.id,
          message: messageContent,
        })
      } catch (error) {
        console.error('Lỗi khi gửi tin nhắn:', error)
        isLoading.value = false
        messages.value.push({
          id: Date.now() + 1,
          session_id: currentUser.id,
          message: 'Không thể gửi tin nhắn. Vui lòng kiểm tra kết nối.',
          sender_type: 'bot',
          sender_id: 'bot-error',
          created_at: new Date().toISOString(),
          cartSummaryData: null,
        })
        scrollToBottom()
      }
    }

    const scrollToBottom = () => {
      nextTick(() => {
        const el = messageBox.value
        if (el) el.scrollTop = el.scrollHeight
      })
    }

    const formatTime = (datetimeStr) => {
      if (!datetimeStr) return ''
      try {
        const date = new Date(datetimeStr)
        return isNaN(date.getTime())
          ? ''
          : date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      } catch (e) {
        console.error('Lỗi định dạng thời gian:', datetimeStr, e)
        return ''
      }
    }

    const formatDateLabel = (datetimeStr) => {
      if (!datetimeStr) return ''
      try {
        const date = new Date(datetimeStr)
        if (isNaN(date.getTime())) return ''

        const now = new Date()
        const isSameDay = (d1, d2) =>
          d1.getFullYear() === d2.getFullYear() &&
          d1.getMonth() === d2.getMonth() &&
          d1.getDate() === d2.getDate()

        const yesterday = new Date(now)
        yesterday.setDate(now.getDate() - 1)

        if (isSameDay(date, now)) return 'Hôm nay'
        if (isSameDay(date, yesterday)) return 'Hôm qua'

        return date.toLocaleDateString('vi-VN', {
          weekday: 'long',
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
        })
      } catch (e) {
        console.error('Lỗi định dạng ngày:', datetimeStr, e)
        return ''
      }
    }

    const shouldShowDate = (index) => {
      if (index === 0) return true
      try {
        const prevMsg = messages.value[index - 1]
        const currentMsg = messages.value[index]

        if (!prevMsg || !currentMsg || !prevMsg.created_at || !currentMsg.created_at) {
          return false
        }

        const prevDate = new Date(prevMsg.created_at)
        const currentDate = new Date(currentMsg.created_at)

        if (isNaN(prevDate.getTime()) || isNaN(currentDate.getTime())) return false

        return (
          prevDate.getFullYear() !== currentDate.getFullYear() ||
          prevDate.getMonth() !== currentDate.getMonth() ||
          prevDate.getDate() !== currentDate.getDate()
        )
      } catch (e) {
        console.error('Lỗi khi so sánh ngày:', e)
        return false
      }
    }

    let pusherInstance = null
    let socketId = ''

    function setupPusher() {
      if (!pusherInstance) {
        pusherInstance = new Pusher('332ba71732c3fee91421', {
          cluster: 'ap1',
        })

        pusherInstance.connection.bind('connected', () => {
          socketId = pusherInstance.connection.socket_id
          console.log('Pusher connected. Socket ID:', socketId)
        })

        pusherInstance.connection.bind('disconnected', () => {
          console.log('Pusher disconnected.')
        })
      }

      const currentUser = getUser()

      if (currentUser && currentUser.id) {
        const channelName = `chat-session.${currentUser.id}`
        if (pusherInstance.channel(channelName)) {
          pusherInstance.unsubscribe(channelName)
        }
        const channel = pusherInstance.subscribe(channelName)

        channel.bind('App\\Events\\MessageSent', (e) => {
          console.log('Nhận tin nhắn bot qua Pusher:', e)
          isLoading.value = false

          const receivedData = e.messageData
          let botMessage = {
            id: receivedData.id || Date.now(),
            session_id: receivedData.session_id,
            message: receivedData.message,
            richContent: receivedData.richContent || [],
            sender_type: receivedData.sender_type || 'bot',
            sender_id: receivedData.sender_id || 'bot',
            created_at: receivedData.created_at,
            cartSummaryData: null,
            outputContexts: receivedData.outputContexts || [],
          }

          messages.value.push(botMessage)
          scrollToBottom()

          const msg = (receivedData.message || '').toLowerCase()
          expectingDateInput.value = msg.includes('ngày') || msg.includes('khi nào')
          expectingTimeOption.value = msg.includes('giờ') && !expectingDateInput.value
          expectingEmailInput.value = (receivedData.message).includes('Email của bạn luôn nè') && !expectingDateInput.value && !expectingTimeOption.value && !expectingCountInput.value
          expectingCountInput.value = msg.includes('bao nhiêu người') && !expectingDateInput.value && !expectingTimeOption.value && !expectingEmailInput.value
          const reservationContext = botMessage.outputContexts.find(ctx =>
            ctx.name && ctx.name.endsWith('/contexts/reservation-info')
          );

          console.log(reservationContext);


          if (reservationContext && reservationContext.parameters && reservationContext.parameters.order_id) {
            currentOrderId.value = reservationContext.parameters.order_id;
            scrollToBottom();
          }
        })
      } else {
        console.warn('Không có Session ID để đăng ký kênh Pusher riêng tư.')
      }
    }

    const handleAddToCartClick = async () => {
      const selectedSpicySelect = document.getElementById('spicyLevel1');
      const selectedSpicyId = parseInt(selectedSpicySelect?.value);
      const selectedSpicy = spicyLevel1.value.find((item) => item.id === selectedSpicyId);

      let allSelectedToppings = [];

      if (selectedSpicy) {
        allSelectedToppings.push({
          name: selectedSpicy.name,
          price: selectedSpicy.price,
          food_toppings_id: selectedSpicy.pivot?.id || null,
          is_spicy_level: true,
        });
      }

      const selectedToppingCheckboxes = document.querySelectorAll('input[name="topping[]1"]:checked');
      const selectedToppingIds = Array.from(selectedToppingCheckboxes).map((el) => parseInt(el.value));

      const normalToppings = toppingList1.value
        .filter((topping) => selectedToppingIds.includes(topping.id))
        .map((topping) => ({
          id: topping.food_toppings_id,
          name: topping.name,
          price: topping.price,
          food_toppings_id: topping.pivot?.id || null,
          is_spicy_level: false,
        }));

      allSelectedToppings = [...allSelectedToppings, ...normalToppings];

      addToCart(foodDetail1.value, quantity1.value, allSelectedToppings);
      showProductDetailInChat.value = false;

      await loadCart();
      await nextTick();

      const currentUser = getUser();
      let totalCartPrice = 0;
      const itemsForDisplay = [];

      cartItems.value.forEach((item) => {
        const itemPrice = Number(item.price) * item.quantity;
        let currentItemToppingPrice = 0;
        let spicyLevelName = '';
        const itemToppingsForDisplay = [];

        if (item.toppings && item.toppings.length > 0) {
          item.toppings.forEach((topping) => {
            if (topping.is_spicy_level) {
              spicyLevelName = topping.name;
            } else {
              currentItemToppingPrice += Number(topping.price) * item.quantity;
              itemToppingsForDisplay.push({
                name: topping.name,
                price: topping.price,
              });
            }
          });
        }

        const itemTotalPrice = itemPrice + currentItemToppingPrice;
        totalCartPrice += itemTotalPrice;

        itemsForDisplay.push({
          name: item.name,
          image: item.image,
          type: item.type,
          quantity: item.quantity,
          price: item.price,
          spicyLevel: spicyLevelName,
          toppings: itemToppingsForDisplay,
          totalItemPrice: itemTotalPrice,
        });
      });

      const newCartSummaryData = {
        items: itemsForDisplay,
        total: totalCartPrice,
      };

      const lastCartMessageIndex = messages.value.reduce((lastIndex, msg, index) => {
        if (msg.cartSummaryData) {
          return index;
        }
        return lastIndex;
      }, -1);

      if (lastCartMessageIndex !== -1) {
        messages.value[lastCartMessageIndex].cartSummaryData = newCartSummaryData;
        messages.value[lastCartMessageIndex].message = 'Danh sách món ăn của bạn đã được cập nhật:';
        messages.value[lastCartMessageIndex].created_at = new Date().toISOString();
      } else {
        const cartSummaryBotMessage = {
          id: Date.now() + 1,
          session_id: currentUser.id,
          message: 'Danh sách món ăn của bạn:',
          richContent: [],
          sender_type: 'bot',
          sender_id: 'bot',
          created_at: new Date().toISOString(),
          cartSummaryData: newCartSummaryData,
        };
        messages.value.push(cartSummaryBotMessage);
      }

      scrollToBottom();
    };

    // hàm update order_detail
    const submitCart = async (orderIdToSubmit) => {
      const currentUser = getUser();
      // if (!orderIdToSubmit) {
      //   toast.error('Không tìm thấy đơn hàng để cập nhật!');
      //   return;
      // }

      // if (cartItems.value.length === 0) {
      //   toast.warning('Giỏ hàng trống, không có gì để cập nhật.');
      //   return;
      // }

      try {
        const payloadDetails = cartItems.value.map((item) => {
          const type = item.type;
          const foodId = type === 'food' ? item.id : null;
          const comboId = type === 'combo' ? item.id : null;

          return {
            food_id: foodId,
            combo_id: comboId,
            quantity: item.quantity,
            type: type,
            toppings: item.toppings.map((top) => ({
              food_toppings_id: top.food_toppings_id || top.id,
            })),
          };
        });

        let calculatedTotalAmount = 0;
        cartItems.value.forEach(item => {
          let itemBasePrice = Number(item.price) * item.quantity;
          let itemToppingsPrice = 0;
          if (item.toppings && item.toppings.length > 0) {
            item.toppings.forEach(topping => {
              if (!topping.is_spicy_level) {
                itemToppingsPrice += Number(topping.price) * item.quantity;
              }
            });
          }
          calculatedTotalAmount += (itemBasePrice + itemToppingsPrice);
        });

        await axios.put(`http://127.0.0.1:8000/api/update-order-detail/${orderIdToSubmit}`, {
          details: payloadDetails,
        });

        const paymentRes = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
          order_id: orderIdToSubmit,
          amount: calculatedTotalAmount,
        });
        if (paymentRes.data && paymentRes.data.payment_url) {
          window.location.href = paymentRes.data.payment_url;
        } else {
          toast.error('Không tạo được link thanh toán VNPAY.');
        }
        // toast.success('Cập nhật đơn hàng thành công!');

        clearCart();
        currentOrderId.value = null;
        let botMessage = {
          id: Date.now() + 2,
          session_id: currentUser.id,
          message: 'Thanh toán thành công',
          richContent: null,
          sender_type: 'bot',
          sender_id: 'bot',
          created_at: new Date().toISOString(),
          cartSummaryData: null,
          outputContexts: null,
        };
        messages.value.push(botMessage);
      } catch (error) {
        console.error('Lỗi khi cập nhật đơn hàng:', error);
        if (error.response && error.response.data && error.response.data.message) {
          toast.error(`Lỗi: ${error.response.data.message}`);
        } else {
          toast.error('Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
      }
    };

    const increaseQuantity = () => {
      quantity1.value++
    }

    const decreaseQuantity = () => {
      if (quantity1.value > 1) quantity1.value--
    }


    const initializeFlatpickr = () => {
      if (datetimePicker.value && !fpInstance) {
        fpInstance = flatpickr(datetimePicker.value, {
          enableTime: true,
          noCalendar: false,
          dateFormat: "Y-m-d H:i",
          altInput: true,
          altFormat: "d/m/Y H:i",
          minDate: "today",
          minTime: "08:00",
          maxTime: "21:30",
          minuteIncrement: 30,
          locale: Vietnamese,
          disableMobile: true,

          onChange: (selectedDates, dateStr, instance) => {
            datetime.value = dateStr;
          },
          onOpen: (selectedDates, dateStr, instance) => {
            const now = new Date();
            const selectedDate = selectedDates[0] || now;

            const minTimeForSelectedDay = new Date(selectedDate);
            minTimeForSelectedDay.setHours(8, 0, 0, 0);

            const maxTimeForSelectedDay = new Date(selectedDate);
            maxTimeForSelectedDay.setHours(21, 30, 0, 0);

            let initialTime = now;

            if (selectedDate.toDateString() === now.toDateString()) {
              if (now < minTimeForSelectedDay) {
                initialTime = minTimeForSelectedDay;
              } else if (now > maxTimeForSelectedDay) {
                // Nếu đã quá 21:30, không làm gì cả, người dùng phải chọn ngày khác
              } else {
                let minutes = now.getMinutes();
                if (minutes > 30) {
                  initialTime.setMinutes(0, 0, 0);
                  initialTime.setHours(initialTime.getHours() + 1);
                } else if (minutes > 0 && minutes <= 30) {
                  initialTime.setMinutes(30, 0, 0);
                } else {
                  initialTime.setMinutes(0, 0, 0);
                }
              }
            } else if (selectedDate < now && selectedDate.toDateString() !== now.toDateString()) {
              instance.setDate(null);
              return;
            } else {
              initialTime = minTimeForSelectedDay;
            }

            if (initialTime > maxTimeForSelectedDay) {
              initialTime = maxTimeForSelectedDay;
            }

            instance.setDate(initialTime, true);
          }
        });
      }
    };

    watch(expectingDateInput, (newValue) => {
      if (newValue) {
        nextTick(() => {
          initializeFlatpickr();
        });
      } else {
        if (fpInstance) {
          fpInstance.destroy();
          fpInstance = null;
        }
      }
    });

    onMounted(async () => {
      await getUser()
      isChatbotActive.value = false
      loadCart()
      for (let hour = 8; hour <= 21; hour++) {
        let hourStr = hour < 10 ? '0' + hour : '' + hour
        timeOptions.value.push(hourStr + ':00')
        if (hour !== 20) timeOptions.value.push(hourStr + ':30')
      }

      setupPusher()
    })

    return {
      showChat,
      toggleChat,
      sendMessage,
      messageInput,
      messages,
      name,
      formatTime,
      messageBox,
      userId,
      user,
      scrollToBottom,
      shouldShowDate,
      formatDateLabel,
      expectingDateInput,
      selectedDate,
      timeOptions,
      time,
      today,
      filteredTimeOptions,
      expectingTimeOption,
      minDatetime,
      datetime,
      isLoading,
      formatNumber,
      getImageUrl,
      foodDetail1,
      spicyLevel1,
      toppingList1,
      handleAddToCartClick,
      quantity1,
      increaseQuantity,
      decreaseQuantity,
      showProductDetailInChat,
      showProductDetail,
      hideProductDetail,
      currentCartIsChatbot,
      cartItems,
      totalPriceItem,
      currentOrderId,
      submitCart,
      expectingEmailInput,
      expectingCountInput,
      datetimePicker,
      fpInstance,
      initializeFlatpickr,
      isInputDisabled
    }
  },
}
</script>
<style scoped>
.icon-x {
  color: #ffffff;
}

.product-detail-in-chat-container {
  background-color: #fff;
  border-top: 1px solid #eee;
  padding: 15px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
  /* Optional: a subtle shadow to separate it */
  animation: slideUp 0.3s ease-out;
  /* Optional: subtle animation */
}

.product-detail-header .title {
  font-size: 0.7em;
  font-weight: bold;
  color: #cc2c40;
}

.product-detail-header .close-btn {
  background: none;
  border: none;
  font-size: 1.2em;
  cursor: pointer;
  color: #888;
}

.product-detail-header .close-btn:hover {
  color: #333;
}

.quantity-display {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 30px;
  border: 1px solid #ced4da;
  border-left: none;
  border-right: none;
  font-weight: bold;
  font-size: 0.9rem;
  color: #343a40;
}

.product-detail-footer {
  padding-top: 15px;
  border-top: 1px solid #eee;
  margin-top: 15px;
}

.loader {
  width: 30%;
  display: flex !important;
  flex-wrap: wrap;
  justify-content: left;
  gap: 10px;
  transition: 0.5s linear;
  background-color: #e0e0e0;
  padding: 10px 14px;
  border-radius: 16px;
}

.ball {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  animation: bounce6135 1s alternate infinite;
  transition: 0.5s linear;
}

.ball {
  background: #fff;
}

.ball:nth-child(2) {
  animation-delay: 0.25s;
}

.ball:nth-child(3) {
  animation-delay: 0.5s;
}

@keyframes bounce6135 {
  from {
    transform: scale(2);
  }

  to {
    transform: scale(1);
  }
}


@keyframes slideUp {
  from {
    transform: translateY(100%);
    opacity: 0;
  }

  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.btn-danger1 {
  background-color: #cc2c40;
  color: white;
  border: none;
  padding: 5px 12px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.btn-danger1:hover {
  background-color: #a82434;
}

.carousel-container {
  width: 100%;
  box-sizing: border-box;
}

.card-button {
  width: 100%;
  margin: 5px 0 0;
}

.chat-toggle-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  background: #cc2c40;
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

.chips-container {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 8px;
}

.chip-button {
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  border-radius: 20px;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 14px;
  color: #333;
  transition: background-color 0.2s ease;
}

.chip-button:hover {
  background-color: #e0e0e0;
}

.chat-input {
  display: flex;
  padding: 10px;
  border-top: 1px solid #ddd;
  background: white;
}

.carousel-container {
  display: flex;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  gap: 12px;
  padding-bottom: 10px;
  margin-top: 8px;
  scroll-snap-type: x mandatory;
  width: 300px;
}

.carousel-container::-webkit-scrollbar {
  height: 6px;
}

.carousel-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.carousel-container::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.carousel-container::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.food-card {
  flex: 0 0 auto;
  width: 180px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  text-align: center;
  scroll-snap-align: start;
}

.card-image {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-bottom: 1px solid #eee;
}

.card-content {
  padding: 10px;
}

.card-title {
  font-weight: bold;
  margin-bottom: 5px;
  color: #333;
  font-size: 15px;
}

.card-subtitle {
  color: #cc2c40;
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 10px;
}

.card-buttons {
  margin-top: 8px;
}

.card-button {
  display: block;
  width: calc(100% - 10px);
  margin: 5px auto 0;
  background-color: #cc2c40;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 8px 10px;
  cursor: pointer;
  font-size: 13px;
  transition: background-color 0.2s ease;
}

.card-button:hover {
  background-color: #a82434;
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

button.btn-danger-customer:hover {
  color: #fff;
  background-color: #cc2c40 !important;
}

.loading-indicator {
  font-style: italic;
  color: #555;
  background-color: #e0e0e0;
  padding: 10px 14px;
  border-radius: 16px;
  font-size: 14px;
  line-height: 1.4;
  word-break: break-word;
  display: table;
  align-self: flex-start;
  margin-right: auto;
}

@media (max-width: 425px) {
  .chat-wrapper {
    width: 300px;
    height: 400px;
    bottom: 80px;
    right: 30px;
    border-radius: 0;
  }

  .chat-toggle-btn {
    bottom: 20px;
    right: 20px;
  }
}
</style>
