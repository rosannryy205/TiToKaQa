import { ref, onMounted } from 'vue'
import axios from 'axios'

export const User = {
  setup() {
    const successMessage = ref('')
    const primaryColor = '#ca111f';
    const user = ref(null)
    const form = ref({
      fullname: '',
      email: '',
      phone: '',
      address: '',
      avatar: '',
      username: ''
    })
    const user1 = JSON.parse(localStorage.getItem('user')) || null
    // const token = localStorage.getItem('token')

    const personally = async (userId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/user/${userId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          }
        })
        user.value = res.data
        form.value = {
          fullname: res.data.fullname || res.data.username,
          email: res.data.email,
          phone: res.data.phone || '',
          address: res.data.address || '',
          avatar: res.data.avatar || '',
          username: res.data.username || ''
        }
      } catch (error) {
        console.error('Không lấy được thông tin người dùng', error)
      }
    }

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        form.value.avatar = URL.createObjectURL(file)
      }
    }

    const isLoggedIn = ref(!!user.value);

    //  Đăng xuất
    const handleLogout = async () => {
      try {
        await axios.post('http://127.0.0.1:8000/api/logout', null, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });


        localStorage.removeItem('user');
        localStorage.removeItem('token');
        user1.value = null;
        isLoggedIn.value = false;

        alert('Đăng xuất thành công!');
        window.location.href = '/';
      } catch (error) {
        console.error('Lỗi đăng xuất:', error);
        alert('Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!');
      }
    };


    const handleSubmit = async () => {
      try {
        if(localStorage.getItem('token')){
          await axios.patch(`http://localhost:8000/api/user/${user.value.id}`, form.value, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            }
          })
          successMessage.value = 'Cập nhật thành công!'
        }

      } catch (error) {
        console.error(error)
        alert('Cập nhật thất bại.')
      }
    }

    const getInitial = (fullname) => {
      if (!fullname) return '?'
      return fullname.trim().charAt(0).toUpperCase()
    }
    const loading = ref(true);

    onMounted(() => {
      if (user1 && user1.id) {
        personally(user1.id).then(() => {
          isLoggedIn.value = !!user.value;
        })
          .finally(() => {
            loading.value = false
          })
      } else {
        console.warn('Không tìm thấy user trong localStorage');
        isLoggedIn.value = false;
      }
    })
    return {
      form,
      user,
      successMessage,
      handleSubmit,
      handleImageUpload,
      handleLogout,
      getInitial,
      loading,
      primaryColor
    }
  },
}
