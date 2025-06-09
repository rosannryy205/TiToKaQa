<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm Combo</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>
  <form class="row mt-2">
    <div class="col-md-8">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
              <label for="name" class="form-label"
                >Tên Combo <span class="text-danger">*</span></label
              >
              <input type="email" class="form-control rounded-0" id="name" required />
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label"
                >Trạng thái <span class="text-danger">*</span></label
              >
              <div class="input-group">
                <select class="form-select rounded-0" id="category" required>
                  <option selected>Chọn trạng thái cho món ăn</option>
                  <option value="1">Ẩn</option>
                  <option value="2">Hiện</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control rounded-0" id="description" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <div class="table-responsive d-none d-lg-block">
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>Món ăn</th>
                    <th>Giá bán</th>
                    <th>Số lượng</th>
                    <th>Tuỳ chọn</th>
                  </tr>
                </thead>
                <tbody>
  <tr v-for="food in selectedFoods" :key="food.id">
    <td>
      <input type="checkbox" v-model="food.checked" />
    </td>
    <td>
      <img
      :src="`/img/food/${food.image}`"
        :alt="food.name"
        class="me-2 img_thumbnail"
      />
      {{ food.name }}
    </td>
    <td>{{ formatNumber(food.price) }} VNĐ</td>
    <td>
      <div class="qty-control px-2 py-1">
        <button type="button" class="btn-sm" @click="decreaseQuantity(food)" style="background-color: #fff">
          -
        </button>
        <span>{{ food.quantity }}</span>
        <button type="button" class="btn-sm" @click="increaseQuantity(food)" style="background-color: #fff">
          +
        </button>
      </div>
    </td>
    <td class="d-flex justify-content-center gap-2">
      <button class="btn btn-danger-delete" @click="removeFood(food.id)">Xoá</button>
    </td>
  </tr>
</tbody>

              </table>
            </div>
            <button class="btn btn-danger-save" data-bs-toggle="modal" data-bs-target="#menuModal">
              Thêm món
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card rounded-0 border-0 shadow mb-2">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
  <label for="price" class="form-label">
    Giá bán <span class="text-danger">*</span>
  </label>
  <input
    v-model="price"
    type="number"
    class="form-control rounded-0"
    id="price"
    min="0"
    required
  />
</div>

<div class="col mb-3">
  <label for="originPrice" class="form-label">
    Giá gốc
  </label>
  <input
    :value="originPriceFormatted"
    type="text"
    class="form-control rounded-0"
    id="originPriceFormatted"
    disabled
  />
</div>

          </div>
        </div>
      </div>
      <div class="card rounded-0 border-0 shadow">
        <div class="card-body">
          <div class="mb-3">
            <label for="image" class="form-label"
              >Ảnh Combo <span class="text-danger">*</span></label
            >
            <input class="form-control rounded-0" type="file" id="image" />
            <div class="mb-3 p-2 text-center">
              <img src="/img/food/cb1.webp" class="w-50" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-danger-save">+ Thêm Combo</button>

  <div
    class="modal fade"
    id="menuModal"
    tabindex="-1"
    aria-labelledby="menuModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
      <div class="modal-content shadow-sm rounded-3">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="menuModalLabel">Danh sách món</h5>
          <button
            type="button"
            class="btn btn-sm btn-outline-secondary"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            &times;
          </button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-12 col-md-8 mb-2 mb-md-0">
              <input
                type="text"
                class="form-control rounded"
                id="searchInput"
                placeholder="Nhập tên món..."
              />
            </div>
            <div class="col-12 col-md-4">
              <select
                  class="form-control rounded"
                  @change="getFoodByCategory($event.target.value)"
                >
                  <option value="">Tất cả món ăn</option>
                  <option v-for="item in flatCategoryList" :key="item.id" :value="item.id">
                    {{ item.indent }}{{ item.name }}
                  </option>
                </select>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th style="width: 5%">Chọn</th>
                  <th style="width: 60%">Tên món</th>
                  <th style="width: 35%">Giá</th>
                </tr>
              </thead>
              <tbody id="menuList">
                <tr v-for="food in foods" :key="food.id" :value="food.name">
                  <td>
                    <input
                      type="checkbox"
                      class="form-check-input menu-checkbox"
                      :value="food.id"
                       @change="toggleSelect(food)"
                        :checked="isSelected(food.id)"
                    />
                  </td>
                  <td class="text-start">{{ food.name }}</td>
                  <td>{{ formatNumber(food.price) }} VND</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger-delete" data-bs-dismiss="modal">Đóng</button>
          <button  @click="addSelectedFoods" type="button" class="btn btn-danger-save">Thêm vào</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import numeral from 'numeral'
import { FoodList } from '@/stores/food.js'
import { Modal } from 'bootstrap'
const {
  getFoodByCategory,
  flatCategoryList,
  foods,
    } = FoodList.setup()

const formatNumber = (value) => {
  return numeral(value).format('0,0')
}
const allFoodsForAdmin = ref([])
const allCatesForAdmin = ref([])
const fetchAllFoodsForCombo = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/admin/foods')
    allFoodsForAdmin.value = res.data
    if (!allFoodsForAdmin.value || allFoodsForAdmin.value.length === 0) {
      console.log('Không có dữ liệu thức ăn')
    }
  } catch (error) {
    console.error('Lỗi khi lấy dữ liệu thức ăn:', error)
  }
}
const fetchAllCatesForCombo = async () => {
  try {
    const res = await axios(`http://127.0.0.1:8000/api/admin/categories`)
    const data = res.data
    data.shift()
    allCatesForAdmin.value = data
    if (!allCatesForAdmin.value || allCatesForAdmin.value.length === 0) {
      console.log('Không có dữ liệu của danh mục')
    }
  } catch (error) {
    console.log(error)
  }
}

/**them combo */
const selectedFoods = ref([])
const isSelected = (id) => {
  return selectedFoods.value.some(item => item.id === id)
}
const toggleSelect = (food) => {
  const index = selectedFoods.value.findIndex(item => item.id === food.id)
  if (index === -1) {
    selectedFoods.value.push({ ...food, quantity: 1 })
  } else {
    selectedFoods.value.splice(index, 1)
  }
}
const addSelectedFoods = () => {
  console.log('Món đã chọn:', selectedFoods.value)

  const modalEl = document.getElementById('menuModal')
  let modalInstance = Modal.getInstance(modalEl)
  if (!modalInstance) {
    modalInstance = new Modal(modalEl)
  }
  modalInstance.hide()
  const backdrop = document.querySelector('.modal-backdrop')
  if (backdrop) {
    backdrop.remove()
    document.body.classList.remove('modal-open')
    document.body.style = ''
  }
}

function increaseQuantity(food) {
  food.quantity++
}
function decreaseQuantity(food) {
  if (food.quantity > 1) {
    food.quantity--
  }
}
function removeFood(id) {
  if (confirm('Bạn có muốn xóa món khỏi danh sách hiện tại?')) {
    selectedFoods.value = selectedFoods.value.filter(f => f.id !== id)
  }
}
//giá
const originPrice = computed(() => {
  return selectedFoods.value.reduce((sum, food) => {
    return sum + (food.price) * (food.quantity || 1)
  }, 0)
})

const originPriceFormatted = computed(() => {
  return numeral(originPrice.value).format('0,0')
})








onMounted(() => {
  fetchAllFoodsForCombo()
  fetchAllCatesForCombo()
})
</script>

<style>
.themsp {
  width: 200px;
}

.img_thumbnail {
  width: 50px;
}

.btn-danger-delete {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition:
    background-color 0.3s ease,
    color 0.3s ease;
}

.btn-danger-delete:hover {
  background-color: #c92c3c;
  color: #fff;
}
.btn-danger-save {
  background: none;
  color: #1d54bc;
  border: 1px solid #1d54bc;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition:
    background-color 0.3s ease,
    color 0.3s ease;
}

.btn-danger-save:hover {
  background-color: #1d54bc;
  color: #fff;
}
.form-select:focus {
  border-color: #c92c3c;
  box-shadow: none;
}
#menuModal > div > div > div.modal-header > button {
  background-color: #fff !important;
}
#menuModal .modal-header .btn-outline-secondary {
  border: none !important;
  background: none !important;
  padding: 0.25rem 0.5rem;
  font-size: 1.5rem;
  line-height: 1;
}

#menuModal .modal-header .btn-outline-secondary:hover {
  background: none !important;
  color: inherit !important;
}
</style>
