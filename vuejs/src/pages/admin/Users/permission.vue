<template>
  <div v-if="hasPermission('view_role')">
    <h3>Danh sách quyền</h3>

    <div
      class="d-flex flex-column flex-md-row justify-content-between mb-3 align-items-start align-items-md-center w-75 gap-2">
      <router-link :to="'/admin/users/list-role-insert'" class="btn btn-add pt-2 pe-3 ps-3 w-25 w-md-25 text-center"
        v-if="hasPermission('create_role')">
        + Thêm quyền
      </router-link>
      <v-select v-model="selectpermission" :options="permission" label="name" placeholder="Nhập tên quyền..."
        :clearable="true" class="form-control rounded border-0 flex-grow-1" style="height: 45px;" />
      <select class="form-select rounded" v-model="itemsPerPageUserSelected" @change="currentPage = 1"
        style="max-width: 109px;">
        <option value="10">Hiển thị</option>
        <option value="5">5</option>
        <option value="15">15</option>
        <option value="20">20</option>
      </select>
    </div>

    <div class="table-responsive d-none d-lg-block">
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th style="width: 50px;">STT</th>
            <th>Tên quyền</th>
            <th>Tuỳ chọn</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginatedPermissions" :key="item.id">
            <td>{{ index + 1 + (currentPage - 1) * itemsPerPageUserSelected }}</td>
            <td>{{ item.name }}</td>
            <td class="d-flex justify-content-center gap-2">
              <router-link :to="`/admin/users/list-role-edit/${item.id}`" class="btn btn-outline"
                v-if="hasPermission('edit_role')">Sửa</router-link>
              <button class="btn btn-danger-delete btn-sm" v-if="hasPermission('delete_role')"
                @click="deletePermission(item.id)">
                Xoá
              </button>
            </td>
          </tr>
          <tr v-if="paginatedPermissions.length === 0">
            <td colspan="3" class="text-center">Không có quyền nào được tìm thấy.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="d-lg-none mt-3">
      <div class="card mb-2" v-for="(item, index) in paginatedPermissions" :key="item.id">
        <div class="card-body">
          <h5 class="card-title mb-1">
            {{ item.name }}
          </h5>
          <p class="card-text mb-2 text-muted">STT: {{ index + 1 + (currentPage - 1) * itemsPerPageUserSelected }}</p>
          <div class="d-flex gap-2">
            <router-link :to="`/admin/users/list-role-edit/${item.id}`" class="btn btn-outline btn-sm"
              v-if="hasPermission('edit_role')">Sửa</router-link>
            <button class="btn btn-danger-delete btn-sm" v-if="hasPermission('delete_role')">
              Xoá
            </button>
          </div>
        </div>
      </div>
      <div v-if="paginatedPermissions.length === 0" class="text-center mt-3">
        Không có quyền nào được tìm thấy.
      </div>
    </div>

    <nav v-if="totalPages > 1" class="mt-3 d-flex justify-content-center">
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">«</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
          <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">»</a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import { onMounted, ref, computed, watch } from 'vue'
import axios from 'axios'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import { Permission } from '@/stores/permission'
import Swal from 'sweetalert2';

export default {
  components: {
    'v-select': vSelect,
  },
  setup() {
    const permission = ref([])
    const selectpermission = ref(null)

    const currentPage = ref(1);
    const itemsPerPageUserSelected = ref(10);
    const getAllPermission = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/permission')
        permission.value = res.data
      } catch (error) {
        console.log(error);
      }
    }

    const filteredPermissions = computed(() => {
      if (!selectpermission.value) {
        return permission.value;
      }
      return permission.value.filter(p => p.id === selectpermission.value.id);
    });

    const paginatedPermissions = computed(() => {
      const limit = Number(itemsPerPageUserSelected.value) || 1;
      const start = (currentPage.value - 1) * limit;
      const end = start + limit;
      return filteredPermissions.value.slice(start, end);
    })

    const totalPages = computed(() => {
      if (itemsPerPageUserSelected.value === 0) return 0;
      return Math.ceil(filteredPermissions.value.length / itemsPerPageUserSelected.value);
    });

    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
      }
    };

    watch(totalPages, (newTotalPages) => {
      if (currentPage.value > newTotalPages && newTotalPages > 0) {
        currentPage.value = newTotalPages;
      } else if (newTotalPages === 0) {
        currentPage.value = 1;
      }
    });

    const deletePermission = async (id) => {
      const result = Swal.fire({
        title: 'Bạn có muốn xoá quyền này?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
      })
      if (result.isConfirmed) {
        return
      }
      try {
        await axios.delete(`http://127.0.0.1:8000/api/permission/${id}`)
        await getAllPermission()
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Xoá thành công',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      } catch (error) {
        console.log(error);
      }
    }

    const userId = ref(null)
    const userString = localStorage.getItem('user')
    if (userString) {
      const user = JSON.parse(userString)
      if (user && user.id !== undefined) {
        userId.value = user.id
      }
    }
    const { hasPermission, permissions } = Permission(userId)

    onMounted(() => {
      getAllPermission()
    })

    return {
      permission,
      selectpermission,
      userId,
      hasPermission,
      currentPage,
      itemsPerPageUserSelected,
      paginatedPermissions,
      totalPages,
      goToPage,
      deletePermission
    }
  },
}
</script>


<style scoped>
.title {
  font-weight: normal;
  margin-bottom: 1rem;
  font-size: 1.5rem;
  color: #333;
}

.btn-outline {
  background: none;
  border: 1px solid #ccc;
  padding: 4px 10px;
  border-radius: 4px;
  color: #555;
  font-weight: normal;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-outline:hover {
  background-color: #eee;
  color: #333;
}

.btn-add {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-add:hover {
  background-color: #c92c3c;
  color: #fff;
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

.btn-danger-filled {
  background-color: #c92c3c;
  color: white;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-danger-filled:hover {
  background-color: #a51928;
}

@media (max-width: 576px) {
  .btn {
    font-size: 0.875rem;
    padding: 6px 10px;
  }

  .v-select {
    font-size: 0.9rem;
  }

  .card-title {
    font-size: 1rem;
  }
}

.v-select {
  flex-grow: 1;
}
</style>
