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
            <p class="title">Khám phá ưu đãi hot!</p>
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
      <!--hôm nay-->
      <div class="mid-banner container-fluid">
        <img
          src="../../../../public/img/Banner (3).webp"
          alt=""
          class="img-fluid"
          style="border-radius: 25px"
        />
        <button class="trans-left d-none d-lg-block">
          <i class="fa-solid fa-arrow-left" style="color: #ffffff"></i>
        </button>
        <button class="trans-right d-none d-lg-block">
          <i class="fa-solid fa-arrow-right" style="color: #ffffff"></i>
        </button>
      </div>
      <section class="foods-homepages d-flex mt-5">
        <div class="container">
          <div class="row">
            <!--Menu -->
            <div class="col-md-3 d-none d-lg-block">
              <span class="title-menu fw-bold">THỰC ĐƠN</span>
              <ul v-for="(category, index) in categories" :key="index" class="menu-list m-5">
                <li>
                  <input type="radio" id="{{ category.id }}" name="menu" checked />
                  <label for="{{ category.name }}"><i class="fa-solid fa-o"></i> {{ category.name }} </label>
                </li>
              </ul>
            </div>
            <!-- small -->
            <div class="col-12 d-lg-none position-relative">
              <div
                class="menu-header d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                data-bs-target="#menuDropdown"
              >
                <h2 class="menu-title">Thực đơn</h2>
                <div class="menu-icon d-flex align-items-center">
                  <i class="fas fa-list-alt"></i>
                  <span>Danh mục</span>
                </div>
              </div>
              <div id="menuDropdown" class="collapse menu-dropdown">
                <ul class="list-group mb-3">
                  <li class="list-group-item">Mì Cay</li>
                  <li class="list-group-item">Lẩu Hàn Quốc</li>
                  <li class="list-group-item">Cơm Trộn</li>
                  <li class="list-group-item">Đồ Ăn Kèm</li>
                </ul>
              </div>
            </div>

            <div class="col-lg-9 align-items-center text-center">
              <div class="title-food-menu text-start m-3">
                <span class="title-food-menu fw-bold">Mỳ Cay</span>
              </div>
              <section v-for="(food, index) in foods" :key="index" class="foods-homepages" >
                <div
                 v-if="index % 2 !== 0"
                  class="food-box-left row align-items-center"
                   @click="openModal(food.id)"
                >
                  <div class="col-md-4 food-image">
                    <img
                   :src="getImageUrl(food.image)"
                      alt="Mì Kim Chi Thập Cẩm"
                      class="img-fluid"
                    />
                  </div>
                  <div class="col-md-8 food-content bg-white text-end">
                    <h2 class="food-title fw-bold">{{ food.name }}</h2>
                    <p class="food-price fw-bold">{{formatNumber(food.price) }} VNĐ</p>
                    <p class="food-desc">{{ food.description.slice(0, 60) }}{{ food.description.length > 50 ? '...' : '' }}</p>
                  </div>
                </div>
                <div v-else
                  class="food-box-right row align-items-center"
                  @click="openModal(food.id)"
                >
                  <div class="col-md-8 food-content bg-white text-start">
                    <h2 class="food-title fw-bold">{{ food.name }}</h2>
                    <p class="food-price fw-bold">{{formatNumber(food.price) }} VNĐ</p>
                    <p class="food-desc">
                    <span class="d-none d-sm-inline">{{ food.description.slice(0, 60) }}{{ food.description.length > 50 ? '...' : '' }}</span>
                    <span class="d-inline d-sm-none">{{ food.description.slice(0, 30) }}{{ food.description.length > 50 ? '...' : '' }}</span>
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
        <span class="badge rounded-pill text-dark bg-dtext-dark px-3 py-2">
          <span>🍛</span> Lẩu
        </span>
        <span class="badge rounded-pill text-dark bg-dtext-dark px-3 py-2">
          <span>🍜🔥</span> Mỳ cay
        </span>
        <span class="badge rounded-pill text-dark bg-dtext-dark px-3 py-2">
          <span>🥩</span> Bò Waygu
        </span>
      </div>
    </section>

    <!---->
    <section class="pots-section container">
      <h2 class="text-center text-md-start mb-3 fw-bold">Thông Báo & Bài Viết<span>📢</span></h2>
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
            <div class="col-md-5 d-flex justify-content-center align-items-center">
              <img
              :src="getImageUrl(foodDetail.image)"
              :alt="foodDetail.name" width="100%" />
            </div>
            <div class="col-md-7 d-flex flex-column justify-content-center">
              <h4 class="fw-bold">🔥{{ foodDetail.name }}🔥</h4>
              <p class="fw-bold text-dark">
                <i class="fa-solid fa-star" style="color: #ffd43b"></i> NULL
              </p>
              <p class="text-danger fw-bold fs-4">{{ formatNumber(foodDetail.price) }} VNĐ</p>

              <p class="text-secondary">
                {{ foodDetail.description }}
              </p>
              <div class="mb-3">
                <label for="spicyLevel" class="form-label fw-bold">🌶 Mức độ cay:</label>
                <select class="form-select" id="spicyLevel">
                  <option type="checkbox" value="1">Cấp độ 1 - Nhẹ</option>
                  <option value="2">Cấp độ 2 - Trung bình</option>
                  <option value="3">Cấp độ 3 - Cay</option>
                  <option value="4">Cấp độ 4 - Siêu Cay</option>
                </select>
                <div class="topping-container mt-3">
                  <h4>Chọn topping</h4>
                  <div class="topping-list">
                    <label>
                      <input type="checkbox" name="topping[]" value="cheese" /> Phô mai
                    </label>
                    <label> <input type="checkbox" name="topping[]" value="egg" /> Trứng </label>
                    <label>
                      <input type="checkbox" name="topping[]" value="sausage" /> Xúc xích
                    </label>
                  </div>
                </div>
              </div>
              <button class="btn btn-danger w-100 fw-bold">🛒 Thêm vào giỏ hàng</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import { ref, onMounted } from "vue";
import numeral from "numeral";
import { Modal } from 'bootstrap';

export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format("0,0.00");;
    },
    getImageUrl(image) {
      return `/img/food/${image}`;
    },
  },
  name: "FoodList",
  setup() {
    const foods = ref([]);
    const categories = ref([]);
    const foodDetail = ref({});

    const getFood = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/foods`);
        foods.value = res.data;
      } catch (error) {
        console.error(error);
      }
    };
    const openModal = async (foodId) => {
   try {
     const res = await axios.get(`http://127.0.0.1:8000/api/home/food/${foodId}`);
     foodDetail.value = res.data;
     console.log(foodDetail);

     const modalElement = document.getElementById('productModal');
     if (modalElement) {
       const modal = new Modal(modalElement);
       modal.show();
     }

   } catch (error) {
     console.error(error);
   }
 };
 const getCategory =  async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/home/categories`);
        categories.value = res.data;
      } catch (error) {
        console.error(error);
      }
    };
    onMounted(() => {
      getFood();
      getCategory();
    });

    return {
      foods,
      categories,
      foodDetail,
      openModal,
    };
  },
};
</script>

