<template>
  <h2 class="mb-3">Quản lý người dùng</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <button type="button" class="btn btn-danger1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
      +Thêm người dùng
    </button>
    <span class="vd">Tìm kiếm</span>
    <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm" />
    <span class="vd">Lọc theo vai trò</span>
    <select class="form-select w-auto rounded" style="max-width: 250px" v-model="selectRole">
      <option value="">Tất cả</option>
      <option value="admin">Admin</option>
      <option value="staff">Nhân viên</option>
      <option value="user">Khách hàng</option>
    </select>

    <span class="vd">Hiển thị</span>
    <select class="form-select w-auto rounded">
      <option selected>5</option>
      <option>10</option>
      <option>15</option>
    </select>
  </div>

  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Mã KH</th>
          <th>Username</th>
          <th>Họ và tên</th>
          <th>Số điện thoại</th>
          <th>Email</th>
          <th>Địa chỉ</th>
          <th>Vai trò</th>
          <th>Trạng thái</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in fillterUsers" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.username }}</td>
          <td>{{ user.fullname }}</td>
          <td>{{ user.phone }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.address }}</td>
          <td>
            <select class="form-select">
              <option :selected="user.role === 'admin'">Admin</option>
              <option :selected="user.role === 'staff'">Nhân viên</option>
              <option :selected="user.role === 'user'">Khách hàng</option>
            </select>
          </td>
          <td>
            {{ user.status }}
          </td>
          <td class="d-flex justify-content-center gap-2">
            <button @click="toggleStatus(user)" v-if="user.status === 'Active'"
              class="btn btn-danger-delete">Khoá</button>
            <button @click="toggleStatus(user)" v-else="user.status==='Block'" class="btn btn-primary">Mở Khóa</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <button class="btn btn-danger-delete delete_desktop">Xoá</button>

  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox" />
          1
        </div>
        <div class="col-9">
          <div class="card-body" v-for="user in fillterUsers" :key="user.id">
            <h5 class="card-title">{{ user.fullname }}</h5>
            <p class="card-text"><strong>SĐT:</strong>{{ user.phone }}</p>
            <p class="card-text"><strong>Email:</strong>{{ user.email }}</p>
            <p class="card-text"><strong>Vai trò: </strong>
              <select class="form-select w-auto">
                <option :selected="user.role === 'admin'">Admin</option>
                <option :selected="user.role === 'staff'">Nhân viên</option>
                <option :selected="user.role === 'user'">Khách hàng</option>
              </select>
            </p>
            <button @click="toggleStatus(user)" v-if="user.status === 'Active'"
              class="btn btn-danger-delete">Khoá</button>
            <button @click="toggleStatus(user)" v-else="user.status==='Block'" class="btn btn-primary">Mở Khóa</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button class="btn btn-danger-delete delete_mobile">Xoá</button>
</template>

<script setup>
import { useMenu } from '@/stores/use-menu'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { computed } from 'vue'

useMenu().onSelectedKeys(['admin-roles'])

const allUser = ref([])
const selectRole = ref('')

const fecthAllUser = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/user`)
    allUser.value = response.data
  } catch (error) {
    console.log('Lỗi kìa mày', error)
  }
}

const toggleStatus = async (user) => {
  const newStatus = user.status === 'Active' ? 'Block' : 'Active'
  try {
    await axios.put(`http://127.0.0.1:8000/api/update/${user.id}`, {
      status: newStatus,
    })
    user.status = newStatus
  } catch (error) {
    console.log('Error:', error)
    alert('Không thể cập nhật người dùng')
  }
}

const fillterUsers = computed(()=>{
  if(!selectRole.value) return allUser.value
  return allUser.value.filter(user => user.role === selectRole.value)
})

onMounted(() => {
  fecthAllUser()
})
</script>


<style scoped>
.delete_mobile {
  display: none;
}

.btn-danger-delete {
  background-color: #C92C3C;
  color: white;
}

.btn-danger-delete:hover {
  background-color: #a51928;
  color: white;
}

@media (max-width: 768px) {
  .table-responsive {
    display: none;
  }

  .vd {
    display: none;
  }

  .delete_desktop {
    display: none;
  }

  .delete_mobile {
    display: block;
  }
}
</style>
