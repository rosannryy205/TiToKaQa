<template v-if="hasPermission('create_food')">
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm món ăn</h3>
    <div>
      <RouterLink to="/admin/products" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </RouterLink>
    </div>
  </div>

  <form class="row mt-2" @submit.prevent="addFood">
    <div class="col-md-8">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">

          <div class="row">
            <div class="col mb-3">
              <label for="name" class="form-label">Tên món <span class="text-danger">*</span></label>
              <input type="text" class="form-control rounded-0" id="name" v-model="newFood.name" required>
            </div>
            <div class="col mb-3">
              <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
              <select class="form-select rounded-0" id="status" v-model="newFood.status" required>
                <option disabled value="">Chọn trạng thái cho món ăn</option>
                <option value="inactive">Ẩn</option>
                <option value="active">Hiện</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col mb-3">
              <label for="instock" class="form-label">Tồn kho <span class="text-danger">*</span></label>
              <input type="number" class="form-control rounded-0" id="instock" v-model="newFood.stock" min="0" required>
            </div>
            <div class="col mb-3">
              <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
              <select v-model="newFood.category_id" class="form-select rounded-0">
                <option disabled value="">-- Chọn danh mục --</option>
                <template v-for="category in categories" :key="category.id">
                  <option :value="category.id">{{ category.name }}</option>
                  <option v-for="child in category.children" :key="child.id" :value="child.id">
                    &nbsp;&nbsp;↳ {{ child.name }}
                  </option>
                </template>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control rounded-0" id="description" rows="6" v-model="newFood.description"></textarea>
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
              <input type="number" class="form-control rounded-0" id="price" v-model="newFood.price" min="0" required>
            </div>
            <div class="col mb-3">
              <label for="sale_price" class="form-label">Giá giảm</label>
              <input type="number" class="form-control rounded-0" id="sale_price" v-model="newFood.sale_price" min="0">
            </div>
          </div>
        </div>
      </div>

      <div class="card rounded-0 border-0 shadow">
        <div class="card-body">
          <div class="mb-3">
            <label for="image" class="form-label">Ảnh món ăn <span class="text-danger">*</span></label>
            <input class="form-control rounded-0" type="file" id="image" @change="handleImageChange">
            <div class="mb-3 p-2 text-center" v-if="previewImage">
              <img :src="previewImage" class="w-50" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <button type="button" class="btn btn-danger-save" @click="addFood">
    Thêm món ăn +
  </button>
</template>

<style>
.themsp {
  width: 200px;
}
</style>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
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


// Dữ liệu món ăn mới
const newFood = ref({
  name: '',
  price: '',
  sale_price: '',
  stock: '',
  category_id: '',
  description: '',
  image: null,
  status: 'active'
})

// Danh sách danh mục
const categories = ref([])

// Ảnh xem trước
const previewImage = ref(null)

// Lỗi thêm
const errorAdd = ref({})

// Load danh mục
const fetchCategories = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/admin/categories', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    categories.value = response.data
  } catch (error) {
    console.error('Lỗi khi load danh mục:', error)
  }
}

// Xử lý chọn ảnh
const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    newFood.value.image = file
    previewImage.value = URL.createObjectURL(file)
  }
}

// Gửi dữ liệu lên server
// Gửi dữ liệu lên server
const addFood = async () => {
  try {
    if (newFood.value.stock < 0) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Số lượng không được âm!',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      })
      return
    }
    const formData = new FormData()
    formData.append('name', newFood.value.name)
    formData.append('price', newFood.value.price)
    formData.append('sale_price', newFood.value.sale_price || '')
    formData.append('stock', newFood.value.stock)
    formData.append('category_id', newFood.value.category_id)
    formData.append('description', newFood.value.description || '')
    formData.append('image', newFood.value.image)
    formData.append('status', newFood.value.status)

    errorAdd.value = {}

    await axios.post('http://127.0.0.1:8000/api/admin/foods', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Thêm món ăn thành công!',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true
    })

    // Reset form
    newFood.value = {
      name: '',
      price: '',
      sale_price: '',
      stock: '',
      category_id: '',
      status: '',
      description: '',
      image: null
    }
    previewImage.value = null

  } catch (error) {
    console.error('Lỗi khi gửi request:', error)

    if (error.response) {
      console.error('Status:', error.response.status)
      console.error('Data:', error.response.data)

      if (error.response.status === 422) {
        const allErrors = error.response.data.errors
        const priorityOrder = [
          'name',
          'price',
          'image',
          'sale_price',
          'stock',
          'category_id',
          'description',
          'status'
        ]

        errorAdd.value = {}
        for (const field of priorityOrder) {
          if (allErrors[field]) {
            errorAdd.value[field] = allErrors[field][0]

            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: `Lỗi: ${allErrors[field][0]}`,
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            })

            break
          }
        }
      } else {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: `Lỗi không xác định: ${error.response.status}`,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        })
      }
    } else {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Không thể kết nối đến server hoặc lỗi khác.',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      })
    }
  }
}




onMounted(() => {
  fetchCategories()
})
</script>
<style>
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
</style>
