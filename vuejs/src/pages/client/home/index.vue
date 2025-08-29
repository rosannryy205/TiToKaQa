<template>
  <!--main-home-->
  <section class="main d-flex justify-content-center align-items-center">
    <div class="container text-center">
      <div class="row justify-content-center align-items-stretch">
        <div class="col-4 d-none d-lg-block pt-5 text-start">
          <div class="title-foods fs-5 mt-5">
            <span class="fw-bold text-dark">Lẩu và Mỳ cay 7 cấp độ</span>
          </div>
          <div class="title-shops d-none d-md-block fw-bold">
            <span class="fw-bold">TITOKAQA</span>
          </div>
          <div class="title-infors fw-normal pe-5 text-dark">
            <span>TITOKAQA là chuỗi nhà hàng thương hiệu ẩm thực Hàn Quốc ra mắt vào năm 2025. Món
              “lẩu”, “mỳ cay” với 7 cung bậc cay đã trở thành cơn sốt đối với giới trẻ lúc bấy giờ.
              TITOKAQA đã trở thành một trong những lựa chọn hàng đầu của giới trẻ Việt Nam khi muốn
              thưởng thức lẩu nói riêng và ẩm thực Hàn Quốc nói chung.</span>
          </div>

          <div class="deals-hot-box d-none d-lg-block">
            <p class="title" style="color: white">Khám phá ưu đãi hot!</p>
            <a href="#" class="link">Xem ngay <span>→</span></a>
          </div>
        </div>
        <div class="col-12 col-lg-8">
          <div class="images-foods-banner">
            <img src="/public/img/Bannerfoods.webp" alt="foods-banner" class="img-fluid" />
          </div>
        </div>
      </div>
      <!--slide-->

      <div class="mid-banner container-fluid position-relative">
        <img :src="images[currentIndex]" alt="banner" class="img-fluid"
          style="border-radius: 25px; transition: opacity 0.5s ease" />
        <!-- <button @click="changeSlide(-1)" class="trans-btn trans-left d-none d-lg-block">
          <i class="fa-solid fa-arrow-left" style="color: white"></i>
        </button>
        <button @click="changeSlide(1)" class="trans-btn trans-right d-none d-lg-block">
          <i class="fa-solid fa-arrow-right" style="color: white"></i>
        </button> -->
      </div>

      <section class="foods-homepages d-flex mt-5">
        <div class="container">
          <div class="row">
            <!-- Menu desktop -->
            <div class="col-md-3 d-none d-lg-block text-start">
              <span class="title-menu fw-bold">THỰC ĐƠN</span>
              <nav class="navbar px-0 py-2">
                <ul class="navbar-nav flex-column w-100">
                  <li v-for="parent in categories" :key="parent.id" class="nav-item dropdown position-relative">
                    <a @click.prevent="getFoodByCategory(parent.id)" class="nav-link fw-bold text-start" href="#">
                      {{ parent.name }}
                    </a>

                    <ul v-if="parent.children && parent.children.length" class="dropdown-menu custom-dropdown">
                      <li v-for="child in parent.children" :key="child.id">
                        <a @click.prevent="getFoodByCategory(child.id)" href="#" class="dropdown-item plain-text">
                          {{ child.name }}
                        </a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </nav>
            </div>

            <!-- Menu mobile -->
            <div class="col-12 d-lg-none position-relative">
              <div class="menu-header d-flex justify-content-between align-items-center mt-3" @click="toggleDropdown">
                <h2 class="menu-title">Thực đơn</h2>
                <div class="menu-icon d-flex align-items-center">
                  <i class="fas fa-list-alt"></i>
                  <span>Danh mục</span>
                </div>
              </div>
              <div :class="{ collapse: !isDropdownOpen, show: isDropdownOpen }" class="menu-dropdown">
                <ul class="list-group">
                  <li v-for="parent in categories" :key="parent.id" class="list-group-item parent-category d-flex">
                    <a @click.prevent="getFoodByCategory(parent.id)" href="#"
                      class="text-decoration-none text-start text-dark fw-bold">
                      {{ parent.name }}
                    </a>
                    <ul v-if="parent.children && parent.children.length" class="list-group ms-3">
                      <li v-for="child in parent.children" :key="child.id"
                        class="list-group-item child-category d-flex">
                        <a @click.prevent="getFoodByCategory(child.id)" href="#"
                          class="text-decoration-none text-start text-dark fw-bold">
                          🔻{{ child.name }}
                        </a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Content -->
            <div class="col-lg-9 align-items-center">
              <div class="title-food-menu-wrap text-start mb-3">
                <span class="title-food-menu fw-bold">
                  {{ selectedCategoryName || 'Món Ăn' }}
                </span>
              </div>
              <div class="col">
                <!-- Skeleton -->
                <section v-if="isLoading">
                  <div v-for="n in 4" :key="n" class="foods-homepages">
                    <!-- n lẻ -->
                    <div v-if="n % 2 !== 0" class="food-box-left row align-items-center">
                      <div class="col-md-4 food-image">
                        <div class="skeleton-box skeleton-img w-100"></div>
                      </div>
                      <div class="col-md-8 food-content bg-white text-end">
                        <div class="skeleton-box skeleton-title mb-2 w-75 ms-auto" style="height: 24px"></div>
                        <div class="skeleton-box skeleton-price mb-2 w-50 ms-auto" style="height: 20px"></div>
                        <div class="skeleton-box skeleton-desc w-75 ms-auto" style="height: 16px"></div>
                      </div>
                    </div>

                    <!-- n chẵn -->
                    <div v-else class="food-box-right row align-items-center">
                      <div class="col-md-8 food-content bg-white text-start">
                        <div class="skeleton-box skeleton-title mb-2 w-75" style="height: 24px"></div>
                        <div class="skeleton-box skeleton-price mb-2 w-50" style="height: 20px"></div>
                        <div class="skeleton-box skeleton-desc w-75" style="height: 16px"></div>
                      </div>
                      <div class="col-md-4 food-image">
                        <div class="skeleton-box skeleton-img w-100"></div>
                      </div>
                    </div>
                  </div>
                </section>

                <!-- Foods -->
                <section v-else v-for="item in foods" :key="item.id" class="foods-homepages">
                  <!-- item lẻ -->
                  <div v-if="item.id % 2 !== 0" class="food-box-left row align-items-center" @click="openModal(item)">
                    <div class="col-md-4 food-image">
                      <img :src="getImageUrl(item.image)" class="img-fluid" />
                    </div>
                    <div class="col-md-8 food-content bg-white text-end">
                      <h2 class="food-title fw-bold">{{ item.name }}</h2>
                      <p class="food-price fw-bold">{{ formatNumber(item.price) }} VNĐ</p>
                      <p class="food-desc">
                        {{ item.description.slice(0, 60)
                        }}{{ item.description.length > 50 ? '...' : '' }}
                      </p>
                    </div>
                  </div>

                  <!-- item chẵn -->
                  <div v-else class="food-box-right row align-items-center" @click="openModal(item)">
                    <div class="col-md-8 food-content bg-white text-start">
                      <h2 class="food-title fw-bold">{{ item.name }}</h2>
                      <p class="food-price fw-bold">{{ formatNumber(item.price) }} VNĐ</p>
                      <p class="food-desc">
                        <span class="d-none d-sm-inline">
                          {{ item.description.slice(0, 60)
                          }}{{ item.description.length > 50 ? '...' : '' }}
                        </span>
                        <span class="d-inline d-sm-none">
                          {{ item.description.slice(0, 30)
                          }}{{ item.description.length > 50 ? '...' : '' }}
                        </span>
                      </p>
                    </div>
                    <div class="col-md-4 food-image">
                      <img :src="getImageUrl(item.image)" class="img-fluid" />
                    </div>
                  </div>
                </section>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </section>

  <section class="populars-infors-pots">
    <!---->
    <section class="popular-searches container py-4">
      <h2 class="fw-bold mb-3 text-start text-md-start">Combo Nhiều Người Gọi</h2>
      <div class="row text-center justify-content-center">
        <template v-if="isLoading">
          <div v-for="n in 3" :key="n" class="col-4 mb-3">
            <div class="combo-skeleton-img w-100 mb-2"></div>
            <div class="combo-skeleton-text w-75"></div>
          </div>
        </template>

        <template v-else>
          <div class="row g-3">
            <div v-for="combo in topCombos" :key="combo.id" class="col-12 col-md-4 text-center combo-card"
              @click="openModal(combo)">
              <img :src="getImageUrl(combo.image)" class="img-fluid mb-2" alt="" />
              <div class="view-more">
                <a href="#" class="link fw-bold">{{ combo.name }}</a>
              </div>
            </div>
          </div>
        </template>
      </div>
    </section>

    <!---->
    <section class="pots-section container">
      <h2 class="text-center text-md-start mb-3 fw-bold">Thông Báo & Bài Viết<span>📢</span></h2>
      <hr />
      <div v-if="loadingPosts" class="text-center">
        <span>Đang tải bài viết...</span>
      </div>

      <div v-else-if="postError" class="text-danger">
        {{ postError }}
      </div>

      <div v-else class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <div v-for="post in allPosts" :key="post.id" class="col">
          <router-link :to="`/posts_detail/${post.id}`">
            <div class="img-post">
              <img v-if="post.image" :src="getImageUrl(post.image)" :alt="post.title" class="img-fluid rounded"
                loading="lazy" />
            </div>
          </router-link>
        </div>
      </div>
    </section>
  </section>
  <!-- modal food -->
  <div class="modal fade" id="productModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content custom-modal modal-ct">
        <div class="modal-body position-relative">
          <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
            aria-label="Close"></button>
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
                  <div v-else class="mt-5 none-topping">
                    <p class="text-center text-muted">Không có topping cho món này.</p>
                  </div>
                </div>

                <!---->
                <div class="mt-auto">
                  <div class="text-center mb-2">
                    <div class="qty-control px-2 py-1">
                      <button type="button" @click="decreaseQuantity" class="btn-lg" style="background-color: #fff">
                        -
                      </button>
                      <span>{{ quantity }}</span>
                      <button type="button" @click="increaseQuantity" class="btn-lg" style="background-color: #fff">
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
</template>
<script>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import numeral from 'numeral'
import { Modal } from 'bootstrap'
import Swal from 'sweetalert2'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'

export default {
  name: 'HomePage',
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `${STORAGE_URL}/img/food/${image}`
    },
  },
  setup() {
    const foods = ref([])
    const combos = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const toppings = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})

    const isLoading = ref(true)
    const isDropdownOpen = ref(false)
    const selectedCategoryName = ref('Món Ăn')
    let postIntervalId = null

    const allPosts = ref([])
    const loadingPosts = ref(false)
    const postError = ref("")
    const quantity = ref(1)

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

        selectedCategoryName.value = childName
          ? `${parentName} > ${childName}`
          : parentName || 'Món Ăn'

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
          const combosWithType = comboRes.data.map((item) => ({ ...item, type: 'combo' }))
          allFoods = [...allFoods, ...combosWithType]
        }

        foods.value = allFoods
      } catch (error) {
        console.error(error)
      }
    }
    const topCombos = ref([])
    const getTopCombos = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/combos`)
        topCombos.value = res.data.top
      } catch (error) {
        console.error(error)
      }
    }
    const getAllCombos = async () => {
      try {
        const res = await axios.get(`${API_URL}/home/combos`)
        combos.value = res.data.all
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
        // combo
        const res = await axios.get(`${API_URL}/home/combo/${item.id}`)
        foodDetail.value = { ...res.data, type: 'combo' }

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

    const addToCart = () => {
      const user = JSON.parse(localStorage.getItem('user'))
      const userId = user?.id || 'guest'
      const cartKey = `cart_${userId}`

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

      const cartItem = {
        id: foodDetail.value.id,
        name: foodDetail.value.name,
        image: foodDetail.value.image,
        price: foodDetail.value.price,
        toppings: allSelectedToppings,
        quantity: quantity.value,
        type: foodDetail.value.type,
        category_id: foodDetail.value.category_id,
      }

      let cart = JSON.parse(localStorage.getItem(cartKey)) || []
      const existingItemIndex = cart.findIndex(
        (item) =>
          item.id === cartItem.id &&
          JSON.stringify(item.toppings.map((t) => t.id).sort()) ===
          JSON.stringify(cartItem.toppings.map((t) => t.id).sort()),
      )

      if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantity += 1
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

    const increaseQuantity = () => {
      quantity.value += 1
    }

    const decreaseQuantity = () => {
      if (quantity.value > 1) {
        quantity.value -= 1
      }
    }

    const getImageUrl = (image) => `${STORAGE_URL}/img/post/${image}`

    const shuffleArray = (array) => {
      return array
        .map(value => ({ value, sort: Math.random() }))
        .sort((a, b) => a.sort - b.sort)
        .map(({ value }) => value)
    }

    const fetchPosts = async () => {
      loadingPosts.value = true
      postError.value = ""
      try {
        const res = await axios.get(`${API_URL}/get_all_post`)
        let posts = []
        if (Array.isArray(res.data.result)) {
          posts = res.data.result
        } else {
          posts = res.data.result.data || []
        }

        // Lọc bài viết không ẩn và lấy 4 bài ngẫu nhiên
        const visiblePosts = posts.filter(post => post.is_hidden == 0)
        allPosts.value = shuffleArray(visiblePosts).slice(0, 4)
      } catch (err) {
        postError.value = "Không thể tải dữ liệu"
        console.error(err)
      } finally {
        loadingPosts.value = false
      }
    }


    onMounted(async () => {
      try {
        const res = await axios.get(`${API_URL}/home/foods`)
        const res1 = await axios.get(`${API_URL}/home/combos`)
        foods.value = res.data
        combos.value = res1.data
        await new Promise((resolve) => setTimeout(resolve, 5000))
      } catch (e) {
        console.error(e)
      } finally {
        isLoading.value = false
      }
    })

    onMounted(async () => {
      await getCategory()
      await getFood()
      await getAllCombos()
      await getTopCombos()
      await fetchPosts()
      postIntervalId = setInterval(fetchPosts, 10 * 60 * 1000) // mỗi 10 phút
      intervalId = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % images.length
      }, 3000)
    })

    onBeforeUnmount(() => {
      clearInterval(intervalId)
      clearInterval(postIntervalId)
    })

    return {
      foods,
      combos,
      topCombos,
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
      quantity,
      increaseQuantity,
      decreaseQuantity,
      allPosts,       // <-- thêm
      loadingPosts,   // <-- thêm
      postError,      // <-- thêm
      getImageUrl,
    }
  },
}
</script>

<style>
.animation-duration {
  animation-duration: 2500ms !important;
}

/**food */
.skeleton-box {
  background: linear-gradient(90deg, #f0f0f0 25%, #e6e6e6 37%, #f0f0f0 63%);
  background-size: 400% 100%;
  animation: shimmer 1.2s infinite linear;
  border-radius: 4px;
}

@keyframes shimmer {
  0% {
    background-position: 100% 0;
  }

  100% {
    background-position: -100% 0;
  }
}

.skeleton-box.skeleton-img {
  height: 180px;
  width: 100%;
  background-color: #e0e0e0;
  border-radius: 8px;
}

@media (max-width: 576px) {
  .skeleton-box.skeleton-img {
    height: 120px;
  }
}

@media (max-width: 400px) {
  .skeleton-box.skeleton-img {
    height: 100px;
  }
}

/**combo */
.skeleton-box {
  background-color: #e0e0e0;
  border-radius: 8px;
  animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-img {
  height: 140px;
}

.skeleton-text {
  height: 18px;
}

@media (max-width: 576px) {
  .skeleton-img {
    height: 100px;
  }
}

@keyframes pulse {
  0% {
    opacity: 1;
  }

  50% {
    opacity: 0.4;
  }

  100% {
    opacity: 1;
  }
}

/**combo */
.combo-skeleton-img {
  background: #eee;
  border-radius: 8px;
  height: 180px;
}

.combo-skeleton-text {
  background: #ddd;
  border-radius: 6px;
  height: 16px;
}

/* Responsive cho màn nhỏ */
@media (max-width: 576px) {
  .combo-skeleton-img {
    height: 120px;
  }
}
</style>
