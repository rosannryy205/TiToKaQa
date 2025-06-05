<template>
  <div
    v-if="loading"
    class="d-flex justify-content-center align-items-center"
    style="min-height: 50vh"
  >
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div v-else class="container mt-5 fade-in">
    <div class="row g-4">
      <!-- Sidebar -->
      <div class="col-12 col-md-4 col-lg-3 mb-4 mb-md-0" style="max-height: 300px">
        <div class="card shadow border-0 h-100 text-center py-4 px-3">
          <div class="d-flex align-items-center mb-3 mx-3">
            <template v-if="form.avatar_preview || form.avatar">
              <img
                :src="
                  form.avatar_preview ||
                  (form.avatar?.startsWith('http')
                    ? form.avatar
                    : `http://localhost:8000/assets/avatar/${form.avatar}`)
                "
                alt="Avatar"
                class="avatar-circle d-flex justify-content-center align-items-center"
              />
            </template>
            <template v-else>
              <div
                class="avatar-circle border-custom d-flex justify-content-center align-items-center"
              >
                {{ getInitial(form?.fullname) || getInitial(form?.username) }}
              </div>
            </template>

            <div class="ms-4 text-center text-md-start">
              <h6 class="mt-2 mb-3 fw-bold">{{ form.fullname || form.username }}</h6>
              <a
                href="#"
                @click="handleLogout"
                class="list-group-item-action link-danger small d-flex align-items-center gap-1 mt-2"
              >
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
              </a>
            </div>
          </div>

          <!-- <div class="bg-light rounded-3 p-3 text-center mb-3">
            <div class="fw-bold">POP MART MEMBER</div>
            <div class="d-flex justify-content-around mt-2">
              <div>
                <div class="fw-bold fs-5">50</div>
                <div class="text-muted small">Điểm</div>
              </div>
              <div>
                <div class="fw-bold fs-5">0</div>
                <div class="text-muted small">Coupons</div>
              </div>
            </div>
            <button class="btn btn-outline-dark btn-sm mt-3 rounded-pill px-4">Rewards</button>
          </div> -->

          <ul class="list-group list-group-flush">
            <router-link to="/update-user" class="text-decoration-none text-dark">
              <li
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div>
                  <div class="fw-bold text-danger">Thông tin tài khoản</div>
                  <div class="small text-muted">Cập nhật thông tin</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>

            <router-link to="/infor-user" class="text-decoration-none text-dark">
              <li
                class="list-group-item d-flex justify-content-between align-items-center"
              >
                <div>
                  <div class="fw-bold">Quản lý đơn hàng</div>
                  <div class="small text-muted">Đơn hàng của tôi</div>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
              </li>
            </router-link>
          </ul>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-12 col-md-8 col-lg-9">
        <h4 class="fw-bold mb-4">Quản lý tài khoản</h4>

        <div class="card shadow-lg p-4 mx-auto">
          <div class="row">
            <!-- Cột trái -->
            <div class="col-md-7 mb-5 mb-md-0">
              <form @submit.prevent="handleSubmit">
                <div class="mb-3">
                  <label class="form-label">Tên người dùng</label>
                  <input
                    type="text"
                    v-model="form.fullname"
                    class="form-control form-control-lg rounded"
                    placeholder="Nhập nickname của bạn"
                    id="fullname"
                  />
                </div>

                <div class="mb-3 text-center">
                  <label class="form-label d-block">Ảnh đại diện</label>
                  <label
                    for="upload-profile"
                    class="border border-2 rounded-3 d-inline-block p-4 text-muted"
                    style="cursor: pointer"
                  >
                    <div class="fs-1 mb-2">+</div>
                    <div class="fw-medium">Tải lên</div>
                  </label>
                  <input
                    type="file"
                    id="upload-profile"
                    class="d-none"
                    @change="handleImageUpload"
                  />
                </div>

                <div class="mb-3">
                  <label for="phone" class="form-label">Số điện thoại</label>
                  <div class="input-group">
                    <span class="input-group-text">+84</span>
                    <input
                      type="text"
                      v-model="form.phone"
                      class="form-control form-control-lg rounded"
                      id="phone"
                      placeholder="Nhập số điện thoại của bạn"
                    />
                  </div>
                </div>

                <div class="mb-3">
                  <label for="address" class="form-label">Địa chỉ</label>
                  <input
                    type="text"
                    v-model="form.address"
                    class="form-control form-control-lg rounded"
                    id="address"
                    placeholder="Nhập địa chỉ của bạn"
                  />
                </div>
                <div class="text-center">
                  <button
                    type="submit"
                    style="background-color: #ca111f"
                    class="btn text-white w-100"
                  >
                    Lưu tài khoản
                  </button>
                </div>
              </form>
            </div>

            <!-- Cột phải -->
            <div class="col-md-5 border-start ps-md-4">
              <ul class="p-0 m-0 list-unstyled">
                <li
                  class="p-3 border rounded d-flex justify-content-between align-items-center mb-3"
                >
                  <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-envelope"></i>
                    <div>
                      <div class="fw-bold">Địa chỉ email</div>
                      <div class="small text-muted">Thay đổi địa chỉ email</div>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger w-100"
                    style="max-width: 100px"
                  >
                    <strong>Cập nhật</strong>
                  </button>
                </li>
                <li
                  class="p-3 border rounded d-flex justify-content-between align-items-center mb-3"
                >
                  <div class="d-flex align-items-center gap-3">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      fill="currentColor"
                      class="bi bi-lock"
                      viewBox="0 0 16 16"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4M4.5 7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7zM8 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3"
                      />
                    </svg>
                    <div>
                      <div class="fw-bold">Đổi mật khẩu</div>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger w-100"
                    style="max-width: 100px"
                  >
                    <strong>Cập nhật</strong>
                  </button>
                </li>
                <li
                  class="p-3 border rounded d-flex justify-content-between align-items-center mb-3"
                >
                  <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-trash"></i>
                    <div>
                      <div class="fw-bold">Xóa tài khoản</div>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-danger w-100"
                    style="max-width: 100px"
                  >
                    <strong>Xóa</strong>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, onMounted } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import { Image } from "ant-design-vue";

export default {
  setup() {
    const primaryColor = "#ca111f";
    const user = ref(null);
    const form = ref({
      fullname: "",
      email: "",
      phone: "",
      address: "",
      avatar: "",
      username: "",
      avatar_file: null,
      avatar_preview: "",
    });

    const personally = async (userId) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/user`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
        user.value = res.data;
        // console.log(res.data.username);

        form.value = {
          fullname: res.data.fullname || "",
          email: res.data.email || "",
          phone: res.data.phone || "",
          address: res.data.address || "",
          avatar: (res.data.avatar || "").trim(),
          username: res.data.username || "",
        };
      } catch (error) {
        console.error("Không lấy được thông tin người dùng", error);
      } finally {
        loading.value = false;
      }
    };

    const handleImageUpload = (event) => {
      const file = event.target.files[0];
      if (!file) return;
      form.value.avatar_preview = URL.createObjectURL(file);
      form.value.avatar_file = file;
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

        alert("Đăng xuất thành công!");
        window.location.href = "/";
      } catch (error) {
        console.error("Lỗi đăng xuất:", error);
        alert("Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại!");
      }
    };

    const handleSubmit = async () => {
      try {
        const formData = new FormData();
        formData.append("fullname", form.value.fullname);
        formData.append("phone", form.value.phone);
        formData.append("address", form.value.address);
        if (form.value.avatar_file) {
          formData.append("avatar", form.value.avatar_file);
        }
        formData.append("_method", "PATCH");

        await axios.post(`http://127.0.0.1:8000/api/user/${user.value.id}`, formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            ...(form.value.avatar_file && {
              "Content-Type": "multipart/form-data",
            }),
          },
        });

        toast.success("Đã cập nhật thông tin thành công.");
        console.log("Gửi form:", form.value);
        await personally();
      } catch (error) {
        toast.error("Có lỗi xảy ra, vui lòng thử lại sau.");
        console.error(error);
        alert("Cập nhật thất bại.");
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
      primaryColor,
    };
  },
};
</script>
<style scoped>
.border-custom {
  border: 1px solid #ca111f;
}

.avatar-circle {
  width: clamp(80px, 25vw, 100px);
  height: clamp(80px, 25vw, 100px);
  border-radius: 50%;
  aspect-ratio: 1/1;
  overflow: hidden;
  /* QUAN TRỌNG: ẩn phần thừa */
  position: relative;
  margin: auto;
}

.avatar-circle img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  /* QUAN TRỌNG: giữ tỷ lệ, cắt vừa khung */
  object-position: center;
  display: block;
}

.fade-in {
  animation: fadeIn 0.4s ease-in-out;
}

.list-group-item:hover {
  background-color: #cdcdcd;
  border-radius: 20px;
  cursor: pointer;
}

li.list-group-item {
  border: none !important;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .avatar-circle {
    max-width: 100px;
    font-size: 28px;
  }
}
</style>
