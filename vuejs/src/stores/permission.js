import axios from 'axios';
import { ref, onMounted } from 'vue';
import { API_URL } from '@/config'
const _permissions = ref([]);
const _isLoadingPermissions = ref(false);

export const checkPermission = (permission) => {
  if (!permission) return true;
  return _permissions.value.includes(permission);
};

export function Permission(useridRef) {
  onMounted(async () => {
      _isLoadingPermissions.value = true;
      try {
        if (useridRef.value) {
          const response = await axios.get(`${API_URL}/role-permission-user/${useridRef.value}`);
            _permissions.value = response.data.permissions;
            // console.log("Quyền người dùng đã được tải từ API:", _permissions.value);
        } else {
          console.warn("Không có User ID được cung cấp để tải quyền.");
          _permissions.value = [];
        }
      } catch (error) {
        console.error('Lỗi khi tải quyền người dùng:', error);
        _permissions.value = [];
      } finally {
        _isLoadingPermissions.value = false;
      }
  });

  return {
    permissions: _permissions,
    hasPermission: checkPermission,
    isLoadingPermissions: _isLoadingPermissions,
  };
}
