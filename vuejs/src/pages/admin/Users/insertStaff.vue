<template>
  <div>
    <div v-if="isLoading" class="isLoading-overlay">
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">isLoading...</span>
      </div>
    </div>
    <div class="container py-4">
      <h2 class="mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill text-success fs-3"></i> Thêm nhân viên mới
      </h2>

      <div class="card shadow rounded-4 border-0">
        <div class="card-body p-4">
          <form @submit.prevent="submitForm" class="row g-4">
            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-person-fill text-primary me-2"></i>Username
              </label>
              <input type="text" class="form-control rounded" placeholder="Username tự động được tạo" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-card-text text-primary me-2"></i>Họ và tên
              </label>
              <input v-model="form.fullname" type="text" class="form-control rounded" placeholder="Họ và tên nhân viên"
                required>
              <div v-if="errors.fullname" class="text-danger small">{{ errors.fullname }}</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-envelope-fill text-primary me-2"></i>Email
              </label>
              <input type="email" class="form-control rounded" placeholder="Email sẽ tự động được tạo" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-lock-fill text-primary me-2"></i>Mật khẩu
              </label>
              <input type="password" class="form-control rounded" placeholder="Mật khẩu sẽ tự động được tạo" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">
                <i class="bi bi-telephone-fill text-primary me-2"></i>Số điện thoại
              </label>
              <input v-model="form.phone" type="text" class="form-control rounded"
                placeholder="Số điện thoại nhân viên">
              <div v-if="errors.phone" class="text-danger small">{{ errors.phone }}</div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
              <router-link to="/admin/employees" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
              </router-link>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle-fill"></i> Lưu nhân viên
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import axios from 'axios'
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isLoading = ref(false)



const form = reactive({
  fullname: '',
  phone: '',
})
const errors = reactive({
  fullname: '',
  phone: '',
})

const submitForm = async () => {
  isLoading.value = true
  errors.fullname = ''
  errors.phone = ''
  try {
    console.log('Dữ liệu gửi:', form)

    // Gọi API backend tại đây (axios.post...)
    await axios.post('http://127.0.0.1:8000/api/insert_staff', form)

    alert('Thêm nhân viên thành công!')

    router.push('/admin/users/list-employee')
  } catch (error) {
    if (error.response?.status === 422) {
      const responseErrors = error.response.data.errors
      errors.fullname = responseErrors.fullname?.[0] || ''
      errors.phone = responseErrors.phone?.[0] || ''
    } else {
      alert('Đã xảy ra lỗi k xác định')
    }
  } finally {
    isLoading.value = false
  }
}
</script>
<style>
.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
