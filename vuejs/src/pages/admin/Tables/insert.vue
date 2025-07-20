<template>
  <div>
    <div v-if="isLoading" class="isLoading-overlay">
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" v-if="hasPermission('create_booking')">
    <h3 class="fw-bold mb-2 mb-md-0">Thêm đơn đặt bàn</h3>
    <div>
      <button @click="$router.back()" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </button>
    </div>
  </div>

  <form class="row mt-2" @submit.prevent="reservation">
    <div class="col-12">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">
          <div class="row d-flex flex-column flex-lg-row">
            <div class="col-12 col-lg-5 mb-3">
              <label for="name" class="form-label fs-5">
                Thông tin khách hàng <span class="text-danger">*</span>
              </label>
              <v-select v-model="selectguest" :options="guest" label="usernameEmail" placeholder="Chọn khách hàng"
                :clearable="true" class="form-control rounded" />
              <div class="mt-2">
                <label for="name" class="form-label">
                  Tên khách hàng <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control rounded-2" required v-model="guest_name" />
                <label for="name" class="form-label">
                  Số điện thoại <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control rounded-2" required v-model="guest_phone" />
                <label for="category" class="form-label">
                  Email
                </label>
                <input type="text" class="form-control rounded-2" required v-model="guest_email" />
                <label for="category" class="form-label">
                  Ghi chú
                </label>
                <textarea class="form-control rounded-2" id="description" rows="1" v-model="note"></textarea>
              </div>
            </div>

              <div class="col-12 col-lg-7 mb-3 pt-3 pt-lg-0 mt-3 mt-lg-0" style="border-left: 1px solid #cccc">
                <label for="category" class="form-label">
                  Tìm bàn <span class="text-danger">*</span>
                </label>
                <div class="row mb-3 gx-2 gy-2">
                  <div class="col-12 col-sm-4">
                    <input type="date" class="form-control rounded" v-model="date" :min="today" @change="findTable" />
                  </div>
                  <div class="col-12 col-sm-4">
                    <select class="form-control rounded" v-model="time" @change="findTable">
                      <option value="">Chọn giờ</option>
                      <option v-for="time in filteredTimeOptions" :key="time" :value="time">
                        {{ time }}
                      </option>
                    </select>
                  </div>
                  <div class="col-12 col-sm-4">
                    <input type="number" class="form-control rounded" placeholder="Số lượng người" v-model="guest_count"
                      @change="findTable" />
                  </div>
                </div>
                <div class="table-container">
                  <div class="table-block" v-for="ban in paginatedTables" :key="ban.id">
                    <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
                      <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="n"></div>
                    </div>
                    <div @click="toggleTable(ban.id)" :class="[
                      selectedTableIds.includes(ban.id) ? 'table-rect1' : 'table-rect',
                      {
                        medium: getChairCount(ban.capacity) === 2,
                        large: getChairCount(ban.capacity) === 3,
                        billed: ban.status === 'Đã đặt trước',
                        'billed-text': ban.status === 'Đã đặt trước',
                        reservation: ban.status === 'Có khách',
                        'reservation-text': ban.status === 'Có khách',
                      },
                    ]">
                      Bàn {{ ban.name || ban.id }}
                    </div>
                    <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
                      <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="'b' + n"></div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-center mt-3 w-100">
                  <nav>
                    <ul class="pagination">
                      <li class="page-item" :class="{ disabled: currentPage.tables === 1 }">
                        <button class="page-link" @click="goToPage(currentPage.tables - 1, 'tables')">
                          «
                        </button>
                      </li>

                      <li v-for="page in totalPagesTables" :key="page" class="page-item"
                        :class="{ active: currentPage.tables === page }">
                        <button type="button" class="page-link" @click="goToPage(page, 'tables')">
                          {{ page }}
                        </button>
                      </li>

                      <li class="page-item" :class="{ disabled: currentPage.tables === totalPagesTables }">
                        <button type="button" class="page-link" @click="goToPage(currentPage.tables + 1, 'tables')">
                          »
                        </button>
                      </li>
                    </ul>
                  </nav>
                </div>
              </div>
            </div>

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
                <label for="foodSearch">Tìm kiếm theo tên món ăn:</label>
                <input id="foodSearch" type="text" class="form-control rounded" v-model="searchFoodTerm"
                  placeholder="Nhập tên món ăn" />
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
                                <label for="spicyLevel" class="form-label fw-bold text-danger">🌶 Mức độ cay:</label>
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
                    <div class="d-flex justify-content-between border-bottom mb-3">
                      <h5>Chi tiết</h5>
                      <a href="#" class="text-danger fw-semibold" @click.prevent="clearCart">Xoá tất cả</a>
                    </div>
                    <div style="max-height: 200px; overflow-y: auto" class="pe-1">
                      <div class="border rounded p-3 mb-3" style="background-color: #fff"
                        v-for="(item, index) in cartItems" :key="index">
                        <div class="d-flex align-items-start border-bottom mb-2 pb-1">
                          <img :src="getImageUrl(item.image)" class="rounded me-2"
                            style="width: 60px; height: 60px; object-fit: cover" />

                          <div class="flex-grow-1" style="max-height: 200px; overflow-y: auto">
                            <div class="fw-semibold">{{ item.name }}</div>

                            <div class="d-flex justify-content-between">
                              <div class="text-muted small" v-if="item.toppings.length">
                                <div v-for="(topping, i) in item.toppings" :key="i">
                                  + {{ topping.name }} ({{ formatNumber(topping.price) }} VNĐ)
                                </div>
                              </div>
                              <div v-else class="text-muted small">Không có topping</div>
                              <i class="bi bi-x-circle me-3 mb-2" style="cursor: pointer"
                                @click="removeItem(index)"></i>
                            </div>
                          </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                          <div class="d-flex align-items-center border rounded" style="background-color: #f8f9fa">
                            <button type="button" class="btn border-0 fw-bold bg-white"
                              style="background-color: transparent" @click="decreaseQuantity1(index)">
                              −
                            </button>
                            <span class="px-2">{{ item.quantity }}</span>
                            <button type="button" class="btn border-0 fw-bold bg-white"
                              style="background-color: transparent" @click="increaseQuantity2(index)">
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

                      <li
                        class="list-group-item mb-0 pb-0 d-flex justify-content-between align-items-center fw-bold fs-6 text-danger">
                        Tổng thanh toán
                        <span class="text-danger fw-bold">
                          {{ formatNumber(totalPrice) }} VNĐ
                        </span>
                      </li>
                    </ul>
                    <hr />
                    <h6 class="mb-3">Phương thức thanh toán</h6>
                    <div class="d-flex justify-content-around mb-4 flex-wrap gap-2">
                      <button class="btn btn-payment active">
                        <img src="/img/cod.png" alt="Credit Card Icon" class="payment-icon mb-1" />
                        <br />
                        Tiền mặt
                      </button>
                      <button class="btn btn-payment">
                        <img src="/img/momo.png" alt="Cash Icon" class="payment-icon mb-1" />
                        <br />
                        MoMo
                      </button>
                      <button class="btn btn-payment">
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
                        Đặt bàn
                      </button>
                      <button class="btn btn-outline-danger flex-fill p-2" type="button">
                        Thanh toán
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'
import { Info } from '@/stores/info-order-reservation'
import { Cart } from '@/stores/cart'
import vSelect from 'vue-select'
import { FoodList } from '@/stores/food'
import { watch } from 'vue'
import { Permission } from '@/stores/permission'
import { useRoute } from 'vue-router'

export default {
  components: {
    'v-select': vSelect,
  },
  setup() {
    const { info, getInfo, formatDate, formatTime, formatDateTime } = Info.setup()
    const {
      cartItems,
      finalTotal,
      loadCart,
      totalPriceItem,
      totalPrice,
      saveCart,
      addToCart,
      quantity,
      increaseQuantity,
      decreaseQuantity,
      removeItem,
      increaseQuantity2,
      decreaseQuantity1,
      clearCart,
    } = Cart()
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
    } = FoodList.setup()
    const userId = ref(null)
    const userString = localStorage.getItem('user')
    if (userString) {
      const user = JSON.parse(userString)
      if (user && user.id !== undefined) {
        userId.value = user.id
      }
    }
    const { hasPermission, permissions } = Permission(userId)

    const isLoading = ref(false)
    const today = new Date().toISOString().split('T')[0]
    const timeOptions = ref([])
    const note = ref('')
    const availableTables = ref([])
    const table_id = ref(null)
    const date = ref('')
    const time = ref('')
    const user_id = ref(null)
    const guest_name = ref('')
    const guest_phone = ref(null)
    const guest_count = ref(null)
    const guest_email = ref('')
    const selectedTableIds = ref([])
    const guest = ref([])
    const selectguest = ref(null)
    const selectfood = ref(null)
    const searchFoodTerm = ref('')
    const route = useRoute()

    const onFoodSearch = (event) => {
      searchFoodTerm.value = event.target.value
      currentPage.value.foods = 1 // reset về trang đầu tiên khi tìm kiếm mới
    }
    const currentPage = ref({
      tables: 1,
      foods: 1,
    })

    // số item trên mỗi trang
    const itemsPerPageTables = 10
    const itemsPerPageFoods = 16

    // lấy danh sách bàn đã phân trang
    const paginatedTables = computed(() => {
      const start = (currentPage.value.tables - 1) * itemsPerPageTables
      return availableTables.value.slice(start, start + itemsPerPageTables)
    })

    // lấy danh sách món ăn đã phân trang
    const paginatedFoods = computed(() => {
      const filtered = foods.value.filter((food) =>
        food.name.toLowerCase().includes(searchFoodTerm.value.toLowerCase()),
      )
      const start = (currentPage.value.foods - 1) * itemsPerPageFoods
      return filtered.slice(start, start + itemsPerPageFoods)
    })

    // tính tổng số trang cho bàn
    const totalPagesTables = computed(() =>
      Math.ceil(availableTables.value.length / itemsPerPageTables),
    )
    // tính tổng số trang cho món ăn
    const totalPagesFoods = computed(() => {
      const filtered = foods.value.filter((food) =>
        food.name.toLowerCase().includes(searchFoodTerm.value.toLowerCase()),
      )
      return Math.ceil(filtered.length / itemsPerPageFoods)
    })

    // hàm chuyển trang
    const goToPage = (page, key) => {
      if (
        page >= 1 &&
        page <= (key === 'tables' ? totalPagesTables.value : totalPagesFoods.value)
      ) {
        currentPage.value[key] = page
      }
    }

    const handleGuestSelection = () => {
      if (selectguest.value && selectguest.value.id !== 'guest') {
        guest_name.value = selectguest.value.guest_name
        guest_phone.value = selectguest.value.guest_phone
        guest_email.value = selectguest.value.guest_email
      } else {
        guest_name.value = ''
        guest_phone.value = ''
        guest_email.value = ''
      }
    }

    // hàm lấy tất cả người dùng
    const getAllUser = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/user')

        const guestDefaultOption = {
          id: 'guest',
          usernameEmail: 'Khách lẻ',
          guest_name: '',
          guest_phone: '',
          guest_email: '',
        }

        guest.value = [
          guestDefaultOption, //"Khách lẻ" ở đầu mảng
          ...res.data.user.map((g) => ({
            guest_name: g.user.fullname || g.user.username,
            guest_phone: g.user.phone,
            guest_email: g.user.email,
            usernameEmail: `${g.user.username} - ${g.user.phone}`,
          })),
        ]

        selectguest.value = guestDefaultOption
        handleGuestSelection()
      } catch (error) {
        console.error('Lỗi khi lấy danh sách người dùng:', error)
      }
    }

    // hàm tìm bàn
    const findTable = async () => {
      isLoading.value = true
      try {
        if (date.value && time.value && guest_count.value) {
          let rawDateTime = ''

          if (route.params.date) {
            rawDateTime = route.params.date
          } else if (date.value && time.value) {
            rawDateTime = `${date.value}T${time.value}:00`
          } else {
            toast.error('Vui lòng chọn ngày và giờ')
            return
          }

          const reservedFrom = new Date(rawDateTime)
          const reservedTo = new Date(reservedFrom.getTime() + 2 * 60 * 60 * 1000)

          const reserved_from = formatDateTime(reservedFrom)
          const reserved_to = formatDateTime(reservedTo)


          const res = await axios.post('http://127.0.0.1:8000/api/available-tables', {
            reserved_from: reserved_from,
            reserved_to: reserved_to,
            number_of_guests: guest_count.value,
          })
          availableTables.value = res.data.tables
        } else {
          const res = await axios.get('http://127.0.0.1:8000/api/tables')
          availableTables.value = res.data.tables
        }

        localStorage.removeItem('selectedDate')
      } catch (error) {
        toast.error('Lỗi khi lấy danh sách bàn')
        console.error('Lỗi:', error)
      } finally {
        isLoading.value = false
      }
    }

    const getChairCount = (seats) => {
      if (seats <= 2) return 1
      if (seats <= 4) return 2
      return 3
    }

    const filteredTimeOptions = computed(() => {
      if (!date.value) {
        return timeOptions.value
      }

      const selectedDate = new Date(date.value)
      const now = new Date()

      if (selectedDate.toDateString() === now.toDateString()) {
        return timeOptions.value.filter((timeStr) => {
          const [hours, minutes] = timeStr.split(':').map(Number)
          const timeDate = new Date(selectedDate)
          timeDate.setHours(hours, minutes, 0)

          return timeDate > now
        })
      }

      return timeOptions.value
    })

    const handleAddToCartClick = () => {
      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)
      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)

      let allSelectedToppings = []

      if (selectedSpicy) {
        allSelectedToppings.push({
          id: selectedSpicy.id,
          name: selectedSpicy.name,
          price: selectedSpicy.price,
          food_toppings_id: selectedSpicy.pivot?.id || null,
          is_spicy_level: true,
        })
      }

      const selectedToppingIds = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked'),
      ).map((el) => parseInt(el.value))

      const normalToppings = toppingList.value
        .filter((topping) => selectedToppingIds.includes(topping.id))
        .map((topping) => ({
          id: topping.id,
          name: topping.name,
          price: topping.price,
          food_toppings_id: topping.pivot?.id || null,
          is_spicy_level: false,
        }))

      allSelectedToppings = [...allSelectedToppings, ...normalToppings]

      addToCart(foodDetail.value, quantity.value, allSelectedToppings)
    }

    // hàm thêm/bỏ chọn bàn
    const toggleTable = (id) => {
      const alreadySelected = selectedTableIds.value.includes(id)

      let tempSelected = alreadySelected
        ? selectedTableIds.value.filter((tid) => tid !== id)
        : [...selectedTableIds.value, id]

      const numbers = availableTables.value
        .filter((table) => tempSelected.includes(table.id))
        .map((table) => table.table_number)
        .sort((a, b) => a - b)

      let consecutive = true
      for (let i = 1; i < numbers.length; i++) {
        if (numbers[i] !== numbers[i - 1] + 1) {
          consecutive = false
          break
        }
      }
      if (!consecutive && tempSelected.length > 1) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          title: 'Vui lòng chọn các bàn có số liền kề nhau!',
          icon: 'error',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
        });
        return
      }

      selectedTableIds.value = tempSelected
    }

    // hàm đặt bàn
    const reservation = async () => {
      isLoading.value = true
      let rawDateTime = ''

      if (route.params.date) {
        rawDateTime = route.params.date
      } else if (date.value && time.value) {
        rawDateTime = `${date.value}T${time.value}:00`
      } else {
        toast.error('Vui lòng chọn ngày và giờ')
        return
      }

      const reservedFrom = new Date(rawDateTime)
      const reservedTo = new Date(reservedFrom.getTime() + 2 * 60 * 60 * 1000)

      const reserved_from = formatDateTime(reservedFrom)
      const reserved_to = formatDateTime(reservedTo)

      try {
        await axios.post('http://127.0.0.1:8000/api/reservation', {
          user_id: selectguest.value.id === 'guest' ? null : selectguest.value.id,
          guest_name: guest_name.value,
          guest_phone: guest_phone.value,
          guest_email: guest_email.value,
          guest_count: guest_count.value,
          note: note.value,
          reserved_from: reserved_from,
          total_price: totalPrice.value + 100000,
          table_ids: selectedTableIds.value,
          order_detail: cartItems.value.map((item) => ({
            food_id: item.id,
            combo_id: null,
            quantity: item.quantity,
            price: item.price,
            type: item.type,
            toppings: item.toppings.map((t) => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price,
            })),
          })),
        }

        const orderCreationResponse = await axios.post('http://127.0.0.1:8000/api/reservation', reservationData)
        if (orderCreationResponse.data && orderCreationResponse.data.order_id) {
          current_order_id.value = orderCreationResponse.data.order_id
          await Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Đơn hàng đã được tạo thành công!',
            icon: 'success',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
          if (paymentMethod.value === 'VNPAY') {
            const paymentRes = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
              order_id: current_order_id.value,
              amount: 100000,
              return_url: 'http://localhost:5173/admin/tables/booking-schedule',
            })
            if (paymentRes.data.payment_url) {
              localStorage.setItem('payment_method', paymentMethod.value)
              // localStorage.removeItem(cartKey.value)
              await Swal.fire({
                title: 'Đang chuyển hướng sang VNPAY...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                  Swal.showLoading()
                }
              })
              window.location.href = paymentRes.data.payment_url
            } else {
              throw new Error('Không tạo được link thanh toán VNPAY!')
            }
            return
          }
          if (paymentMethod.value === 'MOMO') {
            await Swal.fire({
              toast: true,
              position: 'top-end',
              title: 'Chức năng thanh toán MoMo đang được phát triển!',
              icon: 'info',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true,
            });
            return;
          }
          if (paymentMethod.value === 'COD') {
            await new Promise((resolve) => setTimeout(resolve, 300))
            await axios.post('http://127.0.0.1:8000/api/payments/cod-payment', {
              order_id: current_order_id.value,
              amount_paid: 100000,
              payment_type: 'Thanh toán toàn bộ',
            })

            localStorage.setItem('payment_method', paymentMethod.value)
            await Swal.fire({
              toast: true,
              position: 'top-end',
              title: 'Thanh toán bàn bằng tiền mặt thành công!',
              icon: 'success',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true,
            });
            // router.push('/admin/tables/current-order');
          }
          clearCart();
          // guest_name.value = '';
          // guest_phone.value = '';
          // guest_email.value = '';
          // note.value = '';
          router.push('/admin/tables/booking-schedule');
        } else {
          await Swal.fire({
            toast: false,
            position: 'top-end',
            title: 'Lỗi: Không nhận được order_id từ server.',
            icon: 'error',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
          isLoading.value = false
          return
        }
        // toast.success('Đặt bàn thành công!')
        // clearCart()
      } catch (error) {
        console.error('Lỗi khi đặt bàn:', error)
        let errorMessage = 'Đặt bàn hoặc thanh toán thất bại, vui lòng thử lại!';
        let swalOptions = {
          toast: true,
          position: 'top-end',
          icon: 'error',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        };
        if (error.response && error.response.status === 422 && error.response.data.errors) {

          let validationMessages = [];
          for (const field in error.response.data.errors) {
            validationMessages.push(...error.response.data.errors[field]);
          }
          errorMessage = validationMessages.join('\n');

          swalOptions = {
            toast: false,
            // position: 'center',
            title: 'Lỗi xác thực dữ liệu!',
            html: errorMessage,
            icon: 'error',
            showConfirmButton: true, // Cần nút xác nhận để người dùng tự đóng
            timer: undefined, // Không tự đóng
            timerProgressBar: false,
          };
        } else if (error.response && error.response.data && error.response.data.message) {
          errorMessage = error.response.data.message;
        }
        await Swal.fire({
          ...swalOptions,
          title: swalOptions.title || errorMessage,
          text: swalOptions.html ? undefined : errorMessage
        });
      } finally {
        isLoading.value = false
      }
    }
    const handlePayment = async () => {
      isLoading.value = true;
      try {
        if (!guest_name.value) {
          await Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Vui lòng nhập đầy đủ thông tin khách hàng!',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
          return
        }
        if (cartItems.value.length === 0) {
          await Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Giỏ hàng trống! Vui lòng thêm món ăn!',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
          return
        }
        if (!paymentMethod.value) {
          await Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Vui lòng chọn phương thức thanh toán!',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
          return;
        }
        if (!current_order_id.value) {
          await Swal.fire({
            toast: true,
            position: 'top-end',
            title: 'Không có đơn hàng nào để thanh toán. Vui lòng tạo đơn hàng trước!',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
          isLoading.value = false;
          return;
        }
        if (paymentMethod.value === 'VNPAY') {
          const paymentRes = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
            order_id: current_order_id.value,
            amount: totalPrice.value,
            return_url: 'http://localhost:5173/admin/tables/current-order',
          })
          if (paymentRes.data.payment_url) {
            localStorage.setItem('payment_method', paymentMethod.value)
            // localStorage.removeItem(cartKey.value)
            window.location.href = paymentRes.data.payment_url
          } else {
            toast.error('Không tạo được link thanh toán VNPAY.')
          }
          clearCart();
          guest_name.value = '';
          note.value = '';
          router.push('/admin/tables/booking-schedule');
          return
        }
        if (paymentMethod.value === 'MOMO') {
          toast.info('Chức năng thanh toán MoMo đang được phát triển!');
          // localStorage.setItem('payment_method', paymentMethod.value);
          return;
        }
        if (paymentMethod.value === 'COD') {
          await new Promise((resolve) => setTimeout(resolve, 300))
          await axios.post('http://127.0.0.1:8000/api/payments/cod-payment', {
            order_id: current_order_id.value,
            amount_paid: totalPrice.value,
            payment_type: 'Thanh toán toàn bộ',
          })

          localStorage.setItem('payment_method', paymentMethod.value)
          // localStorage.removeItem(cartKey.value)
          toast.success('Thanh toán bàn bằng tiền mặt thành công!')
          clearCart();
          router.push('/admin/tables/booking-schedule');
        }

      } catch {

      }
    }
    watch(selectguest, handleGuestSelection)
    watch(selectfood, (newValue) => {
      if (newValue === null) {
        searchFoodTerm.value = ''
      } else {
        searchFoodTerm.value = newValue.name || ''
      }
    })
    onMounted(() => {
      getAllUser()
      findTable()

      time.value = formatTime(localStorage.getItem('selectedDate')) || ''
      date.value = formatDate(localStorage.getItem('selectedDate')) || ''

      selectguest.value = 'guest'
      loadCart()
      for (let hour = 1; hour <= 21; hour++) {
        let hourStr = hour < 10 ? '0' + hour : '' + hour
        timeOptions.value.push(hourStr + ':00')
        if (hour !== 20) {
          timeOptions.value.push(hourStr + ':30')
        }
      }
    })

    return {
      time,
      today,
      timeOptions,
      note,
      table_id,
      date,
      isLoading,
      info,
      getInfo,
      formatDateTime,
      findTable,
      availableTables,
      filteredTimeOptions,
      getChairCount,
      formatDate,
      formatTime,
      createReservationOnly,
      handlePayment,
      guest_email,
      user_id,
      selectguest,
      guest_phone,
      guest_name,
      guest_count,
      toggleTable,
      selectedTableIds,
      guest,
      paymentMethod,

      currentPage,
      paginatedTables,
      goToPage,
      foods,
      formatNumber,
      getImageUrl,
      getFoodByCategory,
      openModal,
      spicyLevel,
      toppingList,
      flatCategoryList,
      foodDetail,
      addToCart,
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
      paginatedFoods,
      totalPagesTables,
      totalPagesFoods,
      handleGuestSelection,
      selectfood,
      searchFoodTerm,
      onFoodSearch,
      handleAddToCartClick,
      clearCart,
      hasPermission,
      userId
    }
  },
}
</script>
<style>
.bi-plus-circle-fill {
  font-size: 40px;
  color: rgb(146, 145, 145) !important;
  display: flex;
  justify-content: center;
  align-items: center;
}

.themsp {
  width: 200px;
}

.img_thumbnail {
  width: 50px;
}

.btn-danger-delete {
  background-color: #c92c3c;
  color: white;
}

.btn-danger-delete:hover {
  background-color: #a51928;
  color: white;
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

.table-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-top: 20px;
  justify-content: center;
}

.table-rect1.medium {
  min-width: 120px;
}

.table-rect1.large {
  min-width: 160px;
}

.table-block {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.table-block:hover {
  cursor: pointer;
}

.chairs {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 5px 0;
}

.chair {
  width: 40px;
  height: 6px;
  background-color: #ddd;
  border-radius: 3px;
}

.table-rect {
  /* background-color: #f4f4f4; */
  color: rgb(81, 73, 73);
  padding: 10px 20px;
  border-radius: 10px;
  text-align: center;
  border: 5px solid #ddd;
  min-width: 80px;
  font-weight: bold;
  font-size: 16px;
}

.table-rect1 {
  background-color: rgb(226, 225, 225);
  color: rgb(81, 73, 73);
  padding: 10px 20px;
  border-radius: 10px;
  text-align: center;
  border: 5px solid #ddd;
  min-width: 80px;
  font-weight: bold;
  font-size: 16px;
}

.table-rect.medium {
  min-width: 120px;
}

.table-rect.large {
  min-width: 160px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .table-container {
    gap: 10px;
    justify-content: center;
  }

  .table-block {
    flex: 1 1 100px;
  }

  .chair {
    width: 30px;
  }

  .table-rect {
    font-size: 0.85rem;
  }
}

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
  flex: 0 0 calc(50% - 0.5rem);
  max-width: calc(50% - 0.5rem);
  padding: 0.5rem;
  margin: 0.25rem;
  box-sizing: border-box;
}

.box {
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
  background-color: #fffefe;
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
