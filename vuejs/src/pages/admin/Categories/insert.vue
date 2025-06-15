<template>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm danh mục</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>

  <form class="row mt-2">
    <div class="col-12 col-md-6">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">
          <div class="row">
            <div class="mb-3">
              <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
              <input type="text" v-model="name" class="form-control rounded-0" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Danh mục cha</label>
              <div class="input-group">
                <select class="form-select rounded-0" v-model="parentId">
                  <option value="">-- Không --</option>
                  <option v-for="cat in allParents" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Làm danh mục mặc định</label>
              <div class="input-group">
                <select class="form-select rounded-0" v-model="isDefault">
                  <option :value="true">Có</option>
                  <option :value="false">Không</option>
                </select>
              </div>
            </div>
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
            <div class="mb-3 p-2 text-center" v-if="previewImage">
              <img :src="previewImage" class="w-50" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <button type="button" class="btn btn-danger1 themsp" @click="addCategory">
    + Thêm
  </button>
</template>

<script>
import axios from 'axios'
import Swal from 'sweetalert2'
import { ref, onMounted } from 'vue'

export default {
  setup() {
    const name = ref('')
    const parentId = ref('')
    const isDefault = ref(false)
    const image = ref(null)
    const previewImage = ref(null)
    const allParents = ref([])

    const fetchParents = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/admin/categories/parents', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        allParents.value = res.data
      } catch (err) {
        showToast('Lỗi khi tải danh mục cha!', 'error')
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

    const addCategory = async () => {
      if (!name.value.trim()) {
        showToast('Tên danh mục là bắt buộc!', 'error')
        return
      }

      const formData = new FormData()
      formData.append('name', name.value)
      if (parentId.value) formData.append('parent_id', parentId.value)
      if (image.value) formData.append('images', image.value)
      formData.append('default', isDefault.value ? 1 : 0)
      console.log(formData)

      try {
        await axios.post('http://127.0.0.1:8000/api/admin/categories', formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          }
        })

        Swal.fire({
          icon: 'success',
          title: 'Thành công',
          text: 'Thêm danh mục thành công!',
          toast: true,
          timer: 3000,
          position: 'top-end',
          showConfirmButton: false,
        })

      } catch (error) {
        console.log('Error:', error.response); // 🐞 thêm dòng này

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
            text: 'Không thể thêm danh mục.',
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
    })

    return {
      name, parentId, isDefault, image, previewImage, allParents,
      handleImageChange, addCategory
    }
  }
}
</script>

<style>
.themsp {
  width: 200px;
}
</style>
