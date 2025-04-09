<template>
  <!-- Loading Spinner -->
  <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div v-else-if="user" class="container mt-5 fade-in">
    <div class="row g-4">
      <!-- Avatar + Sidebar -->
      <div class="col-12 col-md-3">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="avatar-wrapper position-relative mx-auto mb-3">
            <template v-if="form && form.avatar">
              <img :src="form.avatar" alt="Avatar" class="rounded-circle avatar-img" />
            </template>
            <template v-else>
              <div class="avatar-placeholder d-flex justify-content-center align-items-center">
                {{ getInitial(form?.fullname) }}
              </div>
            </template>

            <!-- Hover overlay -->
            <div class="avatar-overlay d-flex justify-content-center align-items-center">
              <label class="btn btn-light btn-sm m-0 cursor-pointer">
                Đổi ảnh
                <input type="file" @change="handleImageUpload" hidden />
              </label>
            </div>
          </div>

          <h6 class="mt-2 mb-3">{{ form.fullname || 'Chưa có tên' }}</h6>

          <div class="list-group text-start">
            <router-link to="/update-user" class="text-decoration-none list-group-item list-group-item-action">
              <p class="mb-0 me-3">Thông tin tài khoản</p>
            </router-link>
            <router-link to="/history-oder" class="text-decoration-none list-group-item list-group-item-action">
              <p class="mb-0 me-3">Lịch sử đơn hàng</p>
            </router-link>
            <a href="#" class="list-group-item list-group-item-action text-danger" @click="handleLogout">Đăng xuất</a>
          </div>
        </div>
      </div>


      <!-- Container 2: Form -->
      <div class="col-12 col-md-9">
        <div class="card shadow border-0 p-4">
          <h4 class="mb-4 text-center" :style="{ color: primaryColor }">Thông tin tài khoản</h4>
          <div v-if="successMessage" class="alert alert-success mt-3">
            {{ successMessage }}
          </div>
          <form @submit.prevent="handleSubmit">
            <div class="mb-3">
              <label for="fullname" class="form-label">Họ và tên</label>
              <input type="text" v-model="form.fullname" class="form-control border-custom" id="fullname" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" v-model="form.email" class="form-control border-custom" id="email" required>
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label">Số điện thoại</label>
              <input type="text" v-model="form.phone" class="form-control border-custom" id="phone">
            </div>

            <div class="mb-3">
              <label for="address" class="form-label">Địa chỉ</label>
              <input type="text" v-model="form.address" class="form-control border-custom" id="address">
            </div>

            <!-- <div class="mb-3">
              <label for="status" class="form-label">Trạng thái</label>
              <select id="status" v-model="form.status" class="form-select border-custom">
                <option value="active">Đang hoạt động</option>
                <option value="blocked">Bị khoá</option>
              </select>
            </div> -->

            <button type="submit" class="btn text-white w-100" :style="{ backgroundColor: primaryColor }">
              Lưu thay đổi
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
  <!-- Nếu chưa đăng nhập -->
  <div v-else class="container d-flex align-items-center justify-content-center" style="min-height: 50vh;">
    <h5 class="mb-3">Vui lòng đăng nhập để xem thông tin tài khoản.</h5>
  </div>

</template>
<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'


export default {
  setup() {
    const successMessage = ref('')
    const primaryColor = '#ca111f';
    const user = ref(null)
    const form = ref({
      fullname: '',
      email: '',
      phone: '',
      address: '',
      avatar: ''
    })
    const user1 = JSON.parse(localStorage.getItem('user')) || null
    // const token = localStorage.getItem('token')

    const personally = async (userId) => {
      try {
        const res = await axios.get(`http://localhost:8000/api/user/${userId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          }
        })
        user.value = res.data
        form.value = {
          fullname: res.data.fullname,
          email: res.data.email,
          phone: res.data.phone || '',
          address: res.data.address || '',
          avatar: res.data.avatar || ''
        }
      } catch (error) {
        console.error('Không lấy được thông tin người dùng', error)
      }
    }

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        form.value.avatar = URL.createObjectURL(file)
      }
    }

    const isLoggedIn = ref(!!user.value);

    //  Đăng xuất
    const handleLogout = async () => {
      try {
        await axios.post('http://127.0.0.1:8000/api/logout', null, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });


        localStorage.removeItem('user');
        localStorage.removeItem('token');
        user1.value = null;
        isLoggedIn.value = false;

        alert('Đăng xuất thành công!');
        window.location.href = '/';
      } catch (error) {
        console.error('Lỗi đăng xuất:', error);
        alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
      }
    };


    const handleSubmit = async () => {
      try {
        await axios.patch(`http://localhost:8000/api/user/${user.value.id}`, form.value, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          }
        })
        successMessage.value = 'Cập nhật thành công!'
      } catch (error) {
        console.error(error)
        alert('Cập nhật thất bại.')
      }
    }

    const getInitial = (fullname) => {
      if (!fullname) return '?'
      return fullname.trim().charAt(0).toUpperCase()
    }
    const loading = ref(true);

    onMounted(() => {
      if (user1 && user1.id) {
        personally(user1.id).then(() => {
          isLoggedIn.value = !!user.value;
        })
          .finally(() => {
            loading.value = false
          })
      } else {
        console.warn('Không tìm thấy user trong localStorage');
        isLoggedIn.value = false;
      }
    })
    return {
      form,
      user,
      successMessage,
      handleSubmit,
      handleImageUpload,
      handleLogout,
      getInitial,
      loading,
      primaryColor
    }
  },
}
</script>
<style scoped>
.border-custom {
  border: 1px solid #ca111f;
}

.avatar-wrapper {
  width: 150px;
  height: 150px;
  position: relative;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  border: 3px solid #ca111f;
}

.avatar-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s ease;
  cursor: pointer;
}

.avatar-wrapper:hover .avatar-overlay {
  opacity: 1;
}

.avatar-placeholder {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-color: #e0e0e0;
  color: #ca111f;
  font-size: 48px;
  font-weight: bold;
  border: 3px solid #ca111f;
}
.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

</style>
