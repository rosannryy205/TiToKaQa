import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { form } from '@/stores/userForm'
import Swal from 'sweetalert2'
import { API_URL } from '@/config'
export const User = {
  setup() {
    const user = ref(null)
    const loading = ref(true);
    const isLoggedIn = ref(false);

    const userLocal = JSON.parse(localStorage.getItem('user'))

    const personally = async (userId) => {
      try {
        const res = await axios.get(`${API_URL}/user/${userId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })

        user.value = res.data
        form.fullname = res.data.fullname || res.data.username
        form.email = res.data.email
        form.phone = res.data.phone || ''
        form.address = res.data.address || ''
        form.username = res.data.username || ''
        form.avatar = res.data.avatar || null
        form.rank_points = res.data.rank_points
        form.usable_points = res.data.usable_points
        form.rank = res.data.rank
        form.use_points = false

        tempAvatar.value = null
        console.log(res.data.avatar)
      } catch (error) {
        console.error('Không lấy được thông tin người dùng', error)
      }
    }


    const tempAvatar = ref(null)
    const avatarUrl = computed(() => {
      if (tempAvatar.value) return tempAvatar.value;
      if (!form.avatar) return '/default-avatar.png';
      if (form.avatar.startsWith('http://') || form.avatar.startsWith('https://')) {
        return form.avatar;
      }
      return `http://127.0.0.1:8000/storage/${form.avatar}`;
    });
    const handleImageUpload = async (event) => {
      const file = event.target.files[0];

      if (!file) {
        form.avatar = null;
        tempAvatar.value = null;
        return;
      }

      form.avatar = file;
      tempAvatar.value = URL.createObjectURL(file);

      const formData = new FormData();
      formData.append("avatar", form.avatar);

      try {
        const response = await axios.post(`${API_URL}/user/${user.value.id}/upload-avatar`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            // 'Content-Type': 'multipart/form-data'
          }
        });
        form.avatar = response.data.avatar_url;
        tempAvatar.value = null;

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Ảnh đại diện đã được cập nhật thành công!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
        personally(user.value.id);

      } catch (error) {
        console.error('Lỗi khi cập nhật ảnh đại diện:', error);
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Lỗi khi cập nhật ảnh đại diện!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
      };
    }

    //  Đăng xuất
    const handleLogout = async () => {
      const result = await Swal.fire({
        title: 'Bạn có muốn đăng xuất?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
      });
      if (result.isConfirmed) {
        try {
          await axios.post(`${API_URL}/logout`, null, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          });
          localStorage.removeItem('user')
          localStorage.removeItem('token')
          user.value = null
          isLoggedIn.value = false
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Đăng xuất thành công!',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
          })
          window.location.href = '/';
        } catch (error) {
          console.error('Lỗi đăng xuất:', error);
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
          })
        }
      }
    }

    const handleSubmit = async () => {
      try {
        const updateProfile = {
          fullname: form.fullname || '',
          phone: form.phone || '',
          address: form.address || '',
        }
        await axios.patch(`${API_URL}/user/updateProfile/${user.value.id}`, updateProfile, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          }
        });
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Cập nhật thông tin thành công!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        })
        console.log(updateProfile)
        personally(user.value.id)
      } catch (error) {
        console.error(error)
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors

          if (errors.phone) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: errors.phone[0], // Sẽ là "Số điện thoại đã được sử dụng."
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true,
            })
            return
          }
        }
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Cập nhật thông tin thất bại!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        })
      }
    }

    const getInitial = (fullname) => {
      if (!fullname) return '?'
      return fullname.trim().charAt(0).toUpperCase()
    }

    onMounted(() => {
      if (userLocal?.id) {
        personally(userLocal.id)
          .then(() => {
            isLoggedIn.value = !!user.value
          })
          .finally(() => {
            loading.value = false
          })
        // console.log(form.value.avatar);
      } else {
        console.warn('Không tìm thấy user trong localStorage')
        isLoggedIn.value = false
      }
    })
    return {
      form,
      user,
      handleSubmit,
      handleImageUpload,
      handleLogout,
      getInitial,
      loading,
      avatarUrl,
    }
  },
}
