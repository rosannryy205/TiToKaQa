<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Cập nhật Combo</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>
  <form class="row mt-2" v-if="selectedCombo">
    <div class="col-md-8">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
              <label for="name" class="form-label">
                Tên Combo <span class="text-danger">*</span>
              </label>
              <input
                v-model="selectedCombo.name"
                type="text"
                class="form-control rounded-0"
                id="comboName"
                required
              />
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label">
                Trạng thái <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <select class="form-select rounded-0" id="category" v-model="selectedCombo.status" required>
                  <option disabled value="">Chọn trạng thái cho món ăn</option>
                  <option value="inactive">Ẩn</option>
                  <option value="active">Hiện</option>
                </select>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea
              class="form-control rounded-0"
              id="description"
              rows="3"
              v-model="selectedCombo.description"
            ></textarea>
          </div>

          <div class="mb-3">
  <div style="max-height: 200px; overflow-y: auto;" class="table-responsive d-none d-lg-block">
    <!-- Bảng món ăn trong combo -->
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Chọn</th>
          <th>Món ăn</th>
          <th>Giá bán</th>
          <th>Số lượng</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="food in selectedCombo.foods" :key="food.id">
          <td><input type="checkbox" v-model="food.checked" /></td>
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
              <button type="button" class="btn-sm" @click="decreaseQuantity(food)" style="background-color: #fff">-</button>
              <span>{{ food.quantity }}</span>
              <button type="button" class="btn-sm" @click="increaseQuantity(food)" style="background-color: #fff">+</button>
            </div>
          </td>
          <td class="d-flex justify-content-center gap-2">
            <button class="btn btn-danger-delete" @click.prevent="removeFood(food.id)">Xoá</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-between align-items-center mt-2">
    <button type="button" class="btn btn-danger-save" data-bs-toggle="modal" data-bs-target="#menuModal">
      Thêm món
    </button>
    <span class="text-danger fw-bold medium">Giá gốc Combo: {{ numeral(originalTotalPrice).format('0,0') }} VNĐ</span>
  </div>
</div>

        </div>
      </div>
    </div>
    <div class="col-md-4 mb-4">
      <div class="card rounded-0 border-0 shadow mb-2">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
              <p>Giá combo (ưu đãi) hiện tại: {{ numeral(selectedCombo.price).format('0,0') }} VNĐ</p>
<input
  v-model="selectedCombo.price"
  type="number"
  class="form-control rounded-0 mt-1"
  id="price"
  min="0"
  required
/>

            </div>
          </div>
        </div>
      </div>
      <div class="card rounded-0 border-0 shadow">
        <div class="card-body">
          <div class="mb-3">
            <label for="image" class="form-label">Ảnh Combo <span class="text-danger">*</span></label>
            <input class="form-control rounded-0" type="file" id="image" @change="handleImageUpload" />
            <div class="mb-3 p-2 text-center">
              <img :src="imagePreview" v-if="imagePreview" class="w-50" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-danger-save mt-2" @click="updateCombo">Cập nhật Combo</button>
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
// ================== IMPORT ==================
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import { FoodList } from '@/stores/food.js'
import numeral from 'numeral'
import { Modal } from 'bootstrap'

// ================== BIẾN & ROUTE ==================
const route = useRoute()
const comboId = route.params.id
const selectedCombo = ref(null)
const imageFile = ref(null)
const imagePreview = ref('')
const selectedFoods = ref([])

// ================== STORE GỌI TỪ PINIA ==================
const { getFoodByCategory, flatCategoryList, foods } = FoodList.setup()

// ================== HÀM XỬ LÝ CHUNG ==================
const formatNumber = (n) => new Intl.NumberFormat().format(n)

const originalTotalPrice = computed(() => {
  return selectedCombo.value.foods.reduce((sum, food) => {
    return sum + food.price * food.quantity
  }, 0)
})

const isSelected = (id) => {
  return selectedFoods.value.some((item) => item.id === id)
}

const recalculateComboPrice = () => {
  const total = selectedCombo.value.foods.reduce((sum, food) => {
    return sum + food.price * food.quantity
  }, 0)
  selectedCombo.value.price = total
}

// ================== HÀM LẤY DỮ LIỆU BAN ĐẦU ==================
async function fetchComboById() {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/admin/combos/${comboId}`)
    selectedCombo.value = res.data
    selectedCombo.value.foods = res.data.foods.map(food => ({
      ...food,
      checked: true,
      quantity: food.pivot.quantity,
    }))
    imagePreview.value = `/img/food/${res.data.image}`
  } catch (error) {
    console.error(error)
    toast.error('Lỗi khi tải combo!')
  }
}

// ================== XỬ LÝ TĂNG / GIẢM / XOÁ ==================
const increaseQuantity = (food) => {
  food.quantity++
}

const decreaseQuantity = (food) => {
  if (food.quantity > 1) {
    food.quantity--
  }
}

function removeFood(id) {
  const food = selectedCombo.value.foods.find(f => f.id === id)
  if (food && confirm("Bạn có muốn xóa món này ra khỏi combo không?")) {
    selectedCombo.value.foods = selectedCombo.value.foods.filter(f => f.id !== id)
    recalculateComboPrice()
  }
}

// ================== HÀM THÊM MÓN ĂN VÀO COMBO ==================
const toggleSelect = (food) => {
  const index = selectedFoods.value.findIndex((item) => item.id === food.id)
  if (index === -1) {
    selectedFoods.value.push({ ...food, quantity: 1 })
  } else {
    selectedFoods.value.splice(index, 1)
  }
}

const addSelectedFoods = () => {
  let duplicated = []

  selectedFoods.value.forEach(food => {
    const exist = selectedCombo.value.foods.some(f => f.id === food.id)
    if (!exist) {
      selectedCombo.value.foods.push({
        ...food,
        quantity: 1
      })
    } else {
      duplicated.push(food.name)
    }
  })

  if (!selectedCombo.value.price || selectedCombo.value.price === 0) {
    const total = selectedCombo.value.foods.reduce((sum, food) => {
      return sum + food.price * food.quantity
    }, 0)
    selectedCombo.value.price = total
  }

  if (duplicated.length > 0) {
    toast.warning(`Món ${duplicated.join(', ')} đã có trong combo`, { autoClose: 2000 })
  }

  selectedFoods.value = []

  const modalEl = document.getElementById('menuModal')
  const modalInstance = Modal.getInstance(modalEl) || new Modal(modalEl)
  modalInstance.hide()

  const backdrop = document.querySelector('.modal-backdrop')
  if (backdrop) {
    backdrop.remove()
    document.body.classList.remove('modal-open')
    document.body.style = ''
  }
}

// ================== XỬ LÝ ẢNH ==================
function handleImageUpload(event) {
  const file = event.target.files[0]
  imageFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

// ================== CẬP NHẬT COMBO ==================
async function updateCombo() {
  try {
    const formData = new FormData()
    formData.append('name', selectedCombo.value.name)
    formData.append('price', selectedCombo.value.price)
    formData.append('description', selectedCombo.value.description || '')
    formData.append('status', selectedCombo.value.status || 'active')

    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }

    const foods = selectedCombo.value.foods.map(food => ({
      id: food.id,
      quantity: food.quantity,
    }))
    formData.append('foods', JSON.stringify(foods))
    for (let [key, value] of formData.entries()) {
  if (key === 'image' && value instanceof File) {
    console.log(`${key}: [File] name=${value.name}, size=${value.size}B, type=${value.type}`)
  } else {
    console.log(`${key}: ${value}`)
  }
}
    await axios.patch(
      `http://127.0.0.1:8000/api/admin/combos/update/${comboId}`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    toast.success('Cập nhật combo thành công!')
  } catch (error) {
    console.error(error)
    toast.error('Cập nhật combo thất bại!')
  }
}


// ================== Xóa COMBO ==================
async function deleteCombo(comboId) {
  try {
    if (!confirm('Bạn có chắc muốn xóa combo này?')) return;
    await axios.delete(`http://127.0.0.1:8000/api/admin/combos/delete/${comboId}`)
    toast.success('Xóa combo thành công!')
  } catch (error) {
    console.error(error)
    toast.error('Lỗi khi xóa combo!')
  }
}


// ================== KHỞI TẠO ==================
onMounted(() => {
  fetchComboById()
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
