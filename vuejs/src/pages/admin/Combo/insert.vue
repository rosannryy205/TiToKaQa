<template v-if="hasPermission('create_combo')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h3 class="text-danger fw-bold">Thêm Combo</h3>
            <div>
              <router-link to="/admin/products/combo"><a href="#" class="btn btn-outline-secondary rounded-0">
                <i class="bi bi-arrow-counterclockwise"></i> Quay lại
              </a>
            </router-link>
            </div>
          </div>
          <form class="row mt-2">
            <div class="col-md-8">
              <div class="card rounded-0 border-0 shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col mb-3">
                      <label for="name" class="form-label">Tên Combo <span class="text-danger">*</span></label>
                      <input v-model="comboName" type="text" class="form-control rounded-0" id="comboName" required />
                    </div>
                    <div class="col mb-3">
                      <label for="category" class="form-label">Trạng thái <span class="text-danger">*</span></label>
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
                    <textarea class="form-control rounded-0" id="description" rows="3" v-model="description"></textarea>
                  </div>

                  <div class="mb-3">
                    <div style="max-height: 200px ; overflow-y: auto;" class="table-responsive d-none d-lg-block">
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
                              <img :src="getImageUrl(food.image)" :alt="food.name" class="me-2 img_thumbnail" />
                              {{ food.name }}
                            </td>
                            <td>{{ formatNumber(food.price) }} VNĐ</td>
                            <td>
                              <div class="qty-control px-2 py-1">
                                <button type="button" class="btn-sm" @click="decreaseQuantity(food)"
                                  style="background-color: #fff">
                                  -
                                </button>
                                <span>{{ food.quantity }}</span>
                                <button type="button" class="btn-sm" @click="increaseQuantity(food)"
                                  style="background-color: #fff">
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
                    <button type="button" class="btn btn-danger-save" data-bs-toggle="modal"
                      data-bs-target="#menuModal">
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
                      <input v-model="salePrice" type="number" class="form-control rounded-0" id="price" min="0"
                        required />
                    </div>

                    <div class="col mb-3">
                      <label for="originPrice" class="form-label"> Giá gốc </label>
                      <input :value="originPriceFormatted" type="text" class="form-control rounded-0"
                        id="originPriceFormatted" disabled />
                    </div>
                  </div>
                </div>
              </div>
              <div class="card rounded-0 border-0 shadow">
                <div class="card-body">
                  <div class="mb-3">
                    <label for="image" class="form-label">Ảnh Combo <span class="text-danger">*</span></label>
                    <input class="form-control rounded-0" type="file" id="image" @change="handleImage" />
                    <div class="mb-3 p-2 text-center">
                      <img :src="imagePreview" v-if="imagePreview" class="w-50" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
          <button type="button" class="btn btn-danger-save" @click="createCombosByAdmin">+ Thêm Combo</button>
          <div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
              <div class="modal-content shadow-sm rounded-3">
                <div class="modal-header">
                  <h5 class="modal-title fw-semibold" id="menuModalLabel">Danh sách món</h5>
                  <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal"
                    aria-label="Close">
                    &times;
                  </button>
                </div>
                <div class="modal-body">
                  <div class="row mb-3">
                    <div class="col-12 col-md-8 mb-2 mb-md-0">
                      <input v-model="searchQuery" type="text" class="form-control rounded" id="searchInput"
                        placeholder="Nhập tên món..." />
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
                        <tr v-for="food in filteredCombos" :key="food.id" :value="food.name">
                          <td>
                            <input type="checkbox" class="form-check-input menu-checkbox" :value="food.id"
                              @change="toggleSelect(food)" :checked="isSelected(food.id)" />
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
import { Permission } from '@/stores/permission'
const router = useRouter()
import Swal from 'sweetalert2'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
import { useRouter } from 'vue-router'
const { getFoodByCategory, flatCategoryList, foods } = FoodList.setup()

const formatNumber = (value) => numeral(value).format('0,0')
const getImageUrl = (image) => {
  return `${STORAGE_URL}/img/food/${image}`
}
// ============================
// USER PERMISSION
// ============================
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)

// ============================
// FETCH DATA
// ============================
const allFoodsForAdmin = ref([])
const allCatesForAdmin = ref([])

const fetchAllFoodsForCombo = async () => {
  try {
    const res = await axios.get(`${API_URL}/admin/foods`)
    allFoodsForAdmin.value = res.data || []
  } catch (error) {
    console.error('Lỗi khi lấy dữ liệu thức ăn:', error)
  }
}

const fetchAllCatesForCombo = async () => {
  try {
    const res = await axios.get(`${API_URL}/admin/categories`)
    const data = res.data || []
    data.shift()
    allCatesForAdmin.value = data
  } catch (error) {
    console.error('Lỗi lấy danh mục:', error)
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
// MÓN ĂN ĐƯỢC CHỌN & SEARCH
// ============================
const selectedFoods = ref([])
const searchQuery = ref('')

const filteredCombos = computed(() => {
  const keyword = searchQuery.value.toLowerCase()
  return foods.value.filter((p) =>
    p.name.toLowerCase().includes(keyword)
  )
})

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
  const modalInstance = Modal.getInstance(modalEl)
  if (modalInstance) {
    modalInstance.hide()
  } else {
    modalEl.classList.remove('show')
    modalEl.setAttribute('aria-hidden', 'true')
    modalEl.style.display = 'none'
    document.body.classList.remove('modal-open')
    document.body.style = ''
    const backdrop = document.querySelector('.modal-backdrop')
    if (backdrop) backdrop.remove()
  }
}

const cleanUpBody = () => {
  document.body.classList.remove('modal-open')
  document.body.style = ''
  const backdrop = document.querySelector('.modal-backdrop')
  if (backdrop) backdrop.remove()
}

function increaseQuantity(food) {
  food.quantity++
}

function decreaseQuantity(food) {
  if (food.quantity > 1) food.quantity--
}

function removeFood(id) {
  Swal.fire({
    icon: 'question',
    title: 'Xác nhận',
    text: 'Bạn có muốn xóa món này khỏi danh sách?',
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
  }).then((result) => {
    if (result.isConfirmed) {
      selectedFoods.value = selectedFoods.value.filter((f) => f.id !== id)
    }
  })
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
  formatNumber(originPrice.value)
)

// ============================
// VALIDATION
// ============================
const validateBeforeSubmit = () => {
  if (!comboName.value.trim()) {
    Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng nhập tên Combo' })
    return false
  }

  if (!status.value) {
    Swal.fire({ icon: 'warning', title: 'Thiếu thông tin', text: 'Vui lòng chọn trạng thái' })
    return false
  }

  if (!salePrice.value || salePrice.value <= 0) {
    Swal.fire({ icon: 'warning', title: 'Giá không hợp lệ', text: 'Giá bán phải lớn hơn 0' })
    return false
  }

  if (!selectedImage.value) {
    Swal.fire({ icon: 'warning', title: 'Thiếu ảnh', text: 'Vui lòng chọn ảnh Combo' })
    return false
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp']
  const maxSizeMB = 2
  const file = selectedImage.value

  if (!allowedTypes.includes(file.type)) {
    Swal.fire({
      icon: 'warning',
      title: 'Định dạng ảnh không hợp lệ',
      text: 'Ảnh phải là jpg, jpeg, png hoặc webp',
    })
    return false
  }

  if (file.size > maxSizeMB * 1024 * 1024) {
    Swal.fire({
      icon: 'warning',
      title: 'Ảnh quá lớn',
      text: 'Dung lượng ảnh tối đa là 2MB',
    })
    return false
  }

  if (selectedFoods.value.length === 0) {
    Swal.fire({ icon: 'warning', title: 'Chưa chọn món', text: 'Vui lòng chọn ít nhất 1 món' })
    return false
  }

  return true
}

const resetForm = () => {
  comboName.value = ''
  salePrice.value = 0
  status.value = ''
  description.value = ''
  selectedImage.value = null
  imagePreview.value = null
  selectedFoods.value = []
}

// ============================
// GỬI FORM
// ============================
const createCombosByAdmin = async () => {
  if (!validateBeforeSubmit()) return

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
      `${API_URL}/admin/combos/create`,
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Tạo combo thành công!',
      showConfirmButton: false,
      timer: 1500,
      timerProgressBar: true,
    })

    resetForm()
    router.push('/admin/products/combo')
  } catch (error) {
    if (error.response?.status === 422 && error.response.data.errors) {
      const messages = Object.values(error.response.data.errors).flat().join('<br>')
      Swal.fire({
        icon: 'warning',
        title: 'Lỗi xác thực!',
        html: messages,
      })
    } else if (error.response?.status === 422 && error.response.data.error) {
      Swal.fire({
        icon: 'error',
        title: 'Lỗi!',
        text: error.response.data.error,
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Lỗi hệ thống!',
        text: error.response?.data?.message || 'Đã có lỗi xảy ra',
      })
    }
  }
}

// ============================
// ON MOUNT
// ============================
onMounted(() => {
  fetchAllFoodsForCombo()
  fetchAllCatesForCombo()

  const modalEl = document.getElementById('menuModal')
  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', cleanUpBody)
  }
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

#menuModal>div>div>div.modal-header>button {
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
