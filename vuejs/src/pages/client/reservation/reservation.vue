<template>
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
          <button type="button" class="btn btn-custom mb-2" data-bs-toggle="modal" data-bs-target="#orderModal">
            Đặt món <span>✚</span>
          </button>

          <button type="submit" class="btn btn-danger w-100">Xác nhận</button>
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
                <option value="">THỰC ĐƠN</option>
                <option v-for="item in flatCategoryList" :key="item.id" :value="item.id">
                  {{ item.indent }}{{ item.name }}
                </option>
              </select>
            </div>
          </div>

          <!-- Danh sách món ăn -->
          <div class="list-group">
            <div class="container text-center">
              <div class="row row-select" v-for="food in foods" :key="food.id">
                <div class="col">
                  <img :src="getImageUrl(food.image)" alt="Món ăn" class="img-fluid me-3" style="width: 130px" />
                </div>
                <div class="col-6 text-start">
                  <h5 class="mb-1">{{ food.name }}</h5>
                  <div class="row">
                    <div class="col food" @click="openModal(food.id)">
                      <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                      <select class="form-select" :id="'spicyLevel-' + food.id">
                        <option value="" selected>Chọn cấp độ</option>
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.pivot.id">
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
                                      <input class="form-check-input" type="checkbox" :id="'topping-' + toppings.id"
                                        :value="toppings.pivot.id" :name="'topping[]'" />
                                      <label class="form-check-label" :for="'topping-' + toppings.id">{{ toppings.name
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
                  <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm" style="width: 37px; height: 37px"
                      @click="decreaseQuantity(food.id)">
                      <b>-</b>
                    </button>

                    <input type="text" min="1" v-model="quantities[food.id]" class="form-control text-center mx-2"
                      style="width: 100px" />

                    <button class="btn btn-outline-secondary btn-sm" style="width: 37px; height: 37px"
                      @click="increaseQuantity(food.id)">
                      <b>+</b>
                    </button>
                  </div>
                  <button class="btn btn-danger mt-2 w-100" @click="addToCart(food.id)">
                    Đặt món
                  </button>
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
import { FoodList } from '@/stores/food'
import axios from 'axios'
import { ref, onMounted } from 'vue'
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
    const guest_count = ref(2)
    const deposit_amount = 50000
    const router = useRouter()
    const savedUser = JSON.parse(localStorage.getItem('user'))
    const quantities = ref({})
    // const order_details = ref([]);


    const cart = JSON.parse(localStorage.getItem('cart1')) || []
    const generateOrderDetails = async () => {
      const details = []

      cart.forEach(element => {
        const toppings = Array.isArray(element.topping_id)
          ? element.topping_id.map((id, index) => ({
            food_toppings_id: id,
            price: parseFloat(element.toppingPrice?.[index]) || null
          }))
          : []

        details.push({
          food_id: element.id,
          quantity: element.quantity,
          price: element.price || null,
          type: 'food',
          combo_id: null,
          toppings: toppings
        })
      })

      return details
    }



    // console.log(cart);

    // console.log(order_details.value);

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
      // addToCart
    } = FoodList.setup()

    const reservation = async () => {
      const reservations_time = `${date.value} ${time.value}`
      const expiration_time = new Date(new Date(reservations_time).getTime() + 15 * 60000)
        .toLocaleString('sv-SE', { timeZone: 'Asia/Ho_Chi_Minh' })
        .replace(' ', 'T')
        .slice(0, 16)

      try {
        console.log('Total Price:', getTotalPrice(cart));
        const res = await axios.post('http://127.0.0.1:8000/api/reservation', {
          guest_name: fullname.value,
          guest_phone: phone.value,
          guest_email: email.value,
          guest_count: guest_count.value,
          reservations_time: reservations_time,
          note: note.value,
          deposit_amount: deposit_amount,
          expiration_time: expiration_time,
          total_price: getTotalPrice(cart),
          order_details: await generateOrderDetails()
        })
        console.log(res.data)
        alert('Đặt bàn thành công!')
        const orderId = res.data.order_id;
        router.push({
          name: 'reservation-form',
          params: { orderId }
        })
        // console.log(orderId);
      } catch (error) {
        console.error(error.response.data)
      }
    }

    const addToCart = (foodID) => {
      const food = foods.value.find((f) => f.id === foodID)
      if (!food) {
        alert('Không tìm thấy món ăn!')
        return
      }

      const quantityInput = quantities.value[foodID] || 1
      const selectedSpicyId = document.getElementById(`spicyLevel-${foodID}`)?.value

      const selectedSpicy = spicyLevel.value.find((item) => item.pivot.id == selectedSpicyId)
      const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không rõ'

      const selectedToppingId = Array.from(
        document.querySelectorAll(`#toppingModal-${foodID} input[name="topping[]"]:checked`)
      ).map((el) => parseInt(el.value))

      const selectedToppingName = toppingList.value
        .filter((topping) => selectedToppingId.includes(topping.pivot.id))
        .map((topping) => topping.name)

      const selectedToppingPrice = toppingList.value
        .filter((topping) => selectedToppingId.includes(topping.pivot.id))
        .map((topping) => topping.price)

      const toppingIDs = [...selectedToppingId]
      if (selectedSpicy) {
        toppingIDs.push(selectedSpicy.pivot.id)
      }
      const toppingprices = [...selectedToppingPrice]

      const cartItems = {
        id: food.id,
        name: food.name,
        image: food.image,
        price: food.price,
        spicyLevel: selectedSpicyName,
        toppings: selectedToppingName,
        toppings_price: selectedToppingPrice,
        quantity: quantityInput,
        topping_id: toppingIDs,
        toppingPrice: toppingprices,
      }

      let existingCart = JSON.parse(localStorage.getItem('cart1')) || []
      existingCart.push(cartItems)
      localStorage.setItem('cart1', JSON.stringify(existingCart))

      const existingItem = cart.findIndex(
        (item) =>
          item.id === cartItems.id &&
          item.spicyLevel === cartItems.spicyLevel &&
          JSON.stringify(item.toppings.sort()) === JSON.stringify(cartItems.toppings.sort()),
      )

      if (existingItem !== -1) {
        cart[existingItem].quantity += cartItems.quantity
      } else {
        cart.push(cartItems)
      }

      localStorage.setItem('cart1', JSON.stringify(cart))
      alert('Đã thêm vào giỏ hàng!')
    }

    const increaseQuantity = (id) => {
      if (!quantities.value[id]) quantities.value[id] = 1
      quantities.value[id]++
    }

    const decreaseQuantity = (id) => {
      if (!quantities.value[id]) quantities.value[id] = 1
      if (quantities.value[id] > 1) {
        quantities.value[id]--
      }
    }


    const getTotalPrice = (cart) => {
      return cart.reduce((total, item) => {
        const basePrice = parseFloat(item.price) || 0;
        const toppingsTotal = item.toppings_price?.reduce((sum, price) => sum + parseFloat(price), 0) || 0;
        const itemTotal = (basePrice + toppingsTotal) * item.quantity;
        return total + itemTotal;
      }, 0);
    };

    console.log("Total Price:", getTotalPrice(cart));





    onMounted(() => {
      if (savedUser) {
        fullname.value = savedUser.fullname || savedUser.username
        phone.value = savedUser.phone || ''
        email.value = savedUser.email || ''
      }
      generateOrderDetails()
    })

    return {
      time, date, today, timeOptions,
      fullname, phone, email, note, guest_count,
      reservation, foods, categories, getFoodByCategory,
      openModal, spicyLevel, toppingList, formatNumber, getImageUrl,
      flatCategoryList, quantities, increaseQuantity, decreaseQuantity,
      addToCart
    };

  },
}
</script>
