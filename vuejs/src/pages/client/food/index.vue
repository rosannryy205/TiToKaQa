<template>
  <section class="foods">
    <div class="main-page-foods container">
      <div class="row d-flex align-items-center justify-content-between">
        <div class="col-md-6 d-none d-lg-flex justify-content-center align-items-center">
          <span class="fw-bold text-center align-items-center justify-content-center"
            >Thực đơn</span
          >
        </div>
        <div class="col-6 d-none d-lg-flex flex-wrap justify-content-center">
          <ul class="menu-grid">
            <li v-for="parent in categories" :key="parent.id" class="menu-item">
              <a @click.prevent="getFoodByCategory(parent.id)" class="menu-link" href="#">
                {{ parent.name }}
              </a>
              <ul v-if="parent.children && parent.children.length" class="submenu">
                <li v-for="child in parent.children" :key="child.id">
                  <a
                    @click.prevent="getFoodByCategory(child.id)"
                    href="#"
                    class="submenu-link fw-bold text-center"
                  >
                    {{ child.name }}
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <!--small-->
      <div class="col-12 d-lg-none position-relative">
        <div
          class="menu-header d-flex justify-content-between align-items-center mt-3"
          @click="toggleDropdown"
        >
          <h2 class="menu-title">Thực đơn</h2>
          <div class="menu-icon d-flex align-items-center">
            <i class="fas fa-list-alt"></i>
            <span>Danh mục</span>
          </div>
        </div>

        <div :class="{ collapse: !isDropdownOpen, show: isDropdownOpen }" class="menu-dropdown">
          <ul class="list-group">
            <li
              v-for="parent in categories"
              :key="parent.id"
              class="list-group-item parent-category d-flex"
            >
              <a
                @click.prevent="getFoodByCategory(parent.id)"
                href="#"
                class="text-decoration-none text-start text-dark fw-bold"
              >
                {{ parent.name }}
              </a>
              <ul v-if="parent.children && parent.children.length" class="list-group ms-3">
                <li
                  v-for="child in parent.children"
                  :key="child.id"
                  class="list-group-item child-category d-flex"
                >
                  <a
                    @click.prevent="getFoodByCategory(child.id)"
                    href="#"
                    class="text-decoration-none text-start text-dark fw-bold"
                  >
                    🔻{{ child.name }}
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!---->
    <div class="background-banner"></div>
    <section class="all-dish-by-category container">
      <div class="dish-by-category">
        <div class="row custom-row">
          <div class="col-12 images-dish d-flex justify-content-between flex-wrap">
            <!---->
            <div class="col-md-6 d-none d-md-block img-dish">
              <img
                :src="getImageMenuUrl(selectedCategoryImage || 'mycay.png')"
                alt="dish-images"
                class="img-fluid"
              />
            </div>

            <!---->
            <div class="col-12 col-md-6 title-dish">
              <p class="text-end">{{ selectedCategoryName || 'Món Ăn' }}</p>
            </div>
          </div>
          <div class="product-list-wrapper container-fluid">
            <div class="row">
              <template v-if="isLoading">
                <div v-for="n in 8" :key="'skeleton-' + n" class="col-md-3 mb-4">
                  <div class="product-card skeleton-card p-3">
                    <div class="skeleton-img mb-3"></div>
                    <div class="skeleton-line w-100 mb-2"></div>
                    <div class="skeleton-line w-75 mb-2"></div>
                    <div class="skeleton-line w-50 mx-auto"></div>
                  </div>
                </div>
              </template>

              <!-- Real Product -->
              <template v-else>
                <div
                  v-for="item in foods"
                  :key="item"
                  @click="openModal(item)"
                  class="col-md-3 mb-4"
                >
                  <div class="product-card position-relative">
                    <div v-if="isAvailableInFlashSale(item)" class="flash-sale-ribbon">
                      <span>🔥 Flash Sale</span>
                    </div>

                    <img
                      :src="getImageUrl(item.image)"
                      alt=""
                      class="product-img mx-auto d-block"
                      width="180px"
                    />
                    <h3 class="product-dish-title text-center fw-bold">{{ item.name }}</h3>
                    <span class="product-dish-desc text-start">
                      {{ item.description }}
                    </span>
                    <p class="text-center mt-1 fw-bold" v-if="isAvailableInFlashSale(item)">
                      <span class="text-danger">
                        Đã bán: {{ item.flash_sale_sold }}/{{ item.flash_sale_quantity }} sản phẩm
                      </span>
                    </p>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </section>
  </section>

  <section class="section-banner m-3 mt-5">
    <div class="banner-deals container-fluid">
      <img
        :src="images[currentIndex]"
        alt="banner"
        class="img-fluid"
        style="border-radius: 25px; transition: opacity 0.5s ease"
      />
      <button @click="changeSlide(-1)" class="trans-left d-none d-lg-block">
        <i class="fa-solid fa-arrow-left" style="color: #ffffff"></i>
      </button>
      <button @click="changeSlide(1)" class="trans-right d-none d-lg-block">
        <i class="fa-solid fa-arrow-right" style="color: #ffffff"></i>
      </button>
    </div>
  </section>

  <section class="pots-section container mt-5">
    <h2 class="text-center text-md-start mb-3 fw-bold">Bài Viết & Thông Tin<span>📢</span></h2>
    <hr />
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
      <div class="col img-post">
        <img src="/img/bv1.webp" alt="post1" class="img-fluid rounded" />
      </div>
      <div class="col img-post">
        <img src="/img/bv2.png" alt="post2" class="img-fluid rounded" />
      </div>
      <div class="col img-post">
        <img src="/img/bv3.png" alt="post3" class="img-fluid rounded" />
      </div>
      <div class="col img-post">
        <img src="/img/bv1.webp" alt="post4" class="img-fluid rounded" />
      </div>
    </div>
  </section>
  <!-- Modal -->
  <div class="modal fade" id="productModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content custom-modal modal-ct">
        <div class="modal-body position-relative">
          <button
            type="button"
            class="btn-close position-absolute top-0 end-0 m-2"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
          <div class="row">
            <div class="col-md-6 border-end">
              <h5 class="fw-bold text-danger text-center mb-3">{{ foodDetail.name }}</h5>
              <h5 v-if="false">{{ foodDetail.category_id }}</h5>
              <div class="text-center mb-3">
                <img :src="getImageUrl(foodDetail.image)" :alt="foodDetail.name" class="modal-image img-fluid" />
              </div>
              <p class="text-danger fw-bold fs-5 text-center">
                {{ formatNumber(foodDetail.price) }} VNĐ
              </p>
              <p class="text-dark text-center text-lg fw-bold mb-3">{{ foodDetail.description }}</p>
            </div>
            <div class="col-md-6 d-flex flex-column">
              <form @submit.prevent="addToCart" class="d-flex flex-column h-100">
                <div class="flex-grow-1">
                  <div class="topping-container mb-3" v-if="toppingList.length
                    || spicyLevel.length">
                    <div class="mb-3" v-if="spicyLevel.length">
                      <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                      <select class="form-select" id="spicyLevel">
                        <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                    <label v-if="toppingList.length" class="form-label fw-bold">🧀 Chọn Topping:</label>
                    <div v-for="topping in toppingList" :key="topping.id"
                      class="d-flex justify-content-between align-items-center mb-2">
                      <label class="d-flex align-items-center">
                        <input type="checkbox" :value="topping.id" name="topping[]" class="me-2" />
                        {{ topping.name }}
                      </label>
                      <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
                    </div>
                  </div>
                  <div v-else class="mt-5 none-topping">
                    <p class="text-center text-muted">Không có topping cho món này.</p>
                  </div>
                </div>

                <!---->
                <div class="mt-auto">
                  <div class="text-center mb-2">
                    <div class="qty-control px-2 py-1">
                      <button
                        type="button"
                        @click="decreaseQuantity"
                        class="btn-lg"
                        style="background-color: #fff"
                      >
                        -
                      </button>
                      <span>{{ quantity }}</span>
                      <button
                        type="button"
                        @click="increaseQuantity"
                        class="btn-lg"
                        style="background-color: #fff"
                      >
                        +
                      </button>
                    </div>
                  </div>
                  <button class="btn btn-danger w-100 fw-bold">🛒 Thêm vào giỏ hàng</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div
    class="fixed-element d-flex align-items-center justify-content-between px-4"
    v-if="isReservation && orderId"
  >
    <div class="scrolling-container">
      <div class="scrolling-text">✨ Chọn món cho đơn đặt bàn của bạn! ✨</div>
    </div>
    <router-link :to="`/reservation-form/${orderId}`" class="btn-confirm text-decoration-none"
      >Xác nhận chọn xong</router-link
    >
  </div>
</template>
<script>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import axios from 'axios'
import numeral from 'numeral'
import { Modal } from 'bootstrap'
import { useRoute } from 'vue-router'
import Swal from 'sweetalert2'

import { API_URL } from '@/config'

export default {
  name: 'HomePage',
  methods: {
    isAvailableInFlashSale(item) {
      return (
        this.isInFlashSaleTime(item) &&
        item.flash_sale_price &&
        item.flash_sale_quantity > item.flash_sale_sold
      )
    },
    isInFlashSaleTime(item) {
      if (!item.flash_sale_start || !item.flash_sale_end) return false
      const now = new Date()
      const start = new Date(item.flash_sale_start)
      const end = new Date(item.flash_sale_end)
      return now >= start && now <= end
    },
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
    getImageMenuUrl(image) {
      return `/img/food/imgmenu/${image}`
    },
  },
  setup() {
    const foods = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const toppings = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})

    const quantity = ref(1)

    const route = useRoute()
    const orderId = route.params.orderId
    const isReservation = computed(() => {
      return route.name && String(route.name).includes('reservation')
    })

    const isLoading = ref(true)
    const isDropdownOpen = ref(false)
    const selectedCategoryName = ref('Món Ăn')
    const selectedCategoryImage = ref('')

    const currentIndex = ref(0)
    const images = [
      '/img/banner/Banner (1).webp',
      '/img/banner/Banner (2).png',
      '/img/banner/Banner.png',
    ]
    let intervalId = null

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }

    const changeSlide = (direction) => {
      const total = images.length
      currentIndex.value = (currentIndex.value + direction + total) % total
    }

    const getCategory = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/categories`)
        categories.value = res.data
        categories.value.shift()
      } catch (error) {
        console.error(error)
      }
    }

    const getFood = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/foods`)
        foods.value = res.data.map((item) => ({ ...item, type: 'food' }))
      } catch (error) {
        console.error(error)
      }
    }

    const getFoodByCategory = async (categoryId) => {
      try {
        if (!categories.value.length) {
          console.warn('Categories chưa được load.')
          return
        }

        const res = await axios.get(`${API_URL}/home/category/${categoryId}/food`)
        let allFoods = res.data.map((item) => ({ ...item, type: 'food' }))

        let parentName = ''
        let childName = ''
        let parentImage = ''

        for (const parent of categories.value) {
          if (parent.id === categoryId) {
            parentName = parent.name
            parentImage = parent.images || ''
            break
          }
          if (parent.children && parent.children.length) {
            const child = parent.children.find((c) => c.id === categoryId)
            if (child) {
              parentName = parent.name
              childName = child.name
              parentImage = child.images || ''
              if (!child.images) {
                parentImage = parent.images
              }
              break
            }
          }
        }

        selectedCategoryName.value = childName
          ? `${parentName} > ${childName}`
          : parentName || 'Món Ăn'
        selectedCategoryImage.value = parentImage

        const selectedCategory = categories.value.find((c) => c.id === categoryId)
        if (selectedCategory?.children?.length) {
          const childRequests = selectedCategory.children.map((child) =>
            axios.get(`${API_URL}/home/category/${child.id}/food`),
          )
          const childResults = await Promise.all(childRequests)
          childResults.forEach((childRes) => {
            const childFoods = childRes.data.map((item) => ({ ...item, type: 'food' }))
            allFoods = [...allFoods, ...childFoods]
          })
        }

        if (categoryId === 14) {
          const comboRes = await axios.get(`${API_URL}/home/combos`)
          const combosWithType = comboRes.data.all.map((item) => ({ ...item, type: 'combo' }))
          allFoods = [...allFoods, ...combosWithType]
        }

        foods.value = allFoods
      } catch (error) {
        console.error(error)
      }
    }

    const openModal = async (item) => {
      foodDetail.value = {}
      toppings.value = []
      spicyLevel.value = []
      toppingList.value = []
      quantity.value = 1
      try {
        if (item.type === 'food') {
          const res = await axios.get(`${API_URL}/home/food/${item.id}`)
          foodDetail.value = { ...res.data, type: 'food' }

          const res1 = await axios.get(`${API_URL}/home/topping/${item.id}`)
          toppings.value = res1.data

          spicyLevel.value = toppings.value.filter((item) => item.category_id == 15)
          toppingList.value = toppings.value.filter((item) => item.category_id == 16)
          toppingList.value.forEach((item) => {
            item.price = item.price || 0
          })
        } else if (item.type === 'combo') {
          const res = await axios.get(`${API_URL}/home/combo/${item.id}`)
          foodDetail.value = { ...res.data, type: 'combo' }
        }

        const modalElement = document.getElementById('productModal')
        if (modalElement) {
          const modal = new Modal(modalElement)
          modal.show()
        }
      } catch (error) {
        console.error(error)
      }
    }

    const decreaseQuantity = () => {
      if (quantity.value > 1) {
        quantity.value -= 1
      }
    }

    const increaseQuantity = () => {
      quantity.value += 1
    }

    const addToCart = () => {
      const user = JSON.parse(localStorage.getItem('user'))
      const userId = user?.id || 'guest'
      const cartKey = orderId ? `cart_${userId}_reservation_${orderId}` : `cart_${userId}`

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

      let cart = JSON.parse(localStorage.getItem(cartKey)) || []

      const toppingsKey = allSelectedToppings
        .map((t) => t.id)
        .sort()
        .join(',')

      const existingItemIndex = cart.findIndex(
        (item) =>
          item.id === foodDetail.value.id &&
          item.toppings
            .map((t) => t.id)
            .sort()
            .join(',') === toppingsKey,
      )
      let applyPrice = foodDetail.value.price
      const isFlashSale =
        foodDetail.value.flash_sale_price !== undefined &&
        foodDetail.value.flash_sale_price !== null

      if (isFlashSale) {
        const countSameFlashSaleItems = cart.filter(
          (item) =>
            item.id === foodDetail.value.id &&
            item.toppings
              .map((t) => t.id)
              .sort()
              .join(',') === toppingsKey &&
            item.price === foodDetail.value.flash_sale_price,
        ).length
        if (countSameFlashSaleItems === 0) {
          applyPrice = foodDetail.value.flash_sale_price
        }
      }

      const cartItem = {
        id: foodDetail.value.id,
        name: foodDetail.value.name,
        image: foodDetail.value.image,
        price: applyPrice,
        original_price: foodDetail.value.price,
        toppings: allSelectedToppings,
        quantity: quantity.value,
        type: foodDetail.value.type,
        category_id: foodDetail.value.category_id,
        is_flash_sale: isFlashSale && applyPrice === foodDetail.value.flash_sale_price,
      }

      if (existingItemIndex !== -1 && cart[existingItemIndex].price === cartItem.price) {
        cart[existingItemIndex].quantity += cartItem.quantity
      } else {
        cart.push(cartItem)
      }

      localStorage.setItem(cartKey, JSON.stringify(cart))
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Đã thêm món vào giỏ hàng!',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      })
    }

    /**tinh % giam tu flashsale */
    function calculateDiscount(originalPrice, salePrice) {
      if (!originalPrice || !salePrice) return 0
      const discount = ((originalPrice - salePrice) / originalPrice) * 100
      return Math.round(discount)
    }

    onMounted(async () => {
      await getCategory()
      await getFood()
      intervalId = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % images.length
      }, 3000)
    })
    onMounted(async () => {
      isLoading.value = true

      try {
        const res = await axios.get(`${API_URL}/home/api/foods`)
        foods.value = res.data
        await new Promise((resolve) => setTimeout(resolve, 5000))
      } catch (e) {
        console.error(e)
      } finally {
        isLoading.value = false
      }
    })

    onBeforeUnmount(() => {
      clearInterval(intervalId)
    })

    return {
      foods,
      categories,
      foodDetail,
      toppings,
      spicyLevel,
      toppingList,
      isLoading,
      isDropdownOpen,
      selectedCategoryName,
      selectedCategoryImage,
      currentIndex,
      images,
      getFoodByCategory,
      openModal,
      toggleDropdown,
      changeSlide,
      increaseQuantity,
      decreaseQuantity,
      quantity,
      orderId,
      isReservation,
      calculateDiscount,
      addToCart,
    }
  },
}
</script>

<style scoped>
.fixed-element {
  position: fixed;
  top: 157px;
  left: 50%;
  transform: translateX(-50%);
  width: 84%;
  background-color: #cc2c40;
  color: #fff;
  font-weight: bold;
  border-radius: 0 0 20px 20px;
  padding: 10px 20px;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.scrolling-container {
  flex: 1;
  overflow: hidden;
  white-space: nowrap;
}

.scrolling-text {
  display: inline-block;
  padding-left: 100%;
  animation: scroll-left 10s linear infinite;
  font-size: 18px;
  white-space: nowrap;
}

@keyframes scroll-left {
  0% {
    transform: translateX(0%);
  }

  100% {
    transform: translateX(-100%);
  }
}

.btn-confirm {
  background-color: #fff;
  color: #cc2c40;
  font-weight: bold;
  border: none;
  padding: 6px 14px;
  border-radius: 6px;
  cursor: pointer;
  white-space: nowrap;
  transition: background-color 0.3s;
}

.btn-confirm:hover {
  background-color: #f8d7da;
}

.btn-confirm {
  background-color: #fff;
  color: #cc2c40;
  font-weight: bold;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
  cursor: pointer;
}

.btn-confirm:hover {
  background-color: #f8d7da;
}

.menu-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  list-style: none;
  padding: 0;
}

.menu-item {
  position: relative;
}

.menu-link {
  color: #c92c3c;
  font-weight: bold;
  font-size: 20px;
  text-decoration: none;
  display: inline-block;
  transition:
    color 0.3s ease,
    transform 0.3s ease;
}

.submenu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  list-style: none;
  margin: 0;
  padding: 0.5rem 0;
  display: none;
  z-index: 1000;
  opacity: 0;
  transform: translateY(10px) scale(0.95);
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
  border-radius: 8px;
  min-width: 180px;
}

.menu-item:hover .submenu {
  display: block;
  opacity: 1;
  transform: translateY(0) scale(1);
}

.submenu-link {
  color: #c92c3c;
  display: block;
  padding: 0.5rem 1rem;
  text-decoration: none;
  font-size: 18px;
  font-weight: bold;
  transition:
    color 0.3s ease,
    background-color 0.3s ease,
    transform 0.3s ease;
}

.submenu-link:hover {
  background-color: #d5d5d565;
}

@keyframes pulse {
  0% {
    background-color: #f7f7f7;
  }

  50% {
    background-color: #ffffff;
  }

  100% {
    background-color: #f7f7f7;
  }
}

.skeleton-card {
  border-radius: 10px;
  animation: pulse 1.5s infinite ease-in-out;
  background: #ffffff;
  height: 280px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 1rem;
}

.skeleton-img {
  width: 100%;
  height: 120px;
  background: #f0f0f0;
  border-radius: 10px;
}

.skeleton-line {
  height: 16px;
  background: #e5e5e5;
  border-radius: 4px;
  margin-top: 0.5rem;
}
/**gach giua */
del {
  text-decoration: line-through !important;
}
.flash-sale-tag {
  animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
  0% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
  }
  50% {
    transform: scale(1.05);
    box-shadow: 0 0 10px 5px rgba(220, 53, 69, 0);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
  }
}
.product-card-wrapper {
  perspective: 1000px;
}

.flash-sale-ribbon {
  width: 150px;
  height: 32px;
  position: absolute;
  top: 15px;
  left: -40px;
  z-index: 1;
  transform: rotate(-45deg);
  background: linear-gradient(45deg, #d82c2c, #f85032);
  color: white;
  font-weight: bold;
  text-align: center;
  line-height: 32px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}
.flash-sale-ribbon::before,
.flash-sale-ribbon::after {
  content: '';
  position: absolute;
  border-top: 4px solid #a51d1d;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
}

.flash-sale-ribbon::before {
  left: 0;
  bottom: -4px;
}

.flash-sale-ribbon::after {
  right: 0;
  bottom: -4px;
}
.product-card-wrapper:hover .flash-sale-ribbon {
  transform: rotate(-45deg) scale(1.1);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
}
</style>
