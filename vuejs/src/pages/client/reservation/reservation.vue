<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>
  <div class="row d-flex text-center">
    <div class="title-foods fw-medium fs-5 mt-5">
      <span>Lẩu và Mỳ cay 7 cấp độ</span>
    </div>
    <div class="title-shops d-sm-block fw-bold">
      <span>ĐẶT BÀN CÙNG CHÚNG TÔI!</span>
    </div>
  </div>
  <div class="container custom-container">
    <div class="booking-form row w-75" style="border-radius: 0px">
      <div class="col-md-6 booking-image">
        <img class="img-reservation" src="/img/reservation/Rectangle 48.png" alt="Khuyến mãi Tết" />
      </div>
      <div class="col-md-6 form-section mt-2">
        <form @submit.prevent="submitReservationAndSaveUser">
          <input type="text" v-model="form.fullname" class="form-control mb-2" placeholder="Tên của bạn" />
          <input type="text" v-model="form.phone" class="form-control mb-2" placeholder="Số điện thoại" />
          <input type="email" v-model="form.email" class="form-control mb-2" placeholder="Email" />
          <input type="number" v-model="guest_count" class="form-control mb-2" placeholder="Số lượng người" />
          <div class="row g-2">
            <div class="col">
              <input type="date" v-model="date" :min="today" class="form-control" placeholder="Chọn ngày" />
            </div>
            <select v-model="time" class="col mb-2 form-control custom-select">
              <option value="">Chọn giờ</option>
              <option v-for="t in timeOptions" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>

          <textarea cols="5" rows="3" v-model="note" class="form-control mb-2 custom-select"
            placeholder="Ghi chú"></textarea>
          <button @click="showModal" type="button" class="btn btn-custom mb-2">
            Đặt món <span>✚</span>
          </button>
          <button type="submit" class="btn btn-danger w-100">Xác nhận</button>
        </form>
      </div>
    </div>
  </div>
  <!-- Bootstrap Modal -->
  <div class="modal fade" id="orderModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="orderModalLabel">Đặt món</h5>
        </div>
        <div class="modal-body">
          <!-- Bộ lọc -->
          <div class="row mb-3">
            <div class="col">
              <select class="form-select" @change="getFoodByCategory($event.target.value)">
                <option value="">THỰC ĐƠN</option>
                <option v-for="item in flatCategoryList" :key="item.id" :value="item.id">
                  {{ item.indent }}{{ item.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Danh sách món ăn -->
          <div class="list-group">
            <div class="product-list-wrapper container-fluid">
              <div class="row">
                <div v-for="item in foods" :key="item" @click="openModal(item)" class="col-md-3">
                  <div class="product-card" >
                    <img :src="getImageUrl(item.image)" alt="" class="product-img mx-auto d-block" width="180px" />
                    <h3 class="product-dish-title text-center fw-bold fs-5">{{ item.name }}</h3>
                    <p class="product-dish-price fw-bold text-center">{{ formatNumber(item.price) }} VNĐ</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- modal food -->
  <div class="modal fade" id="productModal" >
    <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content custom-modal modal-ct">
        <div class="modal-body position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
            aria-label="Close"></button>
          <h5 class="fw-bold text-danger text-center mb-3">{{ foodDetail.name }}</h5>

          <div class="text-center mb-3">
            <img :src="getImageUrl(foodDetail.image)" :alt="foodDetail.name" class="modal-image" />
          </div>

          <p class="text-danger fw-bold fs-5 text-center">{{ formatNumber(foodDetail.price) }} VNĐ</p>
          <p class="text-dark text-center text-lg fw-bold mb-3">{{ foodDetail.description }}</p>

          <form @submit.prevent="addToCart">
            <div class="mb-3" v-if="spicyLevel.length">
              <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
              <select class="form-select" id="spicyLevel">
                <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
            </div>

            <div class="topping-container mb-3" v-if="toppingList.length">
              <label class="form-label fw-bold">🧀 Chọn Topping:</label>
              <div v-for="topping in toppingList" :key="topping.id"
                class="d-flex justify-content-between align-items-center mb-2">
                <label class="d-flex align-items-center">
                  <input type="checkbox" :value="topping.id" name="topping[]" class="me-2" />
                  {{ topping.name }}
                </label>
                <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
              </div>
            </div>

            <button class="btn btn-danger w-100 fw-bold">
              🛒 Thêm vào giỏ hàng
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { FoodList } from '@/stores/food'
import { User } from '@/stores/user'
import axios from 'axios'
import { ref } from 'vue'
import { Modal } from 'bootstrap'
import { useRouter } from 'vue-router'
export default {
  setup() {
    const time = ref('')
    const date = ref('')
    const today = new Date().toISOString().split('T')[0]
    const timeOptions = []
    const fullname = ref('')
    const phone = ref('')
    const email = ref('')
    const note = ref('')
    const guest_count = ref(2)
    const deposit_amount = 50000
    const router = useRouter()
    const quantities = ref({})

    const cart = JSON.parse(localStorage.getItem('cart')) || []

    const {
      foods,
      categories,
      toppings,
      getFoodByCategory,
      openModal,
      spicyLevel,
      toppingList,
      formatNumber,
      getImageUrl,
      flatCategoryList,
      foodDetail,
      addToCart,
      isLoading
    } = FoodList.setup()

    const {
      form,
      user,
      handleSubmit
    } = User.setup()

    for (let hour = 8; hour <= 19; hour++) {
      let hourStr = hour < 10 ? '0' + hour : '' + hour
      timeOptions.push(hourStr + ':00')
      if (hour !== 19) {
        timeOptions.push(hourStr + ':30')
      }
    }

    const reservation = async () => {
      isLoading.value = true
      const reservations_time = `${date.value} ${time.value}`
      const expiration_time = new Date(new Date(reservations_time).getTime() + 15 * 60000)
        .toLocaleString('sv-SE', { timeZone: 'Asia/Ho_Chi_Minh' })
        .replace(' ', 'T')
        .slice(0, 16)

      try {
        const token = localStorage.getItem('token')
        const res = await axios.post(
          'http://127.0.0.1:8000/api/reservation',
          {
            user_id: user.value?.id,
            guest_name: form.value.fullname,
            guest_phone: form.value.phone,
            guest_email: form.value.email,
            guest_count: guest_count.value,
            reservations_time,
            note: form.value.note,
            deposit_amount,
            expiration_time,
            total_price: getTotalPrice(cart),
            order_details: cart.map(item => ({
              food_id: item.id,
              combo_id: null,
              quantity: item.quantity,
              price: item.price,
              type: 'food',
              toppings: item.toppings.map(t => ({
                food_toppings_id: t.food_toppings_id,
                price: t.price
              }))
            }))
          },
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )
        alert('Đặt bàn thành công!')
        const orderId = res.data.order_id
        router.push({
          name: 'reservation-form',
          params: { orderId },
        })
      } catch (error) {
        console.error('Đặt bàn thất bại:', error.response?.data || error.message)
        alert('Có lỗi xảy ra khi đặt bàn, vui lòng thử lại.')
      } finally {
        isLoading.value = false
      }
    }
    const getTotalPrice = (cart) => {
      return cart.reduce((total, item) => {
        const basePrice = parseFloat(item.price) || 0
        const toppingsTotal =
          item.toppings_price?.reduce((sum, price) => sum + parseFloat(price), 0) || 0
        const itemTotal = (basePrice + toppingsTotal) * item.quantity
        return total + itemTotal
      }, 0)
    }

    const submitReservationAndSaveUser = async () => {
      isLoading.value = true
      try {
        await handleSubmit()
        await reservation()
      } catch (error) {
        console.error('Lỗi:', error)
      } finally {
        isLoading.value = false
      }
    }
    const showModal = () => {
      const modal = new Modal(document.getElementById('orderModal'));
      modal.show();

    };
    return {
      time, date, today, timeOptions, fullname, phone, email, note,
      guest_count, reservation, foods, categories, getFoodByCategory,
      openModal, spicyLevel, toppingList, formatNumber, getImageUrl,
      quantities, foodDetail, form, user, handleSubmit, showModal,
      submitReservationAndSaveUser, isLoading, toppings, flatCategoryList, addToCart
    }
  },
}
</script>
<style scoped>
.custom-modal {
  z-index: 1060; /* cao hơn modal trước (Bootstrap mặc định 1050) */
}
.custom-modal .modal-content {
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  border-radius: 12px;
}
.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
