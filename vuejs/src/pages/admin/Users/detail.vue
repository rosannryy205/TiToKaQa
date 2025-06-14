<template>
  <div class="container mt-5">
    <div class="permission-manager-wrapper">
      <h3 v-if="!isedit">Chi tiết vai trò</h3>
      <h3 v-else>Chỉnh sửa quyền vai trò</h3>

      <div class="d-flex justify-content-end mb-3" v-if="isedit && hasPermission('edit_role')">
        <button @click="savePermissions" class="btn btn-outline-success me-2" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          <span v-else>Lưu Quyền</span>
        </button>
        <button @click="resetPermissions" class="btn btn-outline-warning me-2" :disabled="loading || !originalData">
          Đặt lại
        </button>
        <button @click="$router.back()" class="btn btn-outline-dark" :disabled="loading || !originalData">
          Quay về
        </button>
      </div>
      <div class="d-flex justify-content-end mb-3" v-else>
        <button @click="$router.back()" class="btn btn-outline-dark" :disabled="loading || !originalData">
          Quay về
        </button>
      </div>
      <div class="table-responsive mt-3 permission-manager">
        <table class="table table-borderless table-hover">
          <thead class="table-light">
            <tr>
              <th>Quyền</th>
              <th>Xem</th>
              <th>Thêm</th>
              <th>Sửa</th>
              <th>Xoá</th>
              <th>Toàn quyền</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center">
                <div class="d-flex justify-content-center align-items-center py-4">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                  </div>
                </div>
              </td>
            </tr>
            <template v-else>
              <tr v-for="(moduleDisplayName, moduleKey) in moduleMap" :key="moduleKey">
                <td>{{ moduleDisplayName }}</td>
                <td>
                  <div class="checkbox-container">
                    <input class="checkbox-input" type="checkbox" :id="`view-${moduleKey}-${currentRoleId}`"
                      v-model="currentRoleAbilities[moduleKey].view" @change="handleAbilityChange(moduleKey)" :disabled="!isedit || !hasPermission('edit_role')" />
                    <label class="checkbox" :for="`view-${moduleKey}-${currentRoleId}`"> <span
                        class="line line1"></span>
                      <span class="line line2"></span>
                    </label>
                  </div>
                </td>
                <td v-if="moduleKey !== 'dashboard'">
                  <div class="checkbox-container">
                    <input class="checkbox-input" type="checkbox" :id="`create-${moduleKey}-${currentRoleId}`"
                      v-model="currentRoleAbilities[moduleKey].create" @change="handleAbilityChange(moduleKey)" :disabled="!isedit || !hasPermission('edit_role')" />
                    <label class="checkbox" :for="`create-${moduleKey}-${currentRoleId}`"> <span
                        class="line line1"></span>
                      <span class="line line2"></span>
                    </label>
                  </div>
                </td>
                <td v-else></td>

                <td v-if="moduleKey !== 'dashboard' && moduleKey !== 'order'">
                  <div class="checkbox-container">
                    <input class="checkbox-input" type="checkbox" :id="`edit-${moduleKey}-${currentRoleId}`"
                      v-model="currentRoleAbilities[moduleKey].edit" @change="handleAbilityChange(moduleKey)" :disabled="!isedit || !hasPermission('edit_role')" />
                    <label class="checkbox" :for="`edit-${moduleKey}-${currentRoleId}`"> <span
                        class="line line1"></span>
                      <span class="line line2"></span>
                    </label>
                  </div>
                </td>
                <td v-else></td>

                <td
                  v-if="moduleKey !== 'dashboard' && moduleKey !== 'order' && moduleKey !== 'booking' && moduleKey !== 'employee' && moduleKey !== 'customer'">
                  <div class="checkbox-container">
                    <input class="checkbox-input" type="checkbox" :id="`delete-${moduleKey}-${currentRoleId}`"
                      v-model="currentRoleAbilities[moduleKey].delete" @change="handleAbilityChange(moduleKey)" :disabled="!isedit || !hasPermission('edit_role')" />
                    <label class="checkbox" :for="`delete-${moduleKey}-${currentRoleId}`"> <span
                        class="line line1"></span>
                      <span class="line line2"></span>
                    </label>
                  </div>
                </td>
                <td v-else></td>
                <td>
                  <div class="checkbox-container">
                    <input class="checkbox-input" type="checkbox" :id="`all-${moduleKey}-${currentRoleId}`"
                      v-model="currentRoleAbilities[moduleKey].all" @change="toggleAllAbilities(moduleKey)" :disabled="!isedit || !hasPermission('edit_role')" />
                    <label class="checkbox" :for="`all-${moduleKey}-${currentRoleId}`"> <span class="line line1"></span>
                      <span class="line line2"></span>
                    </label>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { onMounted } from 'vue';
import { computed } from 'vue';
import { ref, watch, nextTick } from 'vue';
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify';
import { Permission } from '@/stores/permission'

export default {
  name: 'RolePermissionManager',
  setup() {
    const currentRoleId = ref(null);
    const currentRoleAbilities = ref({});
    const originalData = ref(null);
    const loading = ref(false);
    const statusMessage = ref('');
    const statusType = ref('');
    const route = useRoute();
    const isedit = computed(() => {
    return route.name && String(route.name).includes('edit')
  })
  const userId = ref(null)
    const userString = localStorage.getItem('user')
    if (userString) {
      const user = JSON.parse(userString)
      if (user && user.id !== undefined) {
        userId.value = user.id
      }
    }
      const { hasPermission, permissions } = Permission(userId)

    //key -> tên hiển thị
    const moduleMap = {
      'dashboard': 'Thống kê',
      'category': 'Danh mục',
      'food': 'Món ăn',
      'topping': 'Topping',
      'combo': 'Combo',
      'order': 'Đơn hàng',
      'table': 'Bàn',
      'booking': 'Lịch đặt bàn',
      'role': 'Vai trò',
      'employee': 'Nhân viên',
      'customer': 'Khách hàng',
    };

    const actionKeys = ['view', 'create', 'edit', 'delete'];

    // tạo cấu trúc quyền mặc định cho từng module
    const createAbilitiesStructure = (abilitiesData = null) => {
      const newAbilities = {};
      for (const moduleKey in moduleMap) {
        newAbilities[moduleKey] = {
          view: false,
          create: false,
          edit: false,
          delete: false,
          all: false
        };
      }

      //ghi đè các quyền lấy từ api lên các khung quyền tạo sẵn
      if (abilitiesData) {
        for (const moduleKey in abilitiesData) {
          if (newAbilities[moduleKey]) {
            newAbilities[moduleKey] = { ...newAbilities[moduleKey], ...abilitiesData[moduleKey] };
          }
        }
      }
      return newAbilities;
    };


    const fetchPermissions = async (id) => {
      loading.value = true;

      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/role-permission/${id}`);
        const roleData = response.data.data;
        const fetchedAbilities = createAbilitiesStructure(roleData.abilities);

        //quyền gốc dùng để làm chức năng reset
        originalData.value = JSON.parse(JSON.stringify(fetchedAbilities));

        // quyền hiện tại dùng để render và cập nhật khác với quyền gốc
        currentRoleAbilities.value = JSON.parse(JSON.stringify(fetchedAbilities));

        // trạng thái toàn quyền cho từng module với các điều kiện đối với các module khác nhau
        for (const moduleKey in moduleMap) {
          handleAbilityChange(moduleKey);
        }

        // toast.success('Tải dử liệu thành công')
      } catch (error) {
        console.log('Lỗi khi lấy dữ liệu quyền:', error);
        toast.error('Lỗi khi lấy dữ liệu quyền')
      } finally {
        loading.value = false;
      }
    };

    // hàm để cập nhật trạng thái 'all' cho các quyền
    const handleAbilityChange = (moduleKey) => {
      if (currentRoleAbilities.value && currentRoleAbilities.value[moduleKey]) {
        const abilities = currentRoleAbilities.value[moduleKey];
        let allChecked = true;

        // nếu như quyền là thống kê thì tick vào ô xem là xem như bật toàn quyền
        if (moduleKey === 'dashboard') {
          allChecked = abilities.view;

          // nếu như quyền là đơn hiện thời hoặc là lịch đặt bàn thì tick vào ô xem và thêm là xem như bật toàn quyền
        } else if (moduleKey === 'current_order' || moduleKey === 'booking') {
          allChecked = abilities.view && abilities.create;

          //các quyền còn lại có đầy đủ crud
        } else {
          allChecked = abilities.view && abilities.create && abilities.edit && abilities.delete;
        }

        nextTick(() => {
          abilities.all = allChecked;
        });
      }
    };

    // click checkbox "toàn quyền", bật/tắt tất cả các quyền con
    const toggleAllAbilities = (moduleKey) => {
      if (currentRoleAbilities.value && currentRoleAbilities.value[moduleKey]) {
        const abilities = currentRoleAbilities.value[moduleKey];
        const isChecked = abilities.all;

        abilities.view = isChecked;

        // nếu quyền thống kê bật checkbox view xem như là toàn quyền
        if (moduleKey === 'dashboard') {
          abilities.view = isChecked;

          // nếu quyền đơn hiện thời và đặt bàn bật checkbox create xem như là toàn quyền
        } else if (moduleKey === 'current_order' || moduleKey === 'booking') {
          abilities.create = isChecked;

          //các quyền còn lại phải tắt hết
        } else {
          abilities.create = isChecked;
          abilities.edit = isChecked;
          abilities.delete = isChecked;
        }
      }
    };


    const savePermissions = async () => {
      loading.value = true;
      try {
        const permissionsToSave = [];
        for (const moduleKey in moduleMap) {
          const abilities = currentRoleAbilities.value[moduleKey];
          for (const actionKey of actionKeys) {
            if (abilities[actionKey]) {
              permissionsToSave.push(`${actionKey}_${moduleKey}`);
            }
          }
        }

        const payload = {
          role_id: currentRoleId.value,
          permissions: permissionsToSave,
        };

        await axios.put('http://127.0.0.1:8000/api/role-permission-update', payload);

        localStorage.setItem('toastMessage', 'Lưu quyền thành công!');
        window.location.reload();

        await fetchPermissions(currentRoleId.value);
      } catch (error) {
        console.error('Lỗi khi lưu quyền:', error);
        toast.error('Lỗi khi lưu quyền');
      } finally {
        loading.value = false;
      }
    };

    const resetPermissions = () => {
      if (confirm('Bạn có chắc chắn muốn đặt lại tất cả quyền về trạng thái ban đầu (dựa trên dữ liệu tải lần cuối)?')) {
        if (originalData.value) {
          currentRoleAbilities.value = JSON.parse(JSON.stringify(originalData.value));
          for (const moduleKey in moduleMap) {
            handleAbilityChange(moduleKey);
          }
          toast.success('Đã đặt lại quyền về trạng thái ban đầu.');
        } else {
          toast.success('Không có gì thay đôir.');
        }
      }
    };

    watch(() => route.params.id, (newId) => {
      if (newId) {
        currentRoleId.value = newId;
        fetchPermissions(newId);
      } else {
        currentRoleId.value = null;
        currentRoleAbilities.value = createAbilitiesStructure();
        originalData.value = null;
      }
    }, { immediate: true });


    onMounted(() => {
      const message = localStorage.getItem('toastMessage');
      if (message) {
        toast.success(message);
        localStorage.removeItem('toastMessage');
      }
    });

    return {
      currentRoleId,
      currentRoleAbilities,
      loading,
      statusMessage,
      statusType,
      moduleMap,
      fetchPermissions,
      handleAbilityChange,
      toggleAllAbilities,
      savePermissions,
      resetPermissions,
      originalData,
      isedit,
      hasPermission,
      userId
    };
  },
};
</script>

<style scoped>
/* Giữ nguyên CSS của bạn */
.permission-manager-wrapper {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.permission-manager {
  min-width: 100%;
}

table th,
table td {
  text-align: center;
  vertical-align: middle;
}

table th:first-child,
table td:first-child {
  text-align: start;
}


/* Checkbox Container (Giữ nguyên) */
.checkbox-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Hidden Checkbox Input (Giữ nguyên) */
.checkbox-input {
  display: none;
}

/* Checkbox Label */
.checkbox {
  position: relative;
  width: 22px;
  /* Giảm từ 28px xuống 22px */
  height: 22px;
  /* Giảm từ 28px xuống 22px */
  background: linear-gradient(145deg, #f0f0f0, #cacaca);
  border-radius: 5px;
  /* Điều chỉnh bo góc cho phù hợp với kích thước nhỏ hơn */
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  box-shadow:
    4px 4px 8px rgba(0, 0, 0, 0.08),
    /* Điều chỉnh đổ bóng */
    -4px -4px 8px rgba(255, 255, 255, 0.6),
    /* Điều chỉnh đổ bóng */
    inset -1px -1px 2px rgba(255, 255, 255, 0.4),
    /* Điều chỉnh đổ bóng */
    inset 1px 1px 2px rgba(0, 0, 0, 0.08);
  /* Điều chỉnh đổ bóng */
}

/* Khi hover vào checkbox */
.checkbox:hover {
  background: linear-gradient(145deg, #e0e0e0, #d0d0d0);
  box-shadow:
    2px 2px 4px rgba(0, 0, 0, 0.06),
    -2px -2px 4px rgba(255, 255, 255, 0.5),
    inset -0.5px -0.5px 1px rgba(255, 255, 255, 0.3),
    inset 0.5px 0.5px 1px rgba(0, 0, 0, 0.06);
}

/* X Mark Lines */
.line {
  position: absolute;
  width: 13px;
  /* Giảm từ 16px xuống 13px */
  height: 2px;
  /* Giảm từ 2.5px xuống 2px */
  background: #707070;
  border-radius: 1px;
  /* Điều chỉnh bo góc */
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* X Mark Line 1 */
.line1 {
  transform: rotate(45deg);
}

/* X Mark Line 2 */
.line2 {
  transform: rotate(-45deg);
}

/* Checked State */
.checkbox-input:checked+.checkbox {
  background: linear-gradient(145deg, #81c784, #4caf50);
  box-shadow:
    2px 2px 4px rgba(0, 0, 0, 0.1),
    /* Điều chỉnh đổ bóng */
    -2px -2px 4px rgba(255, 255, 255, 0.3),
    inset -1px -1px 2px rgba(255, 255, 255, 0.2),
    inset 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.checkbox-input:checked+.checkbox .line1 {
  /* Điều chỉnh giá trị transform để dấu tích nằm đúng vị trí */
  transform: rotate(50deg) translateX(-0.5px) translateY(3px) scaleX(0.6);
  background: #ffffff;
}

.checkbox-input:checked+.checkbox .line2 {
  /* Điều chỉnh giá trị transform */
  transform: rotate(-50deg) translateX(1.5px) translateY(1.5px) scaleX(0.6);
  background: #ffffff;
}

.status-message {
  margin-top: 15px;
  padding: 10px 15px;
  border-radius: 5px;
  font-weight: bold;
  text-align: center;
}

.status-message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.status-message.info {
  background-color: #d1ecf1;
  color: #0c5460;
  border: 1px solid #bee5eb;
}

.status-message.warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}
</style>
