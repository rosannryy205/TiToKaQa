<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>
              {{ isEmployee ? 'Danh sách nhân viên' : 'Danh sách khách hàng' }}
            </h2>
            <router-link to="/admin/insert_staff">
              <button v-if="isEmployee" class="btn btn-insert">
                <i class="bi bi-person-plus-fill"></i> Thêm nhân viên
              </button>
            </router-link>
          </div>


          <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <span class="vd">Tìm kiếm </span>
            <input type="text" class="form-control rounded" style="max-width: 250px"
              placeholder="Tìm kiếm theo tên hoặc SĐT" />
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
                <tr v-for="user in filteredUsersComputed" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.username }}</td>
                  <td>{{ user.fullname ? user.fullname : 'Chưa cập nhật' }}</td>
                  <td>{{ user.phone ? user.phone : 'Chưa cập nhật' }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.address ? user.address : 'Chưa cập nhật' }}</td>
                  <td>
                    <!-- Nếu là khách hàng thì chỉ hiển thị text -->
                    <span v-if="user.current_role === 'khachhang'">
                      {{ getRoleName(user.current_role) }}
                    </span>

                    <!-- Các role khác thì được phép đổi role -->
                    <select v-else class="form-select rounded" v-model="user.current_role"
                      @change="updateUserRole(user)">
                      <option v-for="item in role" :key="item.id" :value="item.name">
                        {{ getRoleName(item.name) }}
                      </option>
                    </select>
                  </td>


                  <td>
                    {{ user.status }}
                  </td>
                  <td class="d-flex justify-content-center gap-2">
                    <button v-if="!isEmployee" class="btn btn-info" @click="openUserModal(user)" data-bs-toggle="modal"
                      data-bs-target="#userDetailModal">
                      Chi tiết
                    </button>

                    <button @click="toggleStatus(user)" v-if="user.status === 'Active'"
                      class="btn btn-danger-delete">Khoá</button>
                    <button @click="toggleStatus(user)" v-else="user.status==='Block'" class="btn btn-primary">Mở
                      Khóa</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="isEmployee">
              <p>*Mật khẩu là (username)Titokaqa <br>
                VD: staff1Titokaqa
              </p>
            </div>
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
                  <div class="card-body" v-for="user in allUser" :key="user.id">
                    <h5 class="card-title">{{ user.fullname }}</h5>
                    <p class="card-text"><strong>SĐT:</strong>{{ user.phone }}</p>
                    <p class="card-text"><strong>Email:</strong>{{ user.email }}</p>
                    <p class="card-text">
                      <strong>Vai trò: </strong>
                      <!-- Nếu là khách hàng thì chỉ hiển thị -->
                      <span v-if="user.current_role === 'khachhang'">
                        {{ getRoleName(user.current_role) }}
                      </span>

                      <!-- Các role khác thì có select -->
                      <select v-else v-model="user.current_role" @change="updateUserRole(user)">
                        <option v-for="r in role" :key="r.id" :value="r.name">
                          {{ getRoleName(r.name) }}
                        </option>
                      </select>
                    </p>

                    <button v-if="!isEmployee" class="btn btn-info" @click="openUserModal(user)" data-bs-toggle="modal"
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
          <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel"
            aria-hidden="true">
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
        </div>
      </div>
    </div>
  </div>



  <!-- <button class="btn btn-danger-delete delete_mobile">Xoá</button> -->
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import { watch } from 'vue'
import { toast } from 'vue3-toastify'
import { API_URL } from '@/config'
const selectedUser = ref(null);

const openUserModal = (user) => {
  selectedUser.value = user;
};
const allUser = ref([])
const route = useRoute()
const isEmployee = computed(() => {
  return route.name && String(route.name).includes('employee')
})
const fecthAllUser = async () => {
  try {
    const response = await axios.get(`${API_URL}/user`);
    const usersData = response.data.user;

    const result = [];

    for (const item of usersData) {
      const user = item.user;
      const roles = item.roles;

      const isCustomer = roles.includes('khachhang');
      const isEmployeeUser = roles.some(role =>
        ['quanly', 'nhanvien', 'nhanvienkho', 'nguoigiaohang'].includes(role)
      );

      if ((isEmployee.value && isEmployeeUser) || (!isEmployee.value && isCustomer)) {
        // gắn role hiện tại (đơn giản lấy role đầu tiên)
        user.current_role = user.roles.length > 0 ? user.roles[0].name : null;

        if (isCustomer) {
          const orders = user.orders || [];
          const success_orders = orders.filter(o => o.order_status === 'Giao thành công').length;
          const failed_orders = orders.filter(o => o.order_status === 'Giao thất bại').length;
          const canceled_orders = orders.filter(o => o.order_status === 'Đã hủy').length;

          user.success_orders = success_orders;
          user.failed_orders = failed_orders;
          user.canceled_orders = canceled_orders;

          if (failed_orders >= 5 && user.status !== 'Block') {
            try {
              await axios.put(`${API_URL}/update/${user.id}`, { status: 'Block' });
              user.status = 'Block';
            } catch (error) {
              console.error(`Lỗi khi block user ${user.id}`, error);
            }
          }
        }

        result.push(user);
      }
    }
    allUser.value = result;
  } catch (error) {
    console.log('Lỗi khi fetch user:', error);
  }
};


const getRoleName = (role) => {
  const roleMap = {
    khachhang: 'Khách hàng',
    quanly: 'Quản lý',
    nhanvien: 'Nhân viên',
    nhanvienkho: 'Nhân viên kho',
  }
  return roleMap[role] || role
}


const toggleStatus = async (user) => {
  const newStatus = user.status === 'Active' ? 'Block' : 'Active'
  try {
    await axios.put(`${API_URL}/update/${user.id}`, {
      status: newStatus,
    })
    user.status = newStatus
  } catch (error) {
    console.log('Error:', error)
    alert('Không thể cập nhật người dùng')
  }
}

const role = ref([])
const getAllRole = async () => {
  const res = await axios.get(`${API_URL}/role`)
  role.value = res.data
}

const updateUserRole = async (user) => {
  try {
    await axios.post(`${API_URL}/assign-role/${user.id}`, {
      role: [user.current_role],
    });
    // đồng bộ lại vào user.roles[0]
    if (user.roles.length > 0) {
      user.roles[0].name = user.current_role;
    }
    toast.success('Cập nhật vai trò thành công');
  } catch (error) {
    console.error(error);
    toast.error('Lỗi');
  }
};


const userString = JSON.parse(localStorage.getItem('user'));

const user_id = ref(userString.id);
// console.log(user_id);


const user_role = ref(userString.role)
// console.log(user_role);

const filteredUsersComputed = computed(() => {
  if (!user_id.value) {
    return allUser.value;
  }
  return allUser.value.filter(user => user.id !== user_id.value);
});

watch(route, async (newRoute, oldRoute) => {
  if (
    (newRoute.name && String(newRoute.name).includes('employee')) ||
    (newRoute.name && String(newRoute.name).includes('customer'))
  ) {
    await fecthAllUser();
    await getAllRole();
  }
}, {
  deep: true,
  immediate: true
});
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

.btn-insert {
  background-color: #C92C3C;
  color: white;
}

.btn-insert:hover {
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
