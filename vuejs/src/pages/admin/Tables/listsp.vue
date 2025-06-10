<template>
  <div class="fw-semibold">Danh sách món</div>
  <div class="mb-3">
    <hr />
    <div class="row">
      <div class="col-12 col-md-6 col-lg-3 mb-3">
        Lọc theo danh mục:
        <select class="form-control rounded" @change="getFoodByCategory($event.target.value)">
          <option value="">Tất cả món ăn</option>
          <option v-for="item in flatCategoryList" :key="item.id" :value="item.id">
            {{ item.indent }}{{ item.name }}
          </option>
        </select>
      </div>
      <div class="col-12 col-md-6 col-lg-3 mb-3">
        Tìm kiếm theo tên:
        <v-select v-model="selectfood" :options="foods" label="name" placeholder="Nhập tên món ăn" :clearable="true"
          @input="onFoodSearch" class="form-control rounded" />
      </div>
      <div class="col-12 d-flex flex-column flex-lg-row gap-3 align-items-start">
        <div class="col-12 col-lg-8 d-flex flex-wrap justify-content-center justify-content-lg-start">
          <div class="box p-2 m-1" v-for="product in paginatedFoods" :key="product.id">
            <div class="gap-1">
              <div class="d-flex flex-column align-items-center" @click="openModal(product)">
                <img :src="getImageUrl(product.image)" alt="" srcset=""
                  style="width: 60px; height: 60px; object-fit: cover" />
                <div class="product_name text-center mt-1">{{ product.name }}</div>
                <strong class="text-danger product_price">
                  {{ formatNumber(product.price) }} VND
                </strong>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-center mt-3 w-100">
            <nav>
              <ul class="pagination">
                <li class="page-item" :class="{ disabled: currentPage.foods === 1 }">
                  <button type="button" class="page-link" @click="goToPage(currentPage.foods - 1, 'foods')">
                    «
                  </button>
                </li>

                <li v-for="page in totalPagesFoods" :key="page" class="page-item"
                  :class="{ active: currentPage.foods === page }">
                  <button type="button" class="page-link" @click="goToPage(page, 'foods')">
                    {{ page }}
                  </button>
                </li>

                <li class="page-item" :class="{ disabled: currentPage.foods === totalPagesFoods }">
                  <button type="button" class="page-link" @click="goToPage(currentPage.foods + 1, 'foods')">
                    »
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <div class="modal fade" id="productModal">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-2 p-1">
              <div class="modal-body">
                <div class="title">
                  <i class="bi bi-bag-plus-fill"></i>
                  Thêm đơn hàng
                </div>
                <div class="d-flex align-items-center p-2 border rounded shadow-sm" style="max-width: 500px">
                  <img :src="getImageUrl(foodDetail.image)" class="rounded me-3 border"
                    style="width: 60px; height: 60px; object-fit: cover" alt="Drink" />
                  <div class="flex-grow-1">
                    <div class="text-dark fw-semibold mb-2">{{ foodDetail.name }}</div>
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="fw-semibold" style="font-size: 14px">
                        {{ formatNumber(foodDetail.price) }}VNĐ
                      </div>
                      <div class="input-group input-group-sm" style="width: 100px">
                        <button class="btn btn-outline-secondary" @click="decreaseQuantity" type="button">
                          −
                        </button>
                        <span>{{ quantity }}</span>
                        <button class="btn btn-outline-secondary" @click="increaseQuantity" type="button">
                          +
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <form @submit.prevent="handleAddToCartClick">
                <div style="max-height: 400px; overflow-y: auto" class="pe-3 ps-3" v-if="toppingList.length">
                  <div class="mb-3">
                    <div class="mb-3" v-if="spicyLevel.length">
                      <label for="spicyLevel" class="form-label fw-bold text-danger">
                        🌶 Mức độ cay:</label>
                      <select class="form-select rounded" id="spicyLevel">
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                  </div>

                  <label class="form-label fw-bold text-danger">🧀 Chọn Topping:</label>
                  <div v-for="topping in toppingList" :key="topping.id"
                    class="d-flex justify-content-between align-items-center mb-2">
                    <label class="d-flex align-items-center text-dark">
                      <input type="checkbox" :value="topping.id" name="topping[]" class="me-2" />
                      {{ topping.name }}
                    </label>
                    <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
                  </div>
                </div>
                <div v-else class="mt-5">
                  <p class="text-center text-muted">Không có topping cho món này.</p>
                </div>

                <div class="modal-footer border-0">
                  <button class="btn btn-danger1 w-100 fw-bold" type="submit">
                    🛒 Thêm vào đơn hàng
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4 border p-3 mt-3 mt-lg-0">
          <form @submit.prevent="submitCart(orderId)">
            <div class="d-flex justify-content-between border-bottom mb-3">
              <h5>Chi tiết</h5>
              <a href="#" class="text-danger fw-semibold" @click.prevent="clearCart">Xoá tất cả</a>
            </div>
            <div style="max-height: 200px; overflow-y: auto" class="pe-1">
              <div class="border rounded p-3 mb-3" style="background-color: #fff" v-for="(item, index) in cartItems"
                :key="index">
                <div class="d-flex align-items-start border-bottom mb-2 pb-1">
                  <img :src="getImageUrl(item.image)" class="rounded me-2"
                    style="width: 60px; height: 60px; object-fit: cover" alt="Shrimp fried spicy sauce" />

                  <div class="flex-grow-1" style="max-height: 200px; overflow-y: auto">
                    <div class="fw-semibold">{{ item.name }}</div>

                    <div class="d-flex justify-content-between">

                      <i class="bi bi-x-circle me-3 mb-2" style="cursor: pointer" @click="removeItem(index)"></i>
                    </div>

                    <div class="text-muted small" v-if="item.toppings.length">
                      <div v-for="(topping, i) in item.toppings" :key="i">
                        + {{ topping.name }} ({{ formatNumber(topping.price) }} VNĐ)
                      </div>
                    </div>
                    <div v-else class="text-muted small">Không có topping</div>
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center border rounded" style="background-color: #f8f9fa">
                    <button type="button" class="btn border-0 fw-bold bg-white" style="background-color: transparent"
                      @click="decreaseQuantity1(index)">
                      −
                    </button>
                    <span class="px-2">{{ item.quantity }}</span>
                    <button type="button" class="btn border-0 fw-bold bg-white" style="background-color: transparent"
                      @click="increaseQuantity2(index)">
                      +
                    </button>
                  </div>
                  <div class="fw-bold fs-6">{{ formatNumber(totalPriceItem(item)) }} VNĐ</div>
                </div>
              </div>
              <div v-if="cartItems.length === 0" class="text-center text-muted py-3">
                Giỏ hàng trống.
              </div>
            </div>

            <div class="pt-0">
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  Tạm tính
                  <span>{{ formatNumber(totalPrice) }} VNĐ</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                  Phí giữ bàn
                  <span>100,000 VNĐ</span>
                </li>
                <li
                  class="list-group-item mb-0 pb-0 d-flex justify-content-between align-items-center fw-bold fs-6 text-danger">
                  Tổng thanh toán
                  <span class="text-danger fw-bold">
                    {{ formatNumber(totalPrice + 100000) }} VNĐ
                  </span>
                </li>
              </ul>
              <hr />
              <h6 class="mb-3">Phương thức thanh toán</h6>
              <div class="d-flex justify-content-around mb-4 flex-wrap gap-2">
                <button class="btn btn-payment active" type="button">
                  <img src="/img/cod.png" alt="Credit Card Icon" class="payment-icon mb-1" />
                  <br />
                  Tiền mặt
                </button>
                <button class="btn btn-payment" type="button">
                  <img src="/img/momo.png" alt="Cash Icon" class="payment-icon mb-1" />
                  <br />
                  MoMo
                </button>
                <button class="btn btn-payment" type="button">
                  <img src="/img/Logo-VNPAY-QR-1 (1).png" alt="Qris Icon" class="payment-icon mb-1" />
                  <br />
                  QR code
                </button>
              </div>
              <hr />
              <div class="d-flex flex-column flex-sm-row">
                <button type="button" @click="$router.back()"
                  class="btn btn-outline-dark flex-fill me-sm-2 mb-2 mb-sm-0 p-2">
                  Quay lại
                </button>
                <button type="submit" class="btn btn-outline-success flex-fill me-sm-2 mb-2 mb-sm-0 p-2">
                  Lưu lại
                </button>
                <button class="btn btn-outline-danger flex-fill p-2" type="button">
                  Thanh toán
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { FoodList } from '@/stores/food'
import { useRoute } from 'vue-router'
import { Cart } from '@/stores/cart'
import { computed } from 'vue'
import vSelect from 'vue-select'
import { toast } from 'vue3-toastify'

export default {
  components: {
    'v-select': vSelect,
  },
  setup() {
    const {
      foods,
      getFoodByCategory,
      openModal,
      formatNumber,
      getImageUrl,
      flatCategoryList,
      foodDetail,
      spicyLevel,
      toppingList,
      isLoading
    } = FoodList.setup()

    const selectfood = ref(null)
    const searchFoodTerm = ref('')

    const {
      cartItems,
      loadCart,
      totalPriceItem,
      totalPrice,
      finalTotal,
      saveCart,
      decreaseQuantity,
      increaseQuantity,
      decreaseQuantity1,
      increaseQuantity2,
      removeItem,
      clearCart,
      addToCart,
      quantity,
    } = Cart()

    const route = useRoute()
    const orderId = route.params.orderId

    const onFoodSearch = (event) => {
      searchFoodTerm.value = event.target.value
      currentPage.value.foods = 1 // reset về trang đầu tiên khi tìm kiếm mới
    }

    const currentPage = ref({
      foods: 1,
    })

    // số item trên mỗi trang
    const itemsPerPageFoods = 16

    // lấy danh sách món ăn đã phân trang
    const paginatedFoods = computed(() => {
      const filtered = foods.value.filter((food) =>
        food.name.toLowerCase().includes(searchFoodTerm.value.toLowerCase()),
      )
      const start = (currentPage.value.foods - 1) * itemsPerPageFoods
      return filtered.slice(start, start + itemsPerPageFoods)
    })

    // tính tổng số trang cho món ăn
    const totalPagesFoods = computed(() => {
      const filtered = foods.value.filter((food) =>
        food.name.toLowerCase().includes(searchFoodTerm.value.toLowerCase()),
      )
      return Math.ceil(filtered.length / itemsPerPageFoods)
    })

    // hàm chuyển trang
    const goToPage = (page, key) => {
      if (page >= 1 && page <= (key === 'foods' ? totalPagesFoods.value : 0)) {
        currentPage.value[key] = page
      }
    }

    const handleAddToCartClick = () => {
      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)
      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)

      let allSelectedToppings = [];

      if (selectedSpicy) {
        allSelectedToppings.push({
          name: selectedSpicy.name,
          price: selectedSpicy.price,
          food_toppings_id: selectedSpicy.pivot?.id || null,
          is_spicy_level: true
        });
      }

      const selectedToppingIds = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked')
      ).map((el) => parseInt(el.value));

      const normalToppings = toppingList.value
        .filter((topping) => selectedToppingIds.includes(topping.id))
        .map((topping) => ({
          id: topping.food_toppings_id,
          name: topping.name,
          price: topping.price,
          food_toppings_id: topping.pivot?.id || null,
          is_spicy_level: false
        }));

      allSelectedToppings = [...allSelectedToppings, ...normalToppings];

      addToCart(foodDetail.value, quantity.value, allSelectedToppings)
    }

    // hàm load món đã đặt trước từ đơn hàng
    const loadOrderDetails = async (id) => {
      if (!id) {
        return
      }
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/load-order-detail/${id}`)
        const orderDetails = response.data.data
        cartItems.value = []

        if (orderDetails) {
          orderDetails.details.forEach((detail) => {
            let mainItem = null
            let itemId = null
            let itemName = ''
            let itemImage = ''
            let basePrice = 0

            if (detail.type === 'food' && detail.foods) {
              mainItem = detail.foods
              itemId = mainItem.id
              itemName = mainItem.name
              itemImage = mainItem.image
              basePrice = parseFloat(mainItem.sale_price || mainItem.price)
            } else if (detail.type === 'combo' && detail.combos) {
              mainItem = detail.combos
              itemId = mainItem.id
              itemName = mainItem.name
              itemImage = mainItem.image
              basePrice = parseFloat(mainItem.sale_price || mainItem.price)
            } else {
              return
            }

            const itemToppings = detail.toppings
              ? detail.toppings.map((t) => ({
                food_toppings_id: t.food_toppings_id,
                name: t.food_toppings.toppings.name,
                price: t.price || 0,
              }))
              : []

            const item = {
              id: itemId,
              name: itemName,
              price: basePrice,
              image: itemImage,
              quantity: detail.quantity,
              toppings: itemToppings,
              combo_id: detail.type === 'combo' ? itemId : null,
              type: detail.type,
            }
            cartItems.value.push(item)
          })
          saveCart()
        }
      } catch (error) {
        console.error('Lỗi khi tải chi tiết đơn hàng:', error)
      }
    }

    // hàm update order_detail
    const submitCart = async (orderIdToSubmit) => {
      if (!orderIdToSubmit) {
        toast.error('Không tìm thấy đơn hàng để cập nhật!');
        return;
      }

      if (cartItems.value.length === 0) {
        toast.warning('Giỏ hàng trống, không có gì để cập nhật.');
        return;
      }

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
              food_toppings_id: top.food_toppings_id, // Dùng food_toppings_id nếu có, nếu không thì dùng id (dành cho trường hợp spicyLevel)
            })),
          };
        });

        await axios.put(`http://127.0.0.1:8000/api/update-order-detail/${orderIdToSubmit}`, {
          details: payloadDetails,
        });

        toast.success('Cập nhật đơn hàng thành công!');

        clearCart();
        await loadOrderDetails(orderIdToSubmit);
      } catch (error) {
        console.error('Lỗi khi cập nhật đơn hàng:', error);
        if (error.response && error.response.data && error.response.data.message) {
          toast.error(`Lỗi: ${error.response.data.message}`);
        } else {
          toast.error('Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
      }
    };

    onMounted(() => {
      const orderId = route.params.orderId
      if (orderId) {
        loadOrderDetails(orderId)
      } else {
        loadCart()
      }
    })

    return {
      foods,
      formatNumber,
      getImageUrl,
      orderId,
      getFoodByCategory,
      openModal,
      spicyLevel,
      toppingList,
      flatCategoryList,
      foodDetail,
      addToCart,
      isLoading,
      quantity,
      decreaseQuantity,
      increaseQuantity,
      decreaseQuantity1,
      increaseQuantity2,
      cartItems,
      loadCart,
      totalPriceItem,
      totalPrice,
      finalTotal,
      saveCart,
      removeItem,
      onFoodSearch,
      currentPage,
      paginatedFoods,
      goToPage,
      searchFoodTerm,
      selectfood,
      totalPagesFoods,
      clearCart,
      submitCart,
      handleAddToCartClick,
    }
  },
}
</script>

<style scoped>
/* Giữ nguyên các styles bạn đã cung cấp */
.btn-payment {
  flex-grow: 1;
  margin: 0 5px;
  padding: 5px 10px;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  background-color: #fff;
  color: #343a40;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease-in-out;
  text-align: center;
}

.btn-payment:hover {
  background-color: #f8f9fa;
}

.btn-payment.active {
  background-color: #fffefe;
  border-color: #c92c3c;
  color: #c92c3c;
}

.payment-icon {
  width: 15px;
  height: 15px;
  object-fit: contain;
}

.list-group-item:first-child {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.list-group-item:last-child {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border-bottom: none;
}

.list-group-flush .list-group-item {
  border-width: 0 0 1px;
}

.chon {
  margin-top: 32px;
  margin-left: 10px;
}

.product_name {
  font-size: 14px;
  font-weight: 700;
}

.product_price {
  font-size: 12px;
}

.box {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  background-color: #fffefe;
}

/* Để làm cho dropdown menu giống Bootstrap card/dropdown */
.v-select .vs__dropdown-menu {
  border: 1px solid rgba(0, 0, 0, 0.15);
  /* Border của dropdown Bootstrap */
  border-radius: 0.25rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
  /* Shadow của Bootstrap dropdown */
}

/* Định kiểu cho các option trong dropdown */
.v-select .vs__dropdown-option {
  padding: 0.25rem 0.5rem;
  /* Padding giống dropdown item */
  font-size: 1rem;
}

.v-select .vs__dropdown-toggle {
  border: none;
  padding: 0;
}

/* Định kiểu cho option khi hover/active */
.v-select .vs__dropdown-option--highlight {
  background-color: #f8f9fa;
  /* Màu background khi hover/active */
  color: #c92c3c;
  font-weight: 500;
}

@media (min-width: 768px) {
  .box {
    flex: 0 0 calc(33.333% - 0.5rem);
    max-width: calc(33.333% - 0.5rem);
  }
}

@media (min-width: 1024px) {
  .box {
    flex: 0 0 calc(25% - 0.5rem);
    max-width: calc(25% - 0.5rem);
  }
}
</style>
