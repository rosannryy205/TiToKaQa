<template v-if="hasPermission('edit_food')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h3 class="text-danger fw-bold">Cập nhật món ăn</h3>
            <div>
              <router-link to="/admin/products" class="btn btn-outline-secondary rounded-0">
                <i class="bi bi-arrow-counterclockwise"></i> Quay lại
              </router-link>
            </div>
          </div>

          <form @submit.prevent="updateFood" class="row mt-2" enctype="multipart/form-data">
            <div class="col-md-8">
              <div class="card rounded-0 border-0 shadow mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col mb-3">
                      <label for="name" class="form-label">Tên món <span class="text-danger">*</span></label>
                      <input type="text" id="name" class="form-control rounded-0" v-model="food.name" required>
                    </div>

                    <div class="col mb-3">
                      <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                      <select id="status" class="form-select rounded-0" v-model="food.status" required>
                        <option disabled value="">Chọn trạng thái</option>
                        <option value="inactive">Ẩn</option>
                        <option value="active">Hiện</option>
                      </select>

                    </div>
                  </div>

                  <div class="row">
                    <div class="col mb-3">
                      <label for="instock" class="form-label">Tồn kho <span class="text-danger">*</span></label>
                      <input type="number" id="instock" class="form-control rounded-0" min="0"
                        v-model.number="food.instock" required>
                    </div>

                    <div class="col mb-3">
                      <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                      <select id="category" class="form-select rounded-0" v-model="food.category_id" required>
                        <option disabled value="">Chọn danh mục</option>
                        <template v-for="category in categories" :key="category.id">
                          <option :value="category.id">{{ category.name }}</option>
                          <option v-for="child in category.children" :key="'child-' + child.id" :value="child.id">
                            &nbsp;&nbsp;↳ {{ child.name }}
                          </option>
                        </template>
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea id="description" class="form-control rounded-0" rows="6"
                      v-model="food.description"></textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4 mb-4">
              <div class="card rounded-0 border-0 shadow mb-2">
                <div class="card-body">
                  <div class="row">
                    <div class="col mb-3">
                      <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                      <input type="number" id="price" class="form-control rounded-0" min="0" v-model.number="food.price"
                        required>
                    </div>

                    <div class="col mb-3">
                      <label for="sale_price" class="form-label">Giá giảm</label>
                      <input type="number" id="sale_price" class="form-control rounded-0" min="0"
                        v-model.number="food.sale_price">
                    </div>
                  </div>
                </div>
              </div>

              <div class="card rounded-0 border-0 shadow">
                <div class="card-body">
                  <div class="mb-3">
                    <label for="image" class="form-label">Ảnh món ăn <span class="text-danger">*</span></label>
                    <input type="file" id="image" class="form-control rounded-0" @change="onFileChange">
                  </div>
                  <div class="mb-3 p-2 text-center" v-if="previewImage">
                    <img :src="previewImage" class="w-50" alt="Preview image">
                  </div>
                  <div class="mb-3 p-2 text-center" v-else-if="food.image">
                    <img :src="'http://127.0.0.1:8000/storage/img/food/' + food.image" class="w-50" alt="Current image">
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 mb-4">
              <button type="submit" class="btn btn-danger-save mt-2" style="width: 200px;">
                Cập nhật món ăn
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2';
import { Permission } from '@/stores/permission'
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)

const route = useRoute()
const router = useRouter()

const food = ref({
  name: '',
  status: '',
  instock: 0,
  category_id: '',
  description: '',
  price: 0,
  sale_price: 0,
  image_url: '',
})

const categories = ref([])
const previewImage = ref(null)
let selectedFile = null

const fetchCategories = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/admin/categories', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    categories.value = res.data
  } catch (error) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Lỗi khi tải danh mục',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    })
    console.error(error)
  }
}

const fetchFood = async (id) => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/admin/food/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    const data = res.data
    food.value.name = data.name
    food.value.status = data.status
    food.value.instock = data.stock
    food.value.category_id = data.category_id
    food.value.description = data.description
    food.value.price = data.price
    food.value.sale_price = data.sale_price
    food.value.image = data.image

  } catch (error) {
    const msg = error.response?.data?.message || error.message || 'Không xác định'
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: `Lỗi khi tải món ăn: ${msg}`,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    })
    console.error(error)
  }
}

const onFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    selectedFile = file
    previewImage.value = URL.createObjectURL(file)
  }
}


const updateFood = async () => {
  try {
    const formData = new FormData()
    formData.append('name', food.value.name)
    formData.append('status', food.value.status)
    formData.append('stock', food.value.instock)
    formData.append('category_id', food.value.category_id)
    formData.append('description', food.value.description)
    formData.append('price', food.value.price)
    if (food.value.sale_price !== null && food.value.sale_price !== '') {
      formData.append('sale_price', food.value.sale_price)
    }

    if (selectedFile) {
      formData.append('image', selectedFile)
    }

    const id = route.params.id
    await axios.post(`http://127.0.0.1:8000/api/admin/update-food/${id}`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Cập nhật món ăn thành công!',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    })

    router.push('/admin/products')
  } catch (error) {
    let msg = 'Lỗi khi cập nhật món ăn. '
    if (error.response) {
      msg += `(${error.response.status}) ${error.response.data.message || JSON.stringify(error.response.data)}`
    } else if (error.request) {
      msg += 'Không nhận được phản hồi từ server.'
    } else {
      msg += error.message
    }

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: msg,
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true
    })
    console.error(error)
  }
}



onMounted(() => {
  fetchCategories()
  const id = route.params.id
  if (id) {
    fetchFood(id)
  }
})
</script>

<style>
.themsp {
  width: 200px;
}
</style>
