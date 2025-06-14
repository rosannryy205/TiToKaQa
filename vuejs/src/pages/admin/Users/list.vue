<template>
  <h2 class="mb-3">Quản lý người dùng</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <span class="vd">Tìm kiếm </span>
    <input type="text" class="form-control rounded" style="max-width: 250px" placeholder="Tìm kiếm theo tên hoặc SĐT" />
    <!-- <span class="vd">Lọc theo vai trò</span>
    <select class="form-select w-auto rounded" style="max-width: 250px" v-model="selectRole">
      <option value="">Tất cả</option>
      <option value="admin">Admin</option>
      <option value="staff">Nhân viên</option>
      <option value="user">Khách hàng</option>
    </select> -->

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
          <td>{{ user.fullname ? user.fullname : 'Chưa cập nhật' }}</td>
          <td>{{ user.phone ? user.phone : 'Chưa cập nhật' }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.address ? user.address : 'Chưa cập nhật' }}</td>
          <td>{{ user.role === 'user' ? 'Khách hàng' : '' }}</td>
          <td>
            {{ user.status }}
          </td>
          <td class="d-flex justify-content-center gap-2">
            <button class="btn btn-info" @click="openUserModal(user)" data-bs-toggle="modal"
              data-bs-target="#userDetailModal">
              Chi tiết
            </button>
            <button @click="toggleStatus(user)" v-if="user.status === 'Active'"
              class="btn btn-danger-delete">Khoá</button>
            <button @click="toggleStatus(user)" v-else="user.status==='Block'" class="btn btn-primary">Mở Khóa</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!-- <button class="btn btn-danger-delete delete_desktop">Xoá</button> -->

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
              <{{ user.role === 'user' ? 'Khách hàng' : '' }} </p>
                <button class="btn btn-info" @click="openUserModal(user)" data-bs-toggle="modal"
                  data-bs-target="#userDetailModal">
                  Chi tiết
                </button>
                <button @click="toggleStatus(user)" v-if="user.status === 'Active'"
                  class="btn btn-danger-delete">Khoá</button>
                <button @click="toggleStatus(user)" v-else="user.status==='Block'" class="btn btn-primary">Mở
                  Khóa</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Chi tiết người dùng -->
  <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <div class="modal-header text-white" style="background-color: #C92C3C;">
          <h5 class="modal-title d-flex align-items-center gap-2" id="userDetailModalLabel">
            <i class="bi bi-person-circle fs-4"></i> Thông tin người dùng
          </h5>
        </div>

        <div class="modal-body">
          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <i class="bi bi-person-fill text-primary"></i>
              <strong> Username:</strong> {{ selectedUser?.username || 'Chưa cập nhật' }}
            </div>
            <div class="col-md-6 mb-2">
              <i class="bi bi-card-text text-primary"></i>
              <strong> Họ và tên:</strong> {{ selectedUser?.fullname || 'Chưa cập nhật' }}
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <i class="bi bi-telephone-fill text-primary"></i>
              <strong> Số điện thoại:</strong> {{ selectedUser?.phone || 'Chưa cập nhật' }}
            </div>
            <div class="col-md-6 mb-2">
              <i class="bi bi-envelope-fill text-primary"></i>
              <strong> Email:</strong> {{ selectedUser?.email || 'Chưa cập nhật' }}
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-12 mb-2">
              <i class="bi bi-geo-alt-fill text-primary"></i>
              <strong> Địa chỉ:</strong> {{ selectedUser?.address || 'Chưa cập nhật' }}
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-md-6 mb-2">
              <i class="bi bi-person-badge-fill text-primary"></i>
              <strong> Vai trò:</strong>
              {{ selectedUser?.role === 'user' ? 'Khách hàng' : selectedUser?.role }}
            </div>
            <div class="col-md-6 mb-2">
              <i class="bi bi-shield-check text-primary"></i>
              <strong> Trạng thái:</strong> {{ selectedUser?.status }}
            </div>
          </div>

          <hr />

          <div class="row text-center">
            <div class="col-md-4 mb-2">
              <i class="bi bi-check-circle-fill text-success"></i><br />
              <strong>Đơn thành công:</strong><br />
              {{ selectedUser?.success_orders || 0 }}
            </div>
            <div class="col-md-4 mb-2">
              <i class="bi bi-x-circle-fill text-danger"></i><br />
              <strong>Đơn thất bại:</strong><br />
              {{ selectedUser?.failed_orders || 0 }}
            </div>
            <div class="col-md-4 mb-2">
              <i class="bi bi-slash-circle-fill text-warning"></i><br />
              <strong>Đơn đã huỷ:</strong><br />
              {{ selectedUser?.canceled_orders || 0 }}
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Đóng
          </button>
        </div>
      </div>
    </div>
  </div>



  <!-- <button class="btn btn-danger-delete delete_mobile">Xoá</button> -->
</template>

<script setup>
import { useMenu } from '@/stores/use-menu'
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { computed } from 'vue'
useMenu().onSelectedKeys(['admin-roles'])

const selectedUser = ref(null);

const openUserModal = (user) => {
  selectedUser.value = user;
};
const allUser = ref([])
const selectRole = ref('')

const fecthAllUser = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/user`);
    const users = response.data.user;

    for (const user of users) {
      if (user.role === 'user') {
        const orders = user.orders || [];

        const success_orders = orders.filter(o => o.order_status === 'Giao thành công').length;
        const failed_orders = orders.filter(o => o.order_status === 'Giao thất bại').length;
        const canceled_orders = orders.filter(o => o.order_status === 'Đã hủy').length;

        user.success_orders = success_orders;
        user.failed_orders = failed_orders;
        user.canceled_orders = canceled_orders;

        if (failed_orders >= 5 && user.status !== 'Block') {
          try {
            await axios.put(`http://127.0.0.1:8000/api/update/${user.id}`, {
              status: 'Block'
            });
            user.status = 'Block';
          } catch (error) {
            console.error(`Lỗi khi block user ${user.id}`, error);
          }
        }
      }
    }

    allUser.value = users.filter((u) => u.role === 'user');
  } catch (error) {
    console.log('Lỗi kìa mày', error);
  }
};


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

const fillterUsers = computed(() => {
  if (!selectRole.value) return allUser.value
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
