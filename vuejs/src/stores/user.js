import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export const User = {
  setup() {
    const user = ref(null);
    const form = ref({
      fullname: "",
      email: "",
      phone: "",
      address: "",
      avatar: null,
      username: "",
    });
    const token = localStorage.getItem("token");
    if (!token) {
      toast.error("Bạn chưa đăng nhập.");
      return (window.location.href = "/login");
    }
    const personally = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/user`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        const data = res.data;
        const currentUserData = data.user || (Array.isArray(data) ? data[0] : data);
        if (currentUserData) {
          user.value = currentUserData;
          form.value.fullname = currentUserData.fullname || currentUserData.username || "";
          form.value.email = currentUserData.email || "";
          form.value.phone = currentUserData.phone || "";
          form.value.address = currentUserData.address || "";
          form.value.username = currentUserData.username || "";

          let avatarPath = data.avatar_url || currentUserData.avatar;

          if (avatarPath) {
            form.value.avatar = (avatarPath.startsWith('http://') || avatarPath.startsWith('https://'))
              ? avatarPath
              : `http://127.0.0.1:8000/storage/${avatarPath}`;
          } else {
            form.value.avatar = null;
          }
        } else {
          console.warn('Không thể phân tích dữ liệu người dùng từ phản hồi:', data);
          toast.error("Định dạng dữ liệu người dùng không hợp lệ khi tải trang.");
        }

        tempAvatar.value = null;
      } catch (error) {
        console.error("Không lấy được thông tin người dùng", error);
        toast.error("Không thể tải thông tin người dùng.");
      } finally {
        loading.value = false;
      }
    };

    const tempAvatar = ref(null);
    const avatarUrl = computed(() => {
      if (tempAvatar.value) {
        return tempAvatar.value;
      }
      return form.value.avatar;
    });
    const handleImageUpload = (event) => {
      const file = event.target.files[0];
      if (file) {
        form.value.avatar = file;
        tempAvatar.value = URL.createObjectURL(file);
      } else {
        form.value.avatar = null;
        tempAvatar.value = null;
      }
    };

    const isLoggedIn = ref(!!user.value);

    //  Đăng xuất
    const handleLogout = async () => {
      try {
        await axios.post("http://127.0.0.1:8000/api/logout", null, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        localStorage.removeItem("user");
        localStorage.removeItem("token");

        isLoggedIn.value = false;

        toast.success('Đăng xuất thành công!');
        window.location.href = "/";
      } catch (error) {
        console.error("Lỗi đăng xuất:", error);
        alert("Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!");
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

        await axios.post(`http://127.0.0.1:8000/api/user`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'multipart/form-data',
          }
        });
        toast.success('Cập nhật thành công!')
        console.log(form.value)
        await personally()
      } catch (error) {
        console.error(error)
        toast.error('Cập nhật thất bại!')
      }
    };

    const getInitial = (username) => {
      if (username?.trim()) return username.trim().charAt(0).toUpperCase();
      return "?";
    };

    const loading = ref(true);

    onMounted(() => {
      personally();
    });

    return {
      form,
      user,
      handleSubmit,
      handleImageUpload,
      handleLogout,
      getInitial,
      loading,
      avatarUrl
    };
  },
}
