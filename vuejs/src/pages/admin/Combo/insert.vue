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
              <input
                v-model="comboName"
                type="text"
                class="form-control rounded-0"
                id="comboName"
                required
              />
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label"
                >Trạng thái <span class="text-danger">*</span></label
              >
              <div class="input-group">
                <select class="form-select rounded-0" id="category" v-model="status" required>
                  <option disabled value="">Chọn trạng thái cho món ăn</option>
                  <option value="inactive">Ẩn</option>
                  <option value="active">Hiện</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control rounded-0" id="description" rows="3"
            v-model="description"></textarea>
          </div>

          <div class="mb-3">
            <div style="max-height: 200px; overflow-y: auto;"
            class="table-responsive d-none d-lg-block" >
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
                        <button
                          type="button"
                          class="btn-sm"
                          @click="decreaseQuantity(food)"
                          style="background-color: #fff"
                        >
                          -
                        </button>
                        <span>{{ food.quantity }}</span>
                        <button
                          type="button"
                          class="btn-sm"
                          @click="increaseQuantity(food)"
                          style="background-color: #fff"
                        >
                          +
                        </button>
                      </div>
                    </td>
                    <td class="d-flex justify-content-center gap-2">
                      <button class="btn btn-danger-delete" @click="removeFood(food.id)">
                        Xoá
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-danger-save" data-bs-toggle="modal" data-bs-target="#menuModal">
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
                v-model="salePrice"
                type="number"
                class="form-control rounded-0"
                id="price"
                min="0"
                required
              />
            </div>

            <div class="col mb-3">
              <label for="originPrice" class="form-label"> Giá gốc </label>
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
            <input 
            class="form-control rounded-0" 
            type="file" 
            id="image" 
            @change="handleImage"/>
            <div class="mb-3 p-2 text-center">
              <img :src="imagePreview" v-if="imagePreview" class="w-50" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-danger-save"
  @click="createCombosByAdmin"
  >+ Thêm Combo</button>
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
              <select class="form-control rounded" @change="getFoodByCategory($event.target.value)">
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
          <button @click="addSelectedFoods" type="button" class="btn btn-danger-save">
            Thêm vào
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import numeral from 'numeral'
import { FoodList } from '@/stores/food.js'
import { Modal } from 'bootstrap'
import { toast } from 'vue3-toastify'

const { getFoodByCategory, flatCategoryList, foods } = FoodList.setup()

const formatNumber = (value) => numeral(value).format('0,0')

const allFoodsForAdmin = ref([])
const allCatesForAdmin = ref([])

const fetchAllFoodsForCombo = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/admin/foods')
    allFoodsForAdmin.value = res.data || []
  } catch (error) {
    console.error('Lỗi khi lấy dữ liệu thức ăn:', error)
  }
}

const fetchAllCatesForCombo = async () => {
  try {
    const res = await axios(`http://127.0.0.1:8000/api/admin/categories`)
    const data = res.data || []
    data.shift() 
    allCatesForAdmin.value = data
  } catch (error) {
    console.log('Lỗi lấy danh mục:', error)
  }
}

// ============================
// DỮ LIỆU FORM
// ============================
const comboName = ref('')
const salePrice = ref(0)
const status = ref('')
const description = ref('')
const selectedImage = ref(null)
const imagePreview = ref(null)

function handleImage(event) {
  const file = event.target.files[0]
  if (file) {
    selectedImage.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

// ============================
// MÓN ĂN ĐƯỢC CHỌN
// ============================
const selectedFoods = ref([])

const isSelected = (id) => {
  return selectedFoods.value.some((item) => item.id === id)
}

const toggleSelect = (food) => {
  const index = selectedFoods.value.findIndex((item) => item.id === food.id)
  if (index === -1) {
    selectedFoods.value.push({ ...food, quantity: 1 })
  } else {
    selectedFoods.value.splice(index, 1)
  }
}

const addSelectedFoods = () => {
  const modalEl = document.getElementById('menuModal')
  const modalInstance = Modal.getInstance(modalEl) || new Modal(modalEl)
  modalInstance.hide()

  //backdrop lỗi nếu có
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
    selectedFoods.value = selectedFoods.value.filter((f) => f.id !== id)
  }
}

// ============================
// TÍNH GIÁ GỐC
// ============================
const originPrice = computed(() =>
  selectedFoods.value.reduce((sum, food) => {
    return sum + food.price * (food.quantity || 1)
  }, 0)
)

const originPriceFormatted = computed(() =>
  numeral(originPrice.value).format('0,0')
)

// ============================
// GỬI DỮ LIỆU COMBO
// ============================
const validateBeforeSubmit = () => {
  if (!comboName.value.trim()) return 'Vui lòng nhập tên Combo'
  if (!status.value) return 'Vui lòng chọn trạng thái'
  if (!salePrice.value || salePrice.value <= 0) return 'Giá bán không hợp lệ'
  if (!selectedImage.value) return 'Vui lòng chọn ảnh Combo'
  if (selectedFoods.value.length === 0) return 'Vui lòng chọn ít nhất 1 món'
  return null
}

const createCombosByAdmin = async () => {
  const errorMsg = validateBeforeSubmit()
  if (errorMsg) {
    alert(errorMsg)
    return
  }

  try {
    const formData = new FormData()
    formData.append('name', comboName.value)
    formData.append('price', salePrice.value)
    formData.append('status', status.value)
    formData.append('description', description.value)
    formData.append('image', selectedImage.value)

    selectedFoods.value.forEach((item, index) => {
      formData.append(`combo_details[${index}][food_id]`, item.id)
      formData.append(`combo_details[${index}][quantity]`, item.quantity)
    })

    const res = await axios.post(
      'http://127.0.0.1:8000/api/admin/combos/create',
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    toast.success('Tạo combo thành công!')
    resetForm()
  } catch (error) {
    if (error.response?.status === 422 && error.response.data.error) {
      toast.warning("Tên Combo Đã Tồn Tại")
    console.log(error.response.data.error);
  } else {
    alert('Lỗi khác khi tạo combo');
  }
}
function resetForm() {
  comboName.value = ''
  salePrice.value = 0
  status.value = ''
  description.value = ''
  selectedImage.value = null
  imagePreview.value = null
  selectedFoods.value = []
}

// ============================
onMounted(() => {
  fetchAllFoodsForCombo()
  fetchAllCatesForCombo()
})
}
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
