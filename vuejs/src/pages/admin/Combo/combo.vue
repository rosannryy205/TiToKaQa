<template>
 <h3 class="title">Quản lý danh mục</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <!-- Nút thêm combo -->
    <router-link :to="{ name: 'insert-combo' }" class="btn btn-add">
      + Thêm Combo
    </router-link>

    <input
      type="text"
      class="clean-input"
      placeholder="Tìm kiếm"
      aria-label="Tìm kiếm"
    />

    <select class="custom-select" style="max-width: 80px">
      <option selected>5</option>
      <option>10</option>
      <option>15</option>
    </select>
  </div>

  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>Tên combo</th>
          <th>Giá bán</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" /></td>
          <td>
            <img
              src="/img/food/mykimchihaisan.webp"
              alt="Mỳ kim chi hải sản"
              class="me-2 img_thumbnail"
            />
            Combo 1
          </td>
          <td>25,000 VNĐ</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-outline btn-sm">Sửa</button>
            <button class="btn btn-clean btn-delete btn-sm">Xoá</button>
            <button
              class="btn btn-outline btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#menuModal"
            >
              Chi tiết
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <button class="btn btn-clean btn-delete delete_desktop">Xoá</button>

  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 d-flex p-1 align-items-center gap-2">
          <input type="checkbox" />
          <img
            src="/img/food/mykimchihaisan.webp"
            alt="Mỳ kim chi hải sản"
            class="img-fluid rounded"
          />
        </div>
        <div class="col-9">
          <div class="card-body py-2">
            <h5 class="card-title mb-1">Combo 1</h5>
            <p class="card-text mb-1"><strong>Giá bán:</strong> 25,000 VNĐ</p>
            <button class="btn btn-clean btn-sm me-2">Sửa</button>
            <button class="btn btn-clean btn-delete btn-sm">Xoá</button>
            <button
              class="btn btn-clean btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#menuModal"
            >
              Chi tiết
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button class="btn btn-clean btn-delete delete_mobile">Xoá</button>

  <!-- Modal chi tiết -->
  <div
    class="modal fade"
    id="menuModal"
    tabindex="-1"
    aria-labelledby="menuModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="menuModalLabel">Danh sách món</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>

        <div class="modal-body">
          <div class="d-flex mb-3 gap-2">
            <input
              type="text"
              class="clean-input"
              placeholder="Nhập tên món..."
              id="searchInput"
            />
            <select class="clean-select w-auto" id="categoryFilter">
              <option value="">Tất cả</option>
              <option value="Khai vị">Khai vị</option>
              <option value="Món chính">Món chính</option>
              <option value="Tráng miệng">Tráng miệng</option>
              <option value="Đồ uống">Đồ uống</option>
            </select>
          </div>

          <div class="pe-3">
            <table class="table">
              <thead>
                <tr>
                  <th>Chọn</th>
                  <th>Tên món</th>
                  <th>Topping</th>
                  <th>Giá</th>
                </tr>
              </thead>
              <tbody id="menuList">
                <tr data-category="Khai vị">
                  <td>
                    <input
                      type="checkbox"
                      class="menu-checkbox"
                      data-price="60000"
                    />
                  </td>
                  <td>Salad rau củ</td>
                  <td>
                    <button class="btn btn-clean btn-sm">Topping</button>
                  </td>
                  <td>60.000 đ</td>
                </tr>
                <tr data-category="Món chính">
                  <td>
                    <input
                      type="checkbox"
                      class="menu-checkbox"
                      data-price="380000"
                    />
                  </td>
                  <td>Lẩu cua đồng</td>
                  <td>
                    <button class="btn btn-clean btn-sm">Topping</button>
                  </td>
                  <td>380.000 đ</td>
                </tr>
                <tr data-category="Món chính">
                  <td>
                    <input
                      type="checkbox"
                      class="menu-checkbox"
                      data-price="250000"
                    />
                  </td>
                  <td>Gà nướng mật ong</td>
                  <td>
                    <button class="btn btn-clean btn-sm">Topping</button>
                  </td>
                  <td>250.000 đ</td>
                </tr>
                <tr data-category="Tráng miệng">
                  <td>
                    <input
                      type="checkbox"
                      class="menu-checkbox"
                      data-price="20000"
                    />
                  </td>
                  <td>Rau câu dừa</td>
                  <td>
                    <button class="btn btn-clean btn-sm">Topping</button>
                  </td>
                  <td>20.000 đ</td>
                </tr>
                <tr data-category="Đồ uống">
                  <td>
                    <input
                      type="checkbox"
                      class="menu-checkbox"
                      data-price="40000"
                    />
                  </td>
                  <td>Trà sữa</td>
                  <td>
                    <button class="btn btn-clean btn-sm">Topping</button>
                  </td>
                  <td>40.000 đ</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                  <td class="fw-bold" id="totalAmount">0 đ</td>
                </tr>
              </tfoot>
            </table>
          </div>
          <button class="btn btn-clean btn-delete btn-sm" id="deleteSelected">
            Xoá
          </button>
        </div>

        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-clean btn-sm"
            data-bs-dismiss="modal"
          >
            Đóng
          </button>
          <button type="button" class="btn btn-clean btn-sm">Lưu lại</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useMenu } from '@/stores/use-menu'
export default {
  setup() {
    useMenu().onSelectedKeys(['admin-roles'])
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
.img_thumbnail {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

/* Giảm kích thước, bỏ viền và nền khi focus, clean hơn */
.clean-input,
.clean-select {
  border: 1px solid #ccc;
  border-radius: 4px;
  height: 28px;
  padding: 4px 8px;
  font-size: 0.85rem;
  background-color: transparent;
  outline: none;
  transition: border-color 0.2s ease;
  box-shadow: none;
  appearance: none; /* Loại bỏ mũi tên select mặc định nếu muốn */
  cursor: pointer;
}

.clean-input:focus,
.clean-select:focus {
  border-color: #c92c3c;
  background-color: transparent;
  box-shadow: none;
  outline: none;
}
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
.custom-select:focus {
  border-color: #999;
  box-shadow: none;
}

.btn-add {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-add:hover {
  background-color: #c92c3c;
  color: #fff;
}
.btn-outline {
  background: none;
  border: 1px solid #ccc;
  padding: 4px 10px;
  border-radius: 4px;
  color: #555;
  font-weight: normal;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-outline:hover {
  background-color: #eee;
  color: #333;
}
/* Nút clean: chỉ viền, không nền, màu chữ đỏ nhẹ */
.btn-clean {
  background-color: transparent !important;
  border: 1px solid #c92c3c;
  color: #c92c3c;
  padding: 4px 12px;
  font-size: 0.85rem;
  border-radius: 4px;
  transition: background-color 0.3s ease, color 0.3s ease;
  cursor: pointer;
}

.btn-clean:hover {
  background-color: #c92c3c !important;
  color: white !important;
}

/* Nút xóa riêng biệt */
.btn-delete {
  border-color: #c92c3c !important;
  color: #c92c3c !important;
}

.btn-delete:hover {
  background-color: #c92c3c !important;
  color: white !important;
}

/* Responsive */
.delete_mobile {
  display: none;
}

@media (max-width: 768px) {
  .table-responsive {
    display: none;
  }

  /* Ẩn label tìm kiếm, lọc trên mobile */
  .mb-4 span.vd {
    display: none;
  }

  .delete_desktop {
    display: none;
  }

  .delete_mobile {
    display: inline-block;
  }
}
</style>
