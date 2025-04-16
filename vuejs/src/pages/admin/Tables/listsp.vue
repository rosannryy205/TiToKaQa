<template>
  <div class="container my-4">
    <div class="row">
      <!-- Bên trái: Danh sách sản phẩm -->
      <div class="col-md-6" style="max-height: 500px; overflow-y: auto;">
        <div v-for="product in foodOrderAdmin" :key="product.id" class="border rounded shadow-sm p-3 mb-3">
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
              <div class="d-flex justify-content-between align-items-center border rounded p-2"
                v-for="topping in product.spicyLevelNotNull" :key="topping.pivot.id">
                <label class="d-flex align-items-center mb-0">
                  <input type="checkbox" :value="topping.pivot.id" name="topping[]" class="me-2" />
                  {{ topping.name }}
                </label>
                <span class="text-muted small">{{ formatNumber(topping.price) }} VND</span>
              </div>
            </div>
            <div class="col-sm-4 chon">
              <button class="btn btn-primary">Chọn</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bên phải: Giỏ hàng -->
      <div class="col-md-6" style="max-height: 500px; overflow-y: auto;">
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
              <tr>
                <td>
                  HHH
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary">-</button>
                    <span class="mx-2">1</span>
                    <button class="btn btn-sm btn-outline-secondary">+</button>
                  </div>
                </td>
                <td>900000 đ</td>
                <td>
                  <button class="btn btn-sm btn-danger">X</button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="text-end mt-3">
            <strong class="text-danger fs-5">Tổng tiền: 9999999 đ</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// import { ref, computed } from 'vue';
import { FoodList } from '@/stores/food';

export default {
  setup() {
    const {
      foodOrderAdmin,
      formatNumber,
      spicyLevelNull,
      spicyLevelNotNull
    } = FoodList.setup()

    // console.log(spicyLevelNotNull);




    return {
      foodOrderAdmin,
      formatNumber,
      spicyLevelNull,
      spicyLevelNotNull
    }
  }
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
