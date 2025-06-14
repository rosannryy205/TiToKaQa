<template v-if="hasPermission('view_role')">
  <div class="d-flex justify-content-between mb-2">
    <h3>Danh sách vai trò</h3>
    <button type="button" class="btn btn-add" v-if="hasPermission('create_role')">+Thêm vai trò</button>
  </div>
  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap w-25">
    <span class="vd">Tìm kiếm vai trò</span>
    <v-select v-model="selectrole" :options="role" label="display_name" placeholder="Nhập vai trò..." :clearable="true"
      class="form-control rounded" />
  </div>

  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>STT</th>
          <th class="d-none d-sm-table-cell">Tên vai trò</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in filteredRoles" :key="item.id">
          <td>{{ index + 1 }}</td>
          <td><router-link :to="`/admin/users/list-role-detail/${item.id}`">{{ item.display_name }}</router-link></td>
          <td class="d-flex justify-content-center gap-2">
            <router-link :to="`/admin/users/list-role-edit/${item.id}`" class="btn btn-outline" v-if="hasPermission('edit_role')">Sửa</router-link>
            <button class="btn btn-danger-delete btn-sm" v-if="hasPermission('delete_role')">Xoá</button>
          </td>
        </tr>
      </tbody>
    </table>
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
      hasPermission
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

.custom-input,
.custom-select {
  border: 1px solid #bbb;
  padding: 2px 6px;
  height: 28px;
  font-size: 13px;
  border-radius: 4px;
  outline: none;
  box-shadow: none !important;
  transition: border-color 0.3s ease;
}

.custom-input:focus,
.custom-select:focus {
  border-color: #999;
  box-shadow: none;
}

/* Buttons */
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
  padding: 4px 10px;
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

/* Responsive */
.delete_mobile {
  display: none;
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
    margin-top: 1rem;
  }
}

/* Để làm cho dropdown menu giống Bootstrap card/dropdown */
.v-select .vs__dropdown-menu {
  border: 1px solid rgba(0, 0, 0, 0.15);
  /* Border của dropdown Bootstrap */
  border-radius: 0.25rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
  /* Shadow của Bootstrap dropdown */
}

/* Định kiểu cho các option trong dropdown */
.v-select .vs__dropdown-option {
  padding: 0.25rem 0.5rem;
  /* Padding giống dropdown item */
  font-size: 1rem;
}

.v-select .vs__dropdown-toggle {
  border: none;
  padding: 0;
}

/* Định kiểu cho option khi hover/active */
.v-select .vs__dropdown-option--highlight {
  background-color: #f8f9fa;
  /* Màu background khi hover/active */
  color: #c92c3c;
  font-weight: 500;
}
</style>
