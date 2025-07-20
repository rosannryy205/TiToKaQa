<template>
  <div class="row">
    <div class="col-xl-12 order-xl-1">
      <div class="d-flex justify-content-between">
        <h3 class="text-danger fw-bold" style="z-index: 9 !important">Thêm danh mục</h3>
        <div style="z-index: 9 !important">
          <router-link to="categories" class="btn btn-outline-secondary rounded-0" >
            <i class="bi bi-arrow-counterclockwise"></i> Quay lại
          </router-link>
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
                    <select class="form-select rounded-0" v-model="parentId" :disabled="isDefault == 1">
                      <option value="">-- Không --</option>
                      <option v-for="cat in allParents" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Đặt làm danh mục mặc định</label>
                  <div class="input-group">
                    <select class="form-select rounded-0" v-model="isDefault">
                      <option :value="1">Có</option>
                      <option :value="0">Không</option>
                    </select>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Loại danh mục <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <select class="form-select rounded-0" v-model="categoryType">
                      <option value="food">Món ăn</option>
                      <option value="topping">Topping</option>
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

      <button type="button" class="btn btn-danger-save" @click="addCategory">
        Thêm danh mục
      </button>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Swal from 'sweetalert2'
import { ref, onMounted, watch } from 'vue'

export default {
  setup() {
    const name = ref('')
    const parentId = ref('')
    const isDefault = ref(0)
    const image = ref(null)
    const previewImage = ref(null)
    const allParents = ref([])
    const categoryType = ref('food')


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
      formData.append('type', categoryType.value)

      try {
        await axios.post('http://127.0.0.1:8000/api/admin/categories', formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          }
        })

        showToast('Thêm danh mục thành công!')

      } catch (error) {
        console.log('Error:', error.response);

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

    watch(isDefault, (newVal) => {
      if (parseInt(newVal) === 1) {
        parentId.value = ''
      }
    })


    onMounted(() => {
      fetchParents()
    })

    return {
      name,
      parentId,
      isDefault,
      image,
      previewImage,
      allParents,
      categoryType,
      handleImageChange,
      addCategory,
    }
  }
}
</script>

<style>
.themsp {
  width: 200px;
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
</style>
