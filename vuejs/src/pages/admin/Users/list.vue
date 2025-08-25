<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>
              {{ isEmployee ? 'Danh sách nhân viên' : 'Danh sách khách hàng' }}
            </h2>
            <router-link to="/admin/insert_staff" v-if="hasPermission('create_employee')">
              <button v-if="isEmployee" class="btn btn-insert">
                <i class="bi bi-person-plus-fill"></i> Thêm nhân viên
              </button>
            </router-link>
          </div>


          <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
              <input v-model="searchTerm" type="text" class="form-control rounded" style="max-width: 300px"
                placeholder="Tìm kiếm" />
              <button class="search-btn" @click="handleSearch">
                <i class="bi bi-search"></i>
              </button>



            </div>


            <span class="vd">Hiển thị</span>
            <select v-model.number="pagination.pageSize" class="form-select w-auto rounded">
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="15">15</option>
              <option :value="30">30</option>
              <option :value="60">60</option>
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
                <tr v-for="user in paginatedUsers" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.username }}</td>
                  <td>{{ user.fullname ? user.fullname : 'Chưa cập nhật' }}</td>
                  <td>{{ user.phone ? user.phone : 'Chưa cập nhật' }}</td>
                  <td>{{ user.email }}</td>
                  <td>{{ user.address ? user.address : 'Chưa cập nhật' }}</td>
                  <td>{{ getRoleName(user.roles) }}</td>
                  <td>
                    {{ user.status === "Active" ? 'Hoạt động' : 'Hạn chế' }}
                  </td>
                  <td class="d-flex justify-content-center gap-2">
                    <button v-if="!isEmployee" class="btn btn-info" @click="openUserModal(user)" data-bs-toggle="modal"
                      data-bs-target="#userDetailModal">
                      Chi tiết
                    </button>

                    <button @click="toggleStatus(user)"
                      v-if="user.status === 'Active' && hasPermission('edit_employee')" class="btn btn-danger-delete">
                      Khoá
                    </button>

                    <button @click="toggleStatus(user)"
                      v-else-if="user.status === 'Block' && hasPermission('edit_employee')" class="btn btn-primary">
                      Mở Khóa
                    </button>

                  </td>
                </tr>
              </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
              <ul class="pagination">
                <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
                  <a class="page-link" href="#" @click.prevent="changePage(pagination.currentPage - 1)">
                    <!-- <i class="bi bi-chevron-left"></i> -->«
                  </a>
                </li>

                <li class="page-item" v-for="page in visiblePages" :key="page"
                  :class="{ active: pagination.currentPage === page }">
                  <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                </li>

                <li class="page-item" :class="{ disabled: pagination.currentPage === totalPages }">
                  <a class="page-link" href="#" @click.prevent="changePage(pagination.currentPage + 1)">
                    <!-- <i class="bi bi-chevron-right"></i> -->»
                  </a>
                </li>
              </ul>
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
                  <div class="card-body" v-for="user in paginatedUsers" :key="user.id">
                    <h5 class="card-title">{{ user.fullname }}</h5>
                    <p class="card-text"><strong>SĐT:</strong>{{ user.phone }}</p>
                    <p class="card-text"><strong>Email:</strong>{{ user.email }}</p>
                    <p class="card-text"><strong>Vai trò: </strong>
                      <span>{{ getRoleName(user.roles) }}</span>
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



          <!-- <button class="btn btn-danger-delete delete_mobile">Xoá</button> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useMenu } from '@/stores/use-menu'
import { ref } from 'vue'
import axios from 'axios'
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import { watch } from 'vue'
import Swal from 'sweetalert2';
import { Permission } from '@/stores/permission'

useMenu().onSelectedKeys(['admin-roles'])
<<<<<<< HEAD
=======
const API_URL = "http://127.0.0.1:8000/api"
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)
const selectedUser = ref(null);

const pagination = ref({
  currentPage: 1,
  pageSize: 5
});

const allUser = ref([])
const route = useRoute()
const searchTerm = ref('');

const openUserModal = (user) => {
  selectedUser.value = user;
};

const handleSearch = () => {
  console.log("Tìm kiếm với:", searchTerm.value);
  fetchUsers();
};

const filteredUsers = computed(() => {
  if (!searchTerm.value) return allUser.value;
  const keyword = searchTerm.value.toLowerCase();
  return allUser.value.filter(user =>
    (user.fullname || '').toLowerCase().includes(keyword) ||
    (user.username || '').toLowerCase().includes(keyword) ||
    (user.phone || '').toLowerCase().includes(keyword)
  );
});

const isEmployee = computed(() => {
  return route.name && String(route.name).includes('employee')
})

const fecthAllUser = async () => {
  try {
<<<<<<< HEAD
    const response = await axios.get(`http://127.0.0.1:8000/api/user`);
=======
    const response = await axios.get(`${API_URL}/user`);
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
    const usersData = response.data.user;

    const result = [];

    for (const item of usersData) {
      const user = item.user;
      const roles = item.roles;

      const isCustomer = roles.includes('khachhang');
      const isEmployeeUser = roles.some(role => ['quanly', 'nhanvien', 'nhanvienkho'].includes(role));

      if ((isEmployee.value && isEmployeeUser) || (!isEmployee.value && isCustomer)) {

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
<<<<<<< HEAD
              await axios.put(`http://127.0.0.1:8000/api/update/${user.id}`, {
=======
              await axios.put(`${API_URL}/update/${user.id}`, {
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
                status: 'Block'
              });
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

const getRoleName = (roles) => {
  if (!roles || roles.length === 0) return 'Chưa phân quyền'

  const map = {
    khachhang: 'Khách hàng',
    quanly: 'Quản lý',
    nhanvien: 'Nhân viên',
    nhanvienkho: 'Nhân viên kho',
  }

  // Nếu roles là mảng object, lấy ra role.name
  const roleNames = roles.map(role => typeof role === 'object' ? role.name : role)

  return roleNames.map(role => map[role] || role).join(', ')
}

const toggleStatus = async (user) => {
  const newStatus = user.status === 'Active' ? 'Block' : 'Active'
  try {
<<<<<<< HEAD
    await axios.put(`http://127.0.0.1:8000/api/update/${user.id}`, {
=======
    await axios.put(`${API_URL}/update/${user.id}`, {
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
      status: newStatus,
    })
    user.status = newStatus
  } catch (error) {
    console.log('Error:', error)
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Không thể cập nhật người dùng',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });
  }
}

const paginatedUsers = computed(() => {
  const start = (pagination.value.currentPage - 1) * pagination.value.pageSize;
  const end = start + pagination.value.pageSize;
  return filteredUsers.value.slice(start, end);
});


const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / pagination.value.pageSize);
});

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    pagination.value.currentPage = page;
  }
};

const visiblePages = computed(() => {
  const maxVisible = 5;
  const total = totalPages.value;
  const current = pagination.value.currentPage;
  let start = Math.max(1, current - Math.floor(maxVisible / 2));
  let end = Math.min(total, start + maxVisible - 1);

  if (end - start < maxVisible - 1) {
    start = Math.max(1, end - maxVisible + 1);
  }

  const pages = [];
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});


watch(route, async (newRoute, oldRoute) => {
  if (
    (newRoute.name && String(newRoute.name).includes('employee')) ||
    (newRoute.name && String(newRoute.name).includes('customer'))
  ) {
    await fecthAllUser();
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

/* .pagination .page-link {
  color: #C92C3C;
  border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
  background-color: #C92C3C;
  color: white;
  border-color: #C92C3C;
} */

/* .pagination .page-link:hover {
  background-color: #f8d7da;
  color: #C92C3C;
} */

/* .pagination {
  margin-bottom: 0;
} */

.search-btn {
  background: linear-gradient(135deg, #007bff, #0056b3);
  color: white;
  font-size: 14px;
  padding: 6px 16px;
  border: none;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.25s ease-in-out;
  cursor: pointer;
  height: 40px;
}

.search-btn:hover {
  background: linear-gradient(135deg, #0056b3, #004080);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
  transform: translateY(-1px);
}

.search-btn:active {
  transform: scale(0.98);
}
</style>
