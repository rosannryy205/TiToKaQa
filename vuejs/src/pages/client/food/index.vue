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
      <a
        @click.prevent="getFoodByCategory(parent.id)"
        class="menu-link"
        href="#"
      >
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
                class="menu-header d-flex justify-content-between align-items-center"
                @click="toggleDropdown"
              >
                <h2 class="menu-title">Thực đơn</h2>
                <div class="menu-icon d-flex align-items-center">
                  <i class="fas fa-list-alt"></i>
                  <span>Danh mục</span>
                </div>
              </div>

              <div
                :class="{ collapse: !isDropdownOpen, show: isDropdownOpen }"
                class="menu-dropdown"
              >
                <ul class="list-group">
                  <li
                    v-for="parent in categories"
                    :key="parent.id"
                    class="list-group-item parent-category d-flex"
                  >
                    <a
                      @click.prevent="getFoodByCategory(parent.id)"
                      href="#"
                      class="text-decoration-none text-start"
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
                          class="text-decoration-none text-start"
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
    <img src="/img/item/imgmenu.webp" alt="dish-images" />
  </div>

  <!---->
  <div class="col-12 col-md-6 title-dish">
    <p class="text-end">{{ selectedCategoryName || 'Món Ăn' }}</p>
  </div>
</div>
<div class="product-list-wrapper container-fluid">
            <div class="row">
            <div
            v-for="(food, index) in foods" :key="index"
             @click="openModal(food.id)"
             class="col-md-3">
              <div
               class="product-card " data-bs-toggle="modal" data-bs-target="#productModal">
                <img
                  :src="getImageUrl(food.image)"
                  alt=""
                  class="product-img mx-auto d-block"
                  width="180px"
                />
                <h3 class="product-dish-title text-start">{{ food.name }}</h3>
                <p class="product-dish-desc text-start">
                  {{ food.description }}
                </p>
                <p class="product-dish-price fw-bold text-center">{{ food.price }}</p>
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
    <img src="../../../../public/img/Banner (3).webp" alt="" class="img-fluid" style="border-radius: 25px" />
    <button class="trans-left d-none d-lg-block">
      <i class="fa-solid fa-arrow-left" style="color: #ffffff"></i>
    </button>
    <button class="trans-right d-none d-lg-block">
      <i class="fa-solid fa-arrow-right" style="color: #ffffff"></i>
    </button>
  </div>
</section>
    <section class="pots-section container">
      <h2 class="text-center text-md-start mb-3 fw-bold">Bài Viết & Thông Tin<span>📢</span></h2>
      <hr />
      <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <div class="col">
          <img src="../../../../public/img/bv1.webp" alt="post1" class="img-fluid rounded" />
        </div>
        <div class="col">
          <img src="../../../../public/img/bv2.png" alt="post2" class="img-fluid rounded" />
        </div>
        <div class="col">
          <img src="../../../../public/img/bv3.png" alt="post3" class="img-fluid rounded" />
        </div>
        <div class="col">
          <img src="../../../../public/img/bv1.webp" alt="post4" class="img-fluid rounded" />
        </div>
      </div>
    </section>
    <!-- Modal -->
    <div
    class="modal fade"
    id="productModal"
    tabindex="-1"
    aria-labelledby="productModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-5 d-flex justify-content-center align-items-center">
              <img :src="getImageUrl(foodDetail.image)" :alt="foodDetail.name" width="100%" />
            </div>
            <div class="col-md-7 d-flex flex-column justify-content-center">
              <h4 class="fw-bold">{{ foodDetail.name }}</h4>
              <p class="fw-bold text-dark">
                <i class="fa-solid fa-star" style="color: #ffd43b"></i> NULL
              </p>
              <p class="text-danger fw-bold fs-4">{{ formatNumber(foodDetail.price) }} VNĐ</p>

              <p class="text-secondary">
                {{ foodDetail.description }}
              </p>
              <form>
                <div class="mb-3">
                  <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                  <select class="form-select" id="spicyLevel">
                    <option v-for="item in spicyLevel" :key="item.id" :value="item.id">
                      {{ item.name }}
                    </option>
                  </select>
                  <div class="topping-container mt-3">
                    <h4>Chọn topping</h4>
                    <div
                      v-for="topping in toppingList"
                      :key="topping.id"
                      class="d-flex justify-content-between align-items-center mb-2"
                    >
                      <label class="d-flex align-items-center">
                        <input type="checkbox" :value="topping.id" name="topping[]" class="m-2" />
                        {{ topping.name }}
                      </label>
                      <span class="font-weight-bold">{{ formatNumber(topping.price) }} VND</span>
                    </div>
                  </div>
                </div>
                <button class="btn btn-danger w-100 fw-bold">🛒 Thêm vào giỏ hàng</button>
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
import { ref, onMounted } from 'vue'
import numeral from 'numeral'
import { Modal } from 'bootstrap'

export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0.00')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },
  name: 'HomePage',
  setup() {
    const foods = ref([])
    const toppings = ref([])
    const categories = ref([])
    const foodDetail = ref([])
    const spicyLevel = ref([])
    const toppingList = ref({})
    const isLoading = ref(false)
    const isDropdownOpen = ref(false)

    const getFood = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/foods`)
        foods.value = res.data
      } catch (error) {
        console.error(error)
      }
    }
    const openModal = async (foodId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${foodId}`)
        foodDetail.value = res.data
        const res1 = await axios.get(`http://127.0.0.1:8000/api/home/topping/${foodId}`)
        toppings.value = res1.data
        spicyLevel.value = toppings.value.filter((item) => item.category_id == 1)
        toppingList.value = toppings.value.filter((item) => item.category_id == 2)
        toppingList.value.forEach((item) => {
          item.price = item.price || 0
        })

        const modalElement = document.getElementById('productModal')
        if (modalElement) {
          const modal = new Modal(modalElement)
          modal.show()
        }
      } catch (error) {
        console.error(error)
      }
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
    const selectedCategoryName = ref('Món Ăn')

    const getFoodByCategory = async (categoryId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/category/${categoryId}/food`)
        foods.value = res.data

        // find cate parent and child
        let parentName = ''
        let childName = ''

        for (const parent of categories.value) {
          if (parent.id === categoryId) {
            // if chose paretn cate
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
        //
        if (childName) {
          selectedCategoryName.value = `${parentName} > ${childName}`
        } else {
          selectedCategoryName.value = parentName || 'Món Ăn'
        }

        // paren hav child => load child
        const selectedCategory = categories.value.find((c) => c.id === categoryId)
        if (selectedCategory?.children?.length) {
          const childRequests = selectedCategory.children.map((child) =>
            axios.get(`http://127.0.0.1:8000/api/home/category/${child.id}/food`),
          )
          const childResults = await Promise.all(childRequests)
          childResults.forEach((childRes) => {
            foods.value = [...foods.value, ...childRes.data]
          })
        }
      } catch (error) {
        console.error(error)
      }
    }

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }

    onMounted(() => {
      getFood()
      getCategory()
    })

    return {
      foods,
      toppings,
      categories,
      getFoodByCategory,
      selectedCategoryName,
      foodDetail,
      openModal,
      isLoading,
      isDropdownOpen,
      toggleDropdown,
      spicyLevel,
      toppingList,
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
