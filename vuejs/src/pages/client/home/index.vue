<template>
  <!--main-home-->
  <section class="main d-flex justify-content-center align-items-center">
    <div class="container text-center">
      <div class="row justify-content-center align-items-stretch">
        <div class="col-4 d-none d-lg-block pt-5 text-start">
          <div class="title-foods fw-medium fs-5 mt-5">
            <span>Lẩu và Mỳ cay 7 cấp độ</span>
          </div>
          <div class="title-shops d-none d-md-block fw-bold">
            <span>TITOKAQA</span>
          </div>
          <div class="title-infors fw-normal pe-5">
            <span
              >TITOKAQA là chuỗi nhà hàng thương hiệu ẩm thực Hàn Quốc ra mắt vào năm 2025. Món
              “lẩu”, “mỳ cay” với 7 cung bậc cay đã trở thành cơn sốt đối với giới trẻ lúc bấy giờ.
              TITOKAQA đã trở thành một trong những lựa chọn hàng đầu của giới trẻ Việt Nam khi muốn
              thưởng thức lẩu nói riêng và ẩm thực Hàn Quốc nói chung.</span
            >
          </div>

          <div class="deals-hot-box d-none d-lg-block">
            <p class="title" style="color: white">Khám phá ưu đãi hot!</p>
            <a href="#" class="link">Xem ngay <span>→</span></a>
          </div>
        </div>
        <div class="col-12 col-lg-8">
          <div class="images-foods-banner">
            <img
              src="../../../../public/img/Bannerfoods.webp"
              alt="foods-banner"
              class="img-fluid"
            />
          </div>
        </div>
      </div>
      <!--slide-->

  <div class="mid-banner container-fluid position-relative">
    <img
      :src="images[currentIndex]"
      alt="banner"
      class="img-fluid"
      style="border-radius: 25px; transition: opacity 0.5s ease"
    />
    <button @click="changeSlide(-1)" class="trans-btn trans-left d-none d-lg-block">
      <i class="fa-solid fa-arrow-left" style="color: white;"></i>
    </button>
    <button @click="changeSlide(1)" class="trans-btn trans-right d-none d-lg-block">
      <i class="fa-solid fa-arrow-right" style="color: white;"></i>
    </button>
  </div>



      <section class="foods-homepages d-flex mt-5">
        <div class="container">
          <div class="row">
            <!--Menu -->
            <div class="col-md-3 d-none d-lg-block text-start">
              <span class="title-menu fw-bold">THỰC ĐƠN</span>
              <nav class="navbar px-0 py-2">
                <ul class="navbar-nav flex-column w-100">
                  <li
                    v-for="parent in categories"
                    :key="parent.id"
                    class="nav-item dropdown position-relative"
                  >
                    <a
                      @click.prevent="getFoodByCategory(parent.id)"
                      class="nav-link fw-semibold text-start"
                      href="#"
                    >
                      {{ parent.name }}
                    </a>

                    <ul
                      v-if="parent.children && parent.children.length"
                      class="dropdown-menu custom-dropdown"
                    >
                      <li v-for="child in parent.children" :key="child.id">
                        <a
                          @click.prevent="getFoodByCategory(child.id)"
                          href="#"
                          class="dropdown-item plain-text"
                        >
                          {{ child.name }}
                        </a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </nav>
            </div>

            <!-- small -->
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

            <div class="col-lg-9 align-items-center text-center">
              <div class="title-food-menu text-center d-flex">
                <span class="title-food-menu fw-bold">{{ selectedCategoryName || 'Món Ăn' }}</span>
              </div>
              <section v-for="(food, index) in foods" :key="index" class="foods-homepages">
                <div
                  v-if="index % 2 !== 0"
                  class="food-box-left row align-items-center"
                  @click="openModal(food.id)"
                >
                  <div class="col-md-4 food-image">
                    <img
                      :src="getImageUrl(food.image)"
                      class="img-fluid"
                    />
                  </div>
                  <div class="col-md-8 food-content bg-white text-end">
                    <h2 class="food-title fw-bold">{{ food.name }}</h2>
                    <p class="food-price fw-bold">{{ formatNumber(food.price) }} VNĐ</p>
                    <p class="food-desc">
                      {{ food.description.slice(0, 60)
                      }}{{ food.description.length > 50 ? '...' : '' }}
                    </p>
                  </div>
                </div>
                <div
                  v-else
                  class="food-box-right row align-items-center"
                  @click="openModal(food.id)"
                >
                  <div class="col-md-8 food-content bg-white text-start">
                    <h2 class="food-title fw-bold">{{ food.name }}</h2>
                    <p class="food-price fw-bold">{{ formatNumber(food.price) }} VNĐ</p>
                    <p class="food-desc">
                      <span class="d-none d-sm-inline"
                        >{{ food.description.slice(0, 60)
                        }}{{ food.description.length > 50 ? '...' : '' }}</span
                      >
                      <span class="d-inline d-sm-none"
                        >{{ food.description.slice(0, 30)
                        }}{{ food.description.length > 50 ? '...' : '' }}</span
                      >
                    </p>
                  </div>
                  <div class="col-md-4 food-image">
                    <img
                      :src="getImageUrl(food.image)"
                      alt="Mì Kim Chi Thập Cẩm"
                      class="img-fluid"
                    />
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </section>
    </div>
  </section>
  <section class="populars-infors-pots">
    <!---->
    <section class="popular-searches container py-4">
      <h2 class="fw-bold mb-3 text-start text-md-start">Nhiều Người Gọi</h2>
      <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3">
        <div class="row d-flex">
          <div class="col-4">
            <img src="/img/food/combo/cb1.webp" alt="" width="100%">
            <div class="view-more">
            <a href="#" class="link fw-bold">Combo 1 người →</a>
          </div>
          </div>
          <div class="col-4">
            <img src="/img/food/combo/cb2.webp" alt="" width="100%">
            <div class="view-more">
            <a href="#" class="link fw-bold">Combo 2 người →</a>
          </div>
          </div>
          <div class="col-4">
            <img src="/img/food/combo/cb3.webp" alt="" width="100%">
            <div class="view-more">
            <a href="#" class="link fw-bold">Combo Panchan 19K →</a>
          </div>
          </div>
        </div>
      </div>
    </section>

    <!---->
    <section class="pots-section container">
      <h2 class="text-center text-md-start mb-3 fw-bold">Thông Báo & Bài Viết<span>📢</span></h2>
      <hr />
      <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        <div class="col img-post">
          <img src="../../../../public/img/bv1.webp" alt="post1" class="img-fluid rounded" />
        </div>
        <div class="col img-post">
          <img src="../../../../public/img/bv2.png" alt="post2" class="img-fluid rounded" />
        </div>
        <div class="col img-post">
          <img src="../../../../public/img/bv3.png" alt="post3" class="img-fluid rounded" />
        </div>
        <div class="col img-post">
          <img src="../../../../public/img/bv1.webp" alt="post4" class="img-fluid rounded" />
        </div>
      </div>
    </section>
  </section>
  <!-- modal food -->
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
            <div class="blink">
              <img src="/img/item/item text.png" alt="">
            </div>
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
                <button class="btn btn-danger w-100 fw-bold" @click.prevent="addToCart">🛒 Thêm vào giỏ hàng</button>
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
import { ref, onMounted, onBeforeUnmount  } from 'vue'
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
        console.log(spicyLevel.value);

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
    const currentIndex = ref(0)
const images = [
  '/img/banner/Banner (1).webp',
  '/img/banner/Banner (2).png',
  '/img/banner/Banner.png',
]
let intervalId = null
const changeSlide = (direction) => {
  const total = images.length
  currentIndex.value = (currentIndex.value + direction + total) % total
}
    const addToCart = () => {
      const selectedSpicyId = parseInt(document.getElementById('spicyLevel')?.value)

      const selectedSpicy = spicyLevel.value.find((item) => item.id === selectedSpicyId)
      const selectedSpicyName = selectedSpicy ? selectedSpicy.name : 'Không rõ'
      const selectedToppingId = Array.from(
        document.querySelectorAll('input[name="topping[]"]:checked')).map((el)=>parseInt(el.value))

      const selectedToppings= toppingList.value
      .filter((topping)=>selectedToppingId.includes(topping.id))
      .map((topping)=>({
        name: topping.name,
        price: topping.price
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

      //lấy giỏ hàng từ localStorage
      let cart=JSON.parse(localStorage.getItem('cart')) || []

      //Tìm xem item có trong giỏ hàng chưa
      const existingItem = cart.findIndex(
        (item) =>
        item.id === cartItem.id &&
        item.spicyLevel === cartItem.spicyLevel &&
        JSON.stringify(item.toppings.sort()) ===  JSON.stringify(cartItem.toppings.sort())
      )

      if(existingItem !== -1){
        cart[existingItem].quantity += 1
      } else {
        cart.push(cartItem)
      }

      localStorage.setItem('cart', JSON.stringify(cart))
      alert('Đã thêm vào giỏ hàng!')
    }
    onMounted(() => {
      getFood()
      getCategory()

      intervalId = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % images.length
  }, 3000)
    })
    onBeforeUnmount(() => {
  clearInterval(intervalId)
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
      currentIndex,
      images,
      changeSlide,
      addToCart,

    }
  },
}
</script>


