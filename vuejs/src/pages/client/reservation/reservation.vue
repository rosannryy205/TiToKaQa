<template>
  <div class="container custom-container">
    <div class="booking-form row w-75" style="border-radius: 0px">
      <div class="col-md-6 booking-image">
        <img class="img-reservation" src="/img/reservation/Rectangle 48.png" alt="Khuyến mãi Tết" />
      </div>
      <div class="col-md-6 form-section mt-2">
        <form @submit.prevent="reservation">
          <input type="text" v-model="fullname" class="form-control mb-2" placeholder="Tên của bạn" />
          <input type="text" v-model="phone" class="form-control mb-2" placeholder="Số điện thoại" />
          <input type="email" v-model="email" class="form-control mb-2" placeholder="Email" />
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
          <button class="btn btn-custom mb-2" data-bs-toggle="modal" data-bs-target="#orderModal">
            Đặt món <span>✚</span>
          </button>

          <button type="submit" class="btn btn-danger w-100">
            Xác nhận
          </button>

        </form>
      </div>
    </div>
  </div>
  <!-- Bootstrap Modal -->
  <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content custom-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="orderModalLabel">Đặt món</h5>
        </div>
        <div class="modal-body">
          <!-- Bộ lọc -->
          <div class="row mb-3">
            <div class="col">
              <select class="form-select" @change="getFoodByCategory($event.target.value)">
                <option value="">Chọn danh mục</option>
                <option v-for="item in flatCategoryList" :key="item.id" :value="item.id">
                  {{ item.indent }}{{ item.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Danh sách món ăn -->
          <div class="list-group">
            <div class="container text-center">
              <div class="row row-select" v-for="(food, index) in foods" :key="index">
                <div class="col">
                  <img :src="getImageUrl(food.image)" alt="Món ăn" class="img-fluid me-3" style="width: 130px" />
                </div>
                <div class="col-6 text-start">
                  <h5 class="mb-1">{{ food.name }}</h5>
                  <div class="row">
                    <div :class="'col food-' + food.id">
                      <label :for="'spicyLevel-' + food.id" class="form-label fw-bold">🌶 Mức độ cay:</label>
                      <select class="form-select" :id="'spicyLevel-' + food.id" @click="openModal(food.id)">
                        <option value="" selected>Chọn cấp độ</option>
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>

                    <div class="col">
                      <label for="spicyLevel" class="form-label fw-bold">🌶 Toppings:</label>
                      <div class="topping-list">
                        <button @click="openModal(food.id, `toppingModal-${food.id}`)" class="btn btn-success">
                          Chọn toppings
                        </button>

                        <div class="modal fade" :id="`toppingModal-${food.id}`" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Danh sách các lựa chọn</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                  aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div><strong>Món thêm</strong></div>
                                <div class="form-check">
                                  <div class="form-check d-flex" v-for="toppings in toppingList" :key="toppings.id">
                                    <div class="w-100">
                                      <input class="form-check-input" type="checkbox" id="topping2" name="topping[]"
                                        :value="toppings.id" />
                                      <label class="form-check-label" for="topping2">{{
                                        toppings.name
                                      }}</label>
                                    </div>
                                    <div class="flex-shrink-1">
                                      <label class="form-check-label" for="topping2">{{ formatNumber(toppings.price)
                                      }}VND</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                  Đóng
                                </button>
                                <button type="button" class="btn btn-primary">Lưu</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="mb-1 mt-3 description2">
                    {{ food.description }}{{ food.description.length > 60 ? '...' : '' }}
                  </p>
                </div>
                <div class="col text-center d-flex flex-column align-items-center">
                  <strong class="price">{{ formatNumber(food.price) }} VNĐ</strong>
                  <div class="d-flex align-items-center mt-2">
                    <button class="btn btn-outline-secondary btn-sm" style="width: 37px; height: 37px">
                      <b>-</b>
                    </button>
                    <input type="text" value="1" class="form-control text-center mx-2" style="width: 100px" />
                    <button class="btn btn-outline-secondary btn-sm" style="width: 37px; height: 37px">
                      <b>+</b>
                    </button>
                  </div>
                  <button class="btn btn-danger mt-2 w-100">Đặt món</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { FoodList } from '@/stores/load_food'
import axios from 'axios'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
export default {
  components: {
    FoodList,
  },
  setup() {
    const time = ref('')
    const date = ref('')
    const today = new Date().toISOString().split('T')[0]
    const timeOptions = []
    const fullname = ref('')
    const phone = ref('')
    const email = ref('')
    const note = ref('')
    const guest_count = ref(0)
    const deposit_amount = 50000
    const router = useRouter()

    for (let hour = 8; hour <= 19; hour++) {
      let hourStr = hour < 10 ? '0' + hour : '' + hour
      timeOptions.push(hourStr + ':00')
      if (hour !== 19) {
        timeOptions.push(hourStr + ':30')
      }
    }
    const {
      foods,
      categories,
      getFoodByCategory,
      openModal,
      spicyLevel,
      toppingList,
      formatNumber,
      getImageUrl,
      flatCategoryList,
    } = FoodList.setup()

    const reservation = async () => {
      const reservations_time = `${date.value} ${time.value}`;
      const expiration_time = new Date(new Date(reservations_time).getTime() + 15 * 60000)
        .toLocaleString('sv-SE', { timeZone: 'Asia/Ho_Chi_Minh' })
        .replace(' ', 'T')
        .slice(0, 16); // lấy định dạng 'YYYY-MM-DD HH:mm'


      // const notify = ref('');

      try {
        const res = await axios.post('http://127.0.0.1:8000/api/reservation', {
          guest_name: fullname.value,
          guest_phone: phone.value,
          guest_email: email.value,
          guest_count: guest_count.value,
          reservations_time: reservations_time,
          note: note.value,
          deposit_amount: deposit_amount,
          expiration_time: expiration_time
        })
        console.log(res.data)
        // notify.value = 'Đặt bàn thành công!'
        router.push({ name: 'reservation-form' })
      } catch (error) {
        // notify.value = 'Đặt bàn không thành công! Vui lòng thử lại!'

        console.error(error.response.data) // xem lỗi cụ thể trả về từ backend
      }
    }


    return {
      today,
      time,
      timeOptions,
      foods,
      categories,
      getFoodByCategory,
      openModal,
      spicyLevel,
      toppingList,
      formatNumber,
      getImageUrl,
      flatCategoryList,
      reservation,
      fullname,
      phone,
      email,
      note,
      guest_count,
      date,
      // notify
    }
  },
}
</script>
