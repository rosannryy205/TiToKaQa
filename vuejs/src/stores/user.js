import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { toast } from "vue3-toastify";
import { form } from '@/stores/userForm'
export const User = {
  setup() {
    const user = ref(null)    
  const loading = ref(true); // Controls loading spinner/state visibility
    const isLoggedIn = ref(false);

    const userLocal = JSON.parse(localStorage.getItem('user'))

    const personally = async (userId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/user/${userId}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          }
        })
    
        user.value = res.data
        form.fullname = res.data.fullname || res.data.username
        form.email = res.data.email
        form.phone = res.data.phone || ''
        form.address = res.data.address || ''
        form.username = res.data.username || ''
        form.avatar = res.data.avatar
          ? (res.data.avatar.startsWith('http')
            ? res.data.avatar
            : `http://127.0.0.1:8000/storage/${res.data.avatar}`)
          : null
        form.rank_points = res.data.rank_points
        form.usable_points = res.data.usable_points 
        form.rank = res.data.rank
        form.use_points = false
    
        tempAvatar.value = null
      } catch (error) {
        console.error('Không lấy được thông tin người dùng', error)
      }
    }
    

    const tempAvatar = ref(null);
    const avatarUrl = computed(() => {
      if (tempAvatar.value) {
        return tempAvatar.value;
      }
      return form.avatar;
    });
    const handleImageUpload = (event) => {
      const file = event.target.files[0];
      if (file) {
        form.avatar = file;
        tempAvatar.value = URL.createObjectURL(file);
      } else {
        form.avatar = null;
        tempAvatar.value = null;
      }
    };


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
        user.value = null;
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
        const formData = new FormData();
        formData.append("fullname", form.value.fullname || "");
        formData.append("phone", form.value.phone || "");
        formData.append("address", form.value.address || "");
        if (form.value.avatar instanceof File) {
          formData.append("avatar", form.value.avatar);
        }
        formData.append("_method", "PATCH");

        await axios.post(`http://127.0.0.1:8000/api/user/${user.value.id}`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          }
        });
        toast.success('Cập nhật thành công!')
        console.log(form.value)
        await personally(user.value.id)
      } catch (error) {
        console.error(error)
        toast.error('Cập nhật thất bại!')
      }
    };



    const getInitial = (fullname) => {
      if (!fullname) return '?'
      return fullname.trim().charAt(0).toUpperCase()
    }

    onMounted(() => {
      if (userLocal?.id) {
        personally(userLocal.id)
          .then(() => {
            isLoggedIn.value = !!user.value;
          })
          .finally(() => {
            loading.value = false
          })
        // console.log(form.value.avatar);

      } else {
        console.warn('Không tìm thấy user trong localStorage');
        isLoggedIn.value = false;
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
      avatarUrl
    }
  },
}
