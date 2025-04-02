<template>
  <body>
    <div class="container custom-container">
      <div class="booking-form row w-75" style="border-radius: 0px;">
        <div class="col-md-6 booking-image">
          <img class="img-reservation" src="/img/reservation/Rectangle 48.png" alt="Khuyến mãi Tết">
        </div>
        <div class="col-md-6  form-section mt-2">
          <input type="text" class="form-control  mb-2" placeholder="Tên của bạn">
          <input type="text" class="form-control  mb-2" placeholder="Số điện thoại">
          <input type="number" class="form-control  mb-2" placeholder="Số lượng người">
          <div class="row g-2">
            <div class="col">
              <input type="date" class="form-control " placeholder="Chọn ngày">
            </div>
            <div class="col mb-2">
              <input type="time" class="form-control " placeholder="Giờ">
            </div>
          </div>

          <input type="text" class="form-control  mb-2" placeholder="Khu vực">
          <input type="text" class="form-control  mb-2" placeholder="Ghi chú">
          <button class="btn btn-custom mb-2" data-bs-toggle="modal" data-bs-target="#orderModal">
            Đặt món <span>✚</span>
          </button>
          <button class="btn btn-danger w-100">Xác nhận</button>
        </div>
      </div>
    </div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content custom-modal">
          <div class="modal-header">
            <h5 class="modal-title" id="orderModalLabel">Đặt món</h5>
          </div>
          <div class="modal-body">
            <!-- Bộ lọc -->
            <div class="row mb-3">
              <div class="col-md-6">
                <select class="form-select">
                  <option selected>Mì cay</option>
                  <option>Mì trộn</option>
                  <option>Lẩu</option>
                </select>
              </div>
              <div class="col-md-6">
                <select class="form-select">
                  <option selected>Mặc định</option>
                </select>
              </div>
            </div>

            <!-- Danh sách món ăn -->
            <div class="list-group">
              <div class="container text-center">
                <div class="row row-select">
                  <div class="col">
                    <img src="/img/reservation/Rectangle32.png" alt="Món ăn" class="img-fluid me-3"
                      style="width: 130px;">
                  </div>
                  <div class="col-6 text-start">
                    <h5 class="mb-1">Mì Kim Chi Đặc
                      Biệt</h5>
                    <span class="badge bg-danger m-1">Bò
                      thường</span>
                    <span class="badge bg-dark">Bò Mỹ</span>
                    <p class="mb-1 mt-3">Mì Chinnnoo, bò Mỹ, tôm,
                      mực, chả cá Hàn Quốc, cá viên, kim
                      chi, cải thảo, nấm, bắp cải tím.</p>
                  </div>
                  <div class="col text-center d-flex flex-column align-items-center">
                    <strong class="price">69,000 VNĐ</strong>

                    <div class="d-flex align-items-center mt-2">
                      <button class="btn btn-outline-secondary btn-sm" style="width: 37px;height:37px"
                        @click="decreaseValue(0)">
                        <b>-</b>
                      </button>
                      <input type="text" class="form-control text-center mx-2" v-model="quantities[0]"
                        style="width: 100px;">
                      <button class="btn btn-outline-secondary btn-sm" style="width: 37px;height:37px"
                        @click="increaseValue(0)">
                        <b>+</b>
                      </button>
                    </div>


                    <button class="btn btn-danger mt-2 w-100">Đặt món</button>
                  </div>

                </div>

                <div class="row row-select">
                  <div class="col">
                    <img src="/img/reservation/Rectangle32.png" alt="Món ăn" class="img-fluid me-3"
                      style="width: 130px;">
                  </div>
                  <div class="col-6 text-start">
                    <h5 class="mb-1">Mì Kim Chi Đặc
                      Biệt</h5>
                    <span class="badge bg-danger m-1">Bò
                      thường</span>
                    <span class="badge bg-dark">Bò Mỹ</span>
                    <p class="mb-1 mt-3">Mì Chinnnoo, bò Mỹ, tôm,
                      mực, chả cá Hàn Quốc, cá viên, kim
                      chi, cải thảo, nấm, bắp cải tím.</p>
                  </div>
                  <div class="col text-center d-flex flex-column align-items-center">
                    <strong class="price">69,000 VNĐ</strong>

                    <div class="d-flex align-items-center mt-2">
                      <button class="btn btn-outline-secondary btn-sm" style="width: 37px;height:37px"
                        @click="decreaseValue(0)">
                        <b>-</b>
                      </button>
                      <input type="text" class="form-control text-center mx-2" v-model="quantities[0]"
                        style="width: 100px;">
                      <button class="btn btn-outline-secondary btn-sm" style="width: 37px;height:37px"
                        @click="increaseValue(0)">
                        <b>+</b>
                      </button>
                    </div>


                    <button class="btn btn-danger mt-2 w-100">Đặt món</button>
                  </div>

                </div>

                <div class="row row-select">
                  <div class="col">
                    <img src="/img/reservation/Rectangle32.png" alt="Món ăn" class="img-fluid me-3"
                      style="width: 130px;">
                  </div>
                  <div class="col-6 text-start">
                    <h5 class="mb-1">Mì Kim Chi Đặc
                      Biệt</h5>
                    <span class="badge bg-danger m-1">Bò
                      thường</span>
                    <span class="badge bg-dark">Bò Mỹ</span>
                    <p class="mb-1 mt-3">Mì Chinnnoo, bò Mỹ, tôm,
                      mực, chả cá Hàn Quốc, cá viên, kim
                      chi, cải thảo, nấm, bắp cải tím.</p>
                  </div>
                  <div class="col text-center d-flex flex-column align-items-center">
                    <strong class="price">69,000 VNĐ</strong>

                    <div class="d-flex align-items-center mt-2">
                      <button class="btn btn-outline-secondary btn-sm" style="width: 37px;height:37px"
                        @click="decreaseValue(0)">
                        <b>-</b>
                      </button>
                      <input type="text" class="form-control text-center mx-2" v-model="quantities[0]"
                        style="width: 100px;">
                      <button class="btn btn-outline-secondary btn-sm" style="width: 37px;height:37px"
                        @click="increaseValue(0)">
                        <b>+</b>
                      </button>
                    </div>


                    <button class="btn btn-danger mt-2 w-100">Đặt món</button>
                  </div>

                </div>

                <!-- Thêm các món khác tương tự -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Script để tăng giảm số lượng -->

  </body>
</template>

<script>
import { ref } from "vue";

export default {
  setup() {
    // Tạo danh sách số lượng riêng cho từng món ăn
    const quantities = ref([1, 1, 1]);

    const increaseValue = (index) => {
      quantities.value[index]++;
    };

    const decreaseValue = (index) => {
      if (quantities.value[index] > 1) quantities.value[index]--;
    };

    return {
      quantities,
      increaseValue,
      decreaseValue,
    };
  },
};
</script>
