<template>
  <div class="container my-4">
    <div class="row">
      <!-- Danh sách sản phẩm -->
      <div class="col-md-6" style="max-height: 500px; overflow-y: auto">
        <div
          v-for="product in foodOrderAdmin"
          :key="product.id"
          class="border rounded shadow-sm p-3 mb-3"
        >
          <div class="row">
            <div class="col">
              <h5>{{ product.name }}</h5>
              <strong class="text-danger">{{ formatNumber(product.price) }} VND</strong>
            </div>

            <div class="col-sm-4">
              <label class="form-label fw-bold">Cấp độ:</label><br>
              <select class="form-select-sm p-1">
                <option v-for="spicy in product.spicyLevelNull" :key="spicy.id" :value="spicy.pivot.id">
                  {{ spicy.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="col-sm-8">
              <label class="form-label fw-bold">Chọn topping:</label>
              <div
                class="d-flex justify-content-between align-items-center border rounded p-2"
                v-for="topping in product.spicyLevelNotNull"
                :key="topping.pivot.id"
              >
                <label class="d-flex align-items-center mb-0">
                  <input
                    type="checkbox"
                    :value="topping.pivot.id"
                    v-model="product.selectedToppings"
                    class="me-2"
                    name="topping[]"
                  />
                  {{ topping.name }}
                </label>
                <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
              </div>
            </div>
            <div class="col-sm-4 chon">
              <button class="btn btn-primary" @click="addToCart(product)">Chọn</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Giỏ hàng -->
      <div class="col-md-6" style="max-height: 500px; overflow-y: auto">
        <div class="border rounded p-3">
          <h5 class="text-center mb-3">Giỏ hàng</h5>

          <table class="table">
            <thead>
              <tr>
                <th>Món ăn</th>
                <th>SL</th>
                <th>Thành tiền</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="cartList.length === 0">
                <td colspan="4" class="text-center text-muted">Khách chưa order</td>
              </tr>

              <tr v-for="(item, index) in cartList" :key="item.id">
                <td>
                  {{ item.name }}
                  <div class="text-muted small">Cấp độ: {{ item.spicyLevel || 'Không chọn' }}</div>
                  <ul class="text-muted small ps-3">
                    <li v-for="top in item.toppings" :key="top.id">
                      + {{ top.name }} ({{ formatNumber(top.price) }}đ)
                    </li>
                  </ul>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary" @click="decreaseQty(item)">
                      -
                    </button>
                    <span class="mx-2">{{ item.quantity }}</span>
                    <button class="btn btn-sm btn-outline-secondary" @click="increaseQty(item)">
                      +
                    </button>
                  </div>
                </td>
                <td>{{ formatNumber(itemTotal(item)) }} đ</td>
                <td>
                  <button class="btn btn-sm btn-danger" @click="removeFromCart(index)">X</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="text-end mt-3">
            <button @click="submitCart(orderId)" class="btn btn-sm btn-danger">
              Thêm món cho khách
            </button>
          </div>
          <div class="text-end mt-3">
            <strong class="text-danger fs-5">Tổng tiền: {{ formatNumber(totalPrice()) }} đ</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { FoodList } from '@/stores/food'
import router from '@/router'
import { param } from 'jquery'
import { useRouter, useRoute } from 'vue-router'

export default {
  setup() {
    const { foodOrderAdmin, formatNumber, spicyLevelNull, spicyLevelNotNull } = FoodList.setup()
    const route = useRoute()
    const router = useRouter()
    const orderId = route.params.id
    const cartList = ref([])

    const addToCart = (product) => {
      const confirmAdd = confirm(`Bạn có muốn thêm "${product.name}" vào giỏ hàng không!`)
      if (!confirmAdd) return

      const selectedSpicy = product.spicyLevelNull.find(
        (spicy) => spicy.pivot.id == product.selectedSpicyLevel,
      )
      const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không cay'

      const selectedToppings = product.spicyLevelNotNull
        .filter((topping) => product.selectedToppings.includes(topping.pivot.id))
        .map((topping) => ({
          id: topping.id,
          name: topping.name,
          price: Number(topping.price),
          food_toppings_id: topping.pivot?.id || null,
        }))

      cartList.value.push({
        id: product.id,
        name: product.name,
        price: Number(product.price),
        spicyLevel: selectedSpicyName,
        spicyLevelid: product.selectedSpicyLevel ?? null,
        toppings: selectedToppings,
        quantity: 1,
      })

      // Reset sau khi thêm
      product.selectedToppings = []
      product.selectedSpicyLevel = null
    }

    const removeFromCart = (index) => {
      const confirmRemove = confirm('Bạn có hủy món của khách không?')
      if (confirmRemove) {
        cartList.value.splice(index, 1)
      }
    }

    const increaseQty = (item) => (item.quantity += 1)

    const decreaseQty = (item) => {
      if (item.quantity > 1) item.quantity -= 1
    }

    const itemTotal = (item) => {
      const basePrice = item.price || 0
      const toppingTotal = item.toppings.reduce((sum, top) => sum + (top.price || 0), 0)
      return (basePrice + toppingTotal) * item.quantity
    }

    const totalPrice = () => {
      return cartList.value.reduce((sum, item) => sum + itemTotal(item), 0)
    }

    console.log(cartList.value)
    const submitCart = async (orderId) => {
      if (!orderId) {
        alert('Không tìm thấy mã đơn hàng!')
        return
      }

      try {
        for (const item of cartList.value) {
          await axios.post(`http://127.0.0.1:8000/api/order-for-user`, {
            order_id: orderId,
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: 'food',
            order_toppings: item.toppings.map((top) => ({
              food_toppings_id: top.food_toppings_id,
              price: top.price,
            }))

          });
        }
        alert('Thêm toàn bộ món thành công!')
        cartList.value = []
      } catch (error) {
        console.error('Lỗi khi đặt món:', error)
        alert('Có lỗi xảy ra khi thêm món!')
      }
    }

    onMounted(() => {
      cartList
      // console.log('ssss',foodOrderAdmin.spicyLevelNotNull)
    })

    return {
      foodOrderAdmin,
      formatNumber,
      spicyLevelNull,
      spicyLevelNotNull,
      cartList,
      addToCart,
      removeFromCart,
      increaseQty,
      decreaseQty,
      itemTotal,
      totalPrice,
      submitCart,
      orderId,
    }
  },
}
</script>

<style scoped>
.chon {
  margin-top: 32px;
  margin-left: 10px;
}

.container {
  margin-top: 20px;
}

.border {
  border: 1px solid #ddd;
}

.shadow-sm {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.d-flex {
  display: flex;
}

.justify-content-between {
  justify-content: space-between;
}

.align-items-center {
  align-items: center;
}

.text-center {
  text-align: center;
}

.fw-bold {
  font-weight: bold;
}

.text-muted {
  color: #6c757d;
}

.btn {
  background-color: #007bff;
  color: white;
  padding: 0.5rem 1rem;
  border: none;
  cursor: pointer;
}

.btn:hover {
  background-color: #0056b3;
}

.btn-warning {
  background-color: #f0ad4e;
}

.btn-warning:hover {
  background-color: #ec971f;
}

.btn-danger {
  background-color: #d9534f;
}

.btn-danger:hover {
  background-color: #c9302c;
}
</style>
