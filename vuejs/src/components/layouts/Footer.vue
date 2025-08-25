<template>
  <div>
    <footer class="bg-white pt-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 mb-4 text-start">
<<<<<<< HEAD
            <img src="/img/logonew.png" alt="logo-footer" width="100px" />
=======
            <img src="/img/logonew.png" alt="logo-footer" width="150px" />
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
            <p class="text-start">
              Mỳ Cay TITOKAQA chuyên phục vụ các món mì cay chuẩn vị Hàn Quốc, kết hợp nguyên liệu
              tươi ngon và công thức đặc biệt.
            </p>
            <p>
              <strong style="color: #c92c3c">Email:</strong>
              <a href="mailto:support@titokaqa.com" class="text-dark fw-bold">support@titokaqa.com</a>
            </p>
            <p class="fw-bold"><strong style="color: #c92c3c">Hotline:</strong> +84 987 654 321</p>
          </div>
          <div class="col-lg-2 mb-4">
            <h5 class="fw-bold" style="color: #c92c3c">Liên Kết Nhanh</h5>
            <ul class="list-unstyled">
              <li><a href="#" class="text-dark">Câu Hỏi Thường Gặp</a></li>
              <li><a href="#" class="text-dark">Điều Khoản & Điều Kiện</a></li>
              <li><a href="#" class="text-dark">Chính Sách Bảo Mật</a></li>
              <li><a href="#" class="text-dark">Liên Hệ</a></li>
            </ul>
          </div>
          <div class="col-lg-3 mb-4">
            <h5 class="fw-bold text-center" style="color: #c92c3c">Theo Dõi Chúng Tôi</h5>
            <div class="d-flex justify-content-center gap-3">
              <a href="#" class="text-dark"><i class="bi bi-facebook"></i></a>
              <a href="#" class="text-dark"><i class="bi bi-instagram"></i></a>
              <a href="#" class="text-dark"><i class="bi bi-tiktok"></i></a>
              <a href="#" class="text-dark"><i class="bi bi-youtube"></i></a>
            </div>
          </div>
          <div class="col-lg-3 mb-4">
            <h5 class="fw-bold" style="color: #c92c3c">Đăng Ký Nhận Tin</h5>
            <p>Nhận thông tin khuyến mãi và món mới từ Mỳ Cay TITOKAQA.</p>
            <div class="input-group">
<<<<<<< HEAD
              <input type="email" class="form-control" placeholder="Nhập email của bạn" v-model="quickEmail" />

              <button class="btn btn-danger-customer" :disabled="loading" @click="handleQuickRegister"
                style="background-color: rgb(199, 11, 11)">
                {{ loading ? 'Đang gửi...' : 'Đăng Ký' }}
=======
              <input type="email" class="form-control" placeholder="Nhập email của bạn" />
              <button style="background-color: rgb(199, 11, 11)" class="btn btn-danger-customer">
                Đăng Ký
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
              </button>
            </div>
          </div>
        </div>
        <hr class="text-secondary" />
        <div class="row align-items-center">
          <div class="col-lg-6 text-center text-lg-start">
            <p class="mb-0">&copy; 2024 Mỳ Cay TITOKAQA. All Rights Reserved.</p>
          </div>
          <div class="col-lg-6 text-center text-lg-end">
            <img src="/img/visa.png" alt="Visa" class="me-2" style="height: 30px" />
            <img src="/img/mastercard.png" alt="MasterCard" class="me-2" style="height: 30px" />
            <img src="/img/momo.png" alt="Momo" style="height: 30px" />
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
<<<<<<< HEAD
import axios from 'axios';
import { API_URL } from '@/config';
import { ref } from 'vue';
import Swal from 'sweetalert2';

export default {
  setup() {
    const quickEmail = ref('');
    const loading = ref(false);

    // cấu hình toast chung
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',   // góc phải trên
      showConfirmButton: false,
      timer: 3000,            // tự đóng sau 3s
      timerProgressBar: true,
    });

    const handleQuickRegister = async () => {
      if (!quickEmail.value) {
        Toast.fire({
          icon: 'warning',
          title: 'Vui lòng nhập email',
        });
        return;
      }

      loading.value = true;

      try {
        const res = await axios.post(`${API_URL}/quickRegister`, {
          email: quickEmail.value,
        });

        if (res.data.status === 'success') {
          Toast.fire({
            icon: 'success',
            title: res.data.message,
          });
          quickEmail.value = '';
        } else if (res.data.status === 'warning') {
          Toast.fire({
            icon: 'warning',
            title: res.data.message,
          });
        }
      } catch (err) {
        Toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Có lỗi xảy ra. Vui lòng thử lại.',
        });
      } finally {
        loading.value = false;
      }
    };

    return {
      quickEmail,
      loading,
      handleQuickRegister,
    };
  },
};





</script>
=======

</script>

>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
