<template>
  <div>
    <div class="d-flex justify-content-between">
      <h3 class="text-danger fw-bold">Cập nhật danh mục</h3>
      <div>
        <router-link :to="{ name: 'admin-categories' }" class="btn btn-outline-secondary rounded-0">
          <i class="bi bi-arrow-counterclockwise"></i> Quay lại
        </router-link>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="text-center my-4">
      <div class="spinner-border text-danger" role="status"></div>
      <p>Đang tải dữ liệu danh mục...</p>
    </div>

    <form v-else class="row mt-2">
      <div class="col-12 col-md-6">
        <div class="card rounded-0 border-0 shadow mb-4">
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
              <input type="text" v-model="name" class="form-control rounded-0" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Danh mục cha</label>
              <select class="form-select rounded-0" v-model="parentId">
                <option value="">-- Không --</option>
                <option v-for="cat in allParents" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Làm danh mục mặc định</label>
              <select class="form-select rounded-0" v-model="isDefault">
                <option :value="true">Có</option>
                <option :value="false">Không</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 mb-4">
        <div class="card rounded-0 border-0 shadow">
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Ảnh danh mục</label>
              <input class="form-control rounded-0" type="file" @change="handleImageChange">
              <div class="mb-3 p-2 text-center">
                <img v-if="previewImage" :src="previewImage" class="w-50" />
                <img v-else-if="oldImage" :src="'http://127.0.0.1:8000/storage/img/food/imgmenu/' + oldImage" class="w-50" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

    <button
      type="button"
      class="btn btn-danger-save mt-2"
      @click="updateCategory"
      :disabled="isLoading"
    >
      Cập nhật
    </button>
  </div>
</template>

<script>
import axios from 'axios'
import Swal from 'sweetalert2'
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default {
  setup() {
    const route = useRoute()
    const router = useRouter()
    const categoryId = route.params.id

    const name = ref('')
    const parentId = ref('')
    const isDefault = ref(false)
    const image = ref(null)
    const oldImage = ref(null)
    const previewImage = ref(null)
    const allParents = ref([])
    const isLoading = ref(true)

    const fetchParents = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/admin/categories/parents/list', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        allParents.value = res.data.data
      } catch (err) {
        showToast('Lỗi khi tải danh mục cha!', 'error')
      }
    }

    const getCategoryById = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/admin/categories/${categoryId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        const cat = res.data.data
        name.value = cat.name
        isDefault.value = !!cat.default
        oldImage.value = cat.images
        parentId.value = cat.parent_id ?? ''
      } catch (error) {
        showToast('Không thể tải dữ liệu danh mục!', 'error')
      } finally {
        isLoading.value = false
      }
    }

    const handleImageChange = (e) => {
      const file = e.target.files[0]
      if (file) {
        image.value = file
        previewImage.value = URL.createObjectURL(file)
      }
    }

    const showToast = (message, icon = 'success') => {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      })
    }

    const updateCategory = async () => {
      if (!name.value.trim()) {
        showToast('Tên danh mục là bắt buộc!', 'error')
        return
      }

      const formData = new FormData()
      formData.append('_method', 'PUT')
      formData.append('name', name.value)
      if (parentId.value) formData.append('parent_id', parentId.value)
      if (image.value) formData.append('images', image.value)
      formData.append('default', isDefault.value ? 1 : 0)

      try {
        await axios.post(`http://127.0.0.1:8000/api/admin/categories/${categoryId}`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          }
        })

        showToast('Cập nhật thành công!')
      } catch (error) {
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors
          let msg = ''
          for (const key in errors) {
            msg += `${errors[key][0]}\n`
          }

          Swal.fire({
            icon: 'error',
            title: 'Lỗi xác thực',
            text: msg,
            toast: true,
            timer: 5000,
            position: 'top-end',
            showConfirmButton: false,
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi server',
            text: 'Không thể cập nhật danh mục.',
            toast: true,
            timer: 4000,
            position: 'top-end',
            showConfirmButton: false,
          })
        }
      }
    }

    onMounted(() => {
      fetchParents()
      getCategoryById()
    })

    return {
      name, parentId, isDefault, image, previewImage, oldImage, allParents, isLoading,
      handleImageChange, updateCategory
    }
  }
}
</script>

<style>
.themsp {
  width: 200px;
}
</style>
