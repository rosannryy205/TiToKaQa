import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import Swal from 'sweetalert2'

export const User = {
  setup() {
    const user = ref(null)
    const form = ref({
      fullname: '',
      email: '',
      phone: '',
      address: '',
      avatar: null,
      username: '',
    })
    const loading = ref(true) // Controls loading spinner/state visibility
    const isLoggedIn = ref(false)

    const userLocal = JSON.parse(localStorage.getItem('user'))

    const personally = async (userId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/user/${userId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        user.value = res.data
        ;(form.value = {
          fullname: res.data.fullname || res.data.username,
          email: res.data.email,
          phone: res.data.phone || '',
          address: res.data.address || '',
          username: res.data.username || '',
          avatar: res.data.avatar
            ? res.data.avatar.startsWith('http')
              ? res.data.avatar
              : `http://127.0.0.1:8000/storage/${res.data.avatar}`
            : null,
          rank_points: res.data.rank_points,
          usable_points: res.data.usable_points,
          rank: res.data.rank,
        }),
          (tempAvatar.value = null)
      } catch (error) {
        console.error('Không lấy được thông tin người dùng', error)
      }
    }

    const tempAvatar = ref(null)
    const avatarUrl = computed(() => {
      if (tempAvatar.value) {
        return tempAvatar.value
      }
      return form.value.avatar
    })

    const handleImageUpload = async (event) => {
      const file = event.target.files[0]

      if (!file) {
        form.value.avatar = null
        tempAvatar.value = null
        return
      }

      form.value.avatar = file
      tempAvatar.value = URL.createObjectURL(file)

      const formData = new FormData()
      formData.append('avatar', form.value.avatar)

      try {
        const response = await axios.post(
          `http://127.0.0.1:8000/api/user/${user.value.id}/upload-avatar`,
          formData,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
              // 'Content-Type': 'multipart/form-data'
            },
          },
        )
        form.value.avatar = response.data.avatar_url
        tempAvatar.value = null

        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Ảnh đại diện đã được cập nhật thành công!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        })
        personally(user.value.id)
      } catch (error) {
        console.error('Lỗi khi cập nhật ảnh đại diện:', error)
        Swal.fire({
          toast: false,
          position: 'top-end',
          icon: 'error',
          title: 'Lỗi khi cập nhật ảnh đại diện!',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        })
      }
    }

    //  Đăng xuất
    const handleLogout = async () => {
      try {
        await axios.post('http://127.0.0.1:8000/api/logout', null, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })

        localStorage.removeItem('user')
        localStorage.removeItem('token')
        user.value = null
        isLoggedIn.value = false

        alert('Đăng xuất thành công!')
        window.location.href = '/'
      } catch (error) {
        console.error('Lỗi đăng xuất:', error)
        alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!')
      }
    }

    const handleSubmit = async () => {
      try {
        const updateProfile = {
          fullname: form.value.fullname || '',
          phone: form.value.phone || '',
          address: form.value.address || '',
        }
        await axios.patch(
          `http://127.0.0.1:8000/api/user/updateProfile/${user.value.id}`,
          updateProfile,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          },
        )
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
        Swal.fire({
          toast: false,
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
