<template>
  <section class="foods">
    <div class="main-page-foods container">
      <div class="row d-flex align-items-center justify-content-between">
        <div class="col-md-6 d-none d-lg-flex justify-content-center align-items-center">
          <span class="fw-bold text-center align-items-center justify-content-center">Thực đơn</span>
        </div>
        <div class="col-6 d-none d-lg-flex flex-wrap justify-content-center">
          <ul class="menu-grid">
            <li v-for="parent in categories" :key="parent.id" class="menu-item">
              <a @click.prevent="getFoodByCategory(parent.id)" class="menu-link" href="#">
                {{ parent.name }}
              </a>
              <ul v-if="parent.children && parent.children.length" class="submenu">
                <li v-for="child in parent.children" :key="child.id">
                  <a @click.prevent="getFoodByCategory(child.id)" href="#" class="submenu-link fw-bold text-center">
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
        <div class="menu-header d-flex justify-content-between align-items-center" @click="toggleDropdown">
          <h2 class="menu-title">Thực đơn</h2>
          <div class="menu-icon d-flex align-items-center">
            <i class="fas fa-list-alt"></i>
            <span>Danh mục</span>
          </div>
        </div>

        <div :class="{ collapse: !isDropdownOpen, show: isDropdownOpen }" class="menu-dropdown">
          <ul class="list-group">
            <li v-for="parent in categories" :key="parent.id" class="list-group-item parent-category d-flex">
              <a @click.prevent="getFoodByCategory(parent.id)" href="#" class="text-decoration-none text-start">
                {{ parent.name }}
              </a>
              <ul v-if="parent.children && parent.children.length" class="list-group ms-3">
                <li v-for="child in parent.children" :key="child.id" class="list-group-item child-category d-flex">
                  <a @click.prevent="getFoodByCategory(child.id)" href="#" class="text-decoration-none text-start">
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
              <img src="/img/item/imgmenu.webp" alt="dish-images" />
            </div>

            <!---->
            <div class="col-12 col-md-6 title-dish">
              <p class="text-end">{{ selectedCategoryName || 'Món Ăn' }}</p>
            </div>
          </div>
          <div class="product-list-wrapper container-fluid">
            <div class="row">
              <div v-for="item in foods" :key="item" @click="openModal(item)" class="col-md-3">
                <div class="product-card">
                  <img :src="getImageUrl(item.image)" alt="" class="product-img mx-auto d-block" width="180px" />
                  <h3 class="product-dish-title text-center fw-bold">{{ item.name }}</h3>
                  <span class="product-dish-desc text-start">
                    {{ item.description }}
                  </span>
                  <p class="product-dish-price fw-bold text-center">{{ formatNumber(item.price) }} VNĐ</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </section>

  <section class="section-banner m-3">
    <div class="banner-deals container-fluid">
      <img :src="images[currentIndex]" alt="banner" class="img-fluid"
        style="border-radius: 25px; transition: opacity 0.5s ease" />
      <button @click="changeSlide(-1)" class="trans-left d-none d-lg-block">
        <i class="fa-solid fa-arrow-left" style="color: #ffffff"></i>
      </button>
      <button @click="changeSlide(1)" class="trans-right d-none d-lg-block">
        <i class="fa-solid fa-arrow-right" style="color: #ffffff"></i>
      </button>
    </div>
  </section>
  <section class="pots-section container">
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
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content custom-modal modal-ct">
      <div class="modal-body position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
          aria-label="Close"></button>
        <div class="row">

          <div class="col-md-6 border-end">
            <h5 class="fw-bold text-danger text-center mb-3">{{ foodDetail.name }}</h5>

            <div class="text-center mb-3">
              <img :src="getImageUrl(foodDetail.image)" :alt="foodDetail.name" class="modal-image img-fluid" />
            </div>

            <p class="text-danger fw-bold fs-5 text-center">{{ formatNumber(foodDetail.price) }} VNĐ</p>
            <p class="text-dark text-center text-lg fw-bold mb-3">{{ foodDetail.description }}</p>
          </div>
          <div class="col-md-6">
            <form @submit.prevent="addToCart">


              <div class="topping-container mb-3" v-if="toppingList.length">
                <div class="mb-3" v-if="spicyLevel.length">
                <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                <select class="form-select" id="spicyLevel">
                  <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                    {{ item.name }}
                  </option>
                </select>
              </div>
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
              <div class="text-center mb-2">
              <div class="qty-control px-2 py-1">
                <button @click="decreaseQuantity" type="button" class="btn-lg" style="background-color: #fff;">-</button>
                <span>{{ quantity }}</span>
                <button @click="increaseQuantity" type="button" class="btn-lg" style="background-color: #fff;">+</button>
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
  </div>
</div>
</template>
<script>
import axios from 'axios'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import numeral from 'numeral'
import { Modal } from 'bootstrap'

export default {
  name: 'HomePage',
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },
  setup() {
    const foods = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const toppings = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})

    const quantity = ref(1);

    const isLoading = ref(false)
    const isDropdownOpen = ref(false)
    const selectedCategoryName = ref('Món Ăn')

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
        const res = await axios.get(`http://127.0.0.1:8000/api/home/categories`)
        categories.value = res.data
        categories.value.shift()
      } catch (error) {
        console.error(error)
      }
    }

    const getFood = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/foods`)
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

        const res = await axios.get(`http://127.0.0.1:8000/api/home/category/${categoryId}/food`)
        let allFoods = res.data.map((item) => ({ ...item, type: 'food' }))

        let parentName = ''
        let childName = ''

        for (const parent of categories.value) {
          if (parent.id === categoryId) {
            parentName = parent.name
            break
          }
          if (parent.children && parent.children.length) {
            const child = parent.children.find((c) => c.id === categoryId)
            if (child) {
              parentName = parent.name
              childName = child.name
              break
            }
          }
        }

        selectedCategoryName.value = childName ? `${parentName} > ${childName}` : parentName || 'Món Ăn'

        const selectedCategory = categories.value.find((c) => c.id === categoryId)
        if (selectedCategory?.children?.length) {
          const childRequests = selectedCategory.children.map((child) =>
            axios.get(`http://127.0.0.1:8000/api/home/category/${child.id}/food`),
          )
          const childResults = await Promise.all(childRequests)
          childResults.forEach((childRes) => {
            const childFoods = childRes.data.map((item) => ({ ...item, type: 'food' }))
            allFoods = [...allFoods, ...childFoods]
          })
        }

        if (categoryId === 14) {
          const comboRes = await axios.get(`http://127.0.0.1:8000/api/home/combos`)
          const combosWithType = comboRes.data.map((item) => ({ ...item, type: 'combo' }))
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
          const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${item.id}`)
          foodDetail.value = res.data

          const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${item.id}`)
          toppings.value = res1.data

          spicyLevel.value = toppings.value.filter((item) => item.category_id == 1)
          toppingList.value = toppings.value.filter((item) => item.category_id == 2)
          toppingList.value.forEach((item) => {
            item.price = item.price || 0
          })
        } else if (item.type === 'combo') {
          const res = await axios.get(`http://127.0.0.1:8000/api/home/combo/${item.id}`)
          foodDetail.value = res.data
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
      const cartKey = `cart_${userId}`

      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)
      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)
      const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không cay'

      const selectedToppingId = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked')
      ).map((el) => parseInt(el.value))

      const selectedToppings = toppingList.value
        .filter((topping) => selectedToppingId.includes(topping.id))
        .map((topping) => ({
          id: topping.id,
          name: topping.name,
          price: topping.price,
          food_toppings_id: topping.pivot?.id || null
        }))

      const cartItem = {
        id: foodDetail.value.id,
        name: foodDetail.value.name,
        image: foodDetail.value.image,
        price: foodDetail.value.price,
        spicyLevel: selectedSpicyName,
        toppings: selectedToppings,
        quantity: 1,
      }

      let cart = JSON.parse(localStorage.getItem(cartKey)) || []

      const existingItem = cart.findIndex(
        (item) =>
          item.id === cartItem.id &&
          item.spicyLevel === cartItem.spicyLevel &&
          JSON.stringify(item.toppings.sort()) === JSON.stringify(cartItem.toppings.sort())
      )

      if (existingItem !== -1) {
        cart[existingItem].quantity += 1
      } else {
        cart.push(cartItem)
      }

      localStorage.setItem(cartKey, JSON.stringify(cart))
      alert('Đã thêm vào giỏ hàng!')
    }

    onMounted(async () => {
      await getCategory()
      await getFood()
      intervalId = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % images.length
      }, 3000)
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
      currentIndex,
      images,
      getFoodByCategory,
      openModal,
      addToCart,
      toggleDropdown,
      changeSlide,
      increaseQuantity,
      decreaseQuantity,
      quantity
    }
  },
}
</script>
<style scoped>
.menu-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-item {
  position: relative;
}

.menu-link {
  font-weight: bold;
  font-size: 28px;
  text-decoration: none;
  display: inline-block;
  transition: color 0.3s ease, transform 0.3s ease;
}

.menu-link:hover {
  color: #c92c3c;
  transform: scale(1.05);
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
  transition: opacity 0.3s ease, transform 0.3s ease;
  border-radius: 8px;
  min-width: 180px;
}

.menu-item:hover .submenu {
  display: block;
  opacity: 1;
  transform: translateY(0) scale(1);
}

.submenu-link {
  display: block;
  padding: 0.5rem 1rem;
  text-decoration: none;
  font-size: 20px;
  font-weight: bold;
  transition: color 0.3s ease, background-color 0.3s ease, transform 0.3s ease;
}

.submenu-link:hover {
  background-color: #f8f9fa;
  color: #c92c3c;
  transform: scale(1.05);
  font-weight: 500;
}
</style>
