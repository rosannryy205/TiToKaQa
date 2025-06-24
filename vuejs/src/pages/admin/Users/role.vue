<template v-if="hasPermission('view_role')">
  <h3>Danh sách vai trò</h3>

  <div class="d-flex flex-column flex-md-row justify-content-between mb-3 align-items-start align-items-md-center w-50">
    <router-link :to="'/admin/users/list-role-insert'" class="btn btn-add pt-2 pe-3 ps-3 w-25 w-md-25 text-center"
      v-if="hasPermission('create_role')">
      + Thêm vai trò
    </router-link>
    <v-select v-model="selectrole" :options="role" label="display_name" placeholder="Tìm kiếm vai trò..."
      :clearable="true" class="form-control rounded border-0 w-25" style="height: 45px;" />
  </div>

  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th style="width: 50px;">STT</th>
          <th>Tên vai trò</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in filteredRoles" :key="item.id">
          <td>{{ index + 1 }}</td>
          <td><router-link :to="`/admin/users/list-role-detail/${item.id}`">{{ item.display_name }}</router-link></td>
          <td class="d-flex justify-content-center gap-2">
            <router-link :to="`/admin/users/list-role-edit/${item.id}`" class="btn btn-outline"
              v-if="hasPermission('edit_role')">Sửa</router-link>
            <button class="btn btn-danger-delete btn-sm" v-if="hasPermission('delete_role')">
              Xoá
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="d-lg-none mt-3">
    <div class="card mb-2" v-for="(item, index) in filteredRoles" :key="item.id">
      <div class="card-body">
        <h5 class="card-title mb-1">
          <router-link :to="`/admin/users/list-role-detail/${item.id}`">{{ item.display_name }}</router-link>
        </h5>
        <p class="card-text mb-2 text-muted">STT: {{ index + 1 }}</p>
        <div class="d-flex gap-2">
          <router-link :to="`/admin/users/list-role-edit/${item.id}`" class="btn btn-outline btn-sm"
            v-if="hasPermission('edit_role')">Sửa</router-link>
          <button class="btn btn-danger-delete btn-sm" v-if="hasPermission('delete_role')">
            Xoá
          </button>
        </div>
      </div>
    </div>
    <p v-if="filteredRoles.length === 0" class="text-center text-muted">Không tìm thấy vai trò nào.</p>
  </div>

</template>

<script>
import { onMounted, ref, computed } from 'vue'
import axios from 'axios'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import { Permission } from '@/stores/permission'

export default {
  components: {
    'v-select': vSelect,
  },
  setup() {
    const role = ref([])
    const selectrole = ref(null)

    const getAllRole = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/role')
        role.value = res.data
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


    const filteredRoles = computed(() => {
      if (!selectrole.value) {
        return role.value;
      }
      return role.value.filter(item => item.id === selectrole.value.id);
    });

    onMounted(() => {
      getAllRole()
    })

    return {
      role,
      selectrole,
      filteredRoles,
      userId,
      hasPermission,
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

/* Xóa các quy tắc responsive cũ không cần thiết hoặc gây xung đột */
/* @media (max-width: 768px) {
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
    margin-top: 1rem;
  }
} */

/* Custom styles for v-select to better fit its container */
.v-select {
  flex-grow: 1;
  /* Cho phép v-select co giãn */
}
</style>
