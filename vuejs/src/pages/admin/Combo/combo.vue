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

  <div class="table-responsive">
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th class="d-none d-sm-table-cell"><input type="checkbox" /></th>
        <th>Tên combo</th>
        <th>Giá bán</th>
        <th class="d-none d-md-table-cell">Tuỳ chọn</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in combo" :key="index">
        <td class="d-none d-sm-table-cell"><input type="checkbox" /></td>
        <td>
  <img
    :src="`/img/food/${item.image}`"
    :alt="item.name"
    class="me-2 img_thumbnail"
  />
  {{ item.name }}
  <div
    class="d-md-none mt-2 d-flex justify-content-center gap-2 flex-wrap"
  >
    <button type="button" class="btn btn-outline btn-sm">Sửa</button>
    <button class="btn btn-clean btn-delete btn-sm">Xoá</button>
    <button
      class="btn btn-outline btn-sm"
      data-bs-toggle="modal"
      data-bs-target="#menuModal"
    >
      Chi tiết
    </button>
  </div>
</td>

        <td>{{ formatNumber(item.price) }} VNĐ</td>
        <td class="d-none d-md-table-cell">
          <div class="d-flex justify-content-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline btn-sm">Sửa</button>
            <button class="btn btn-clean btn-delete btn-sm">Xoá</button>
            <button
              class="btn btn-outline btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#menuModal"
            >
              Chi tiết
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</div>

  <button class="btn btn-clean btn-delete">Xoá</button>

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
        <div class="d-flex flex-column flex-md-row mb-3 gap-2">
          <input
            type="text"
            class="clean-input w-100"
            placeholder="Nhập tên món..."
            id="searchInput"
          />
          <select class="clean-select w-100 w-md-auto" id="categoryFilter">
            <option value="">Tất cả</option>
            <option value="Khai vị">Khai vị</option>
            <option value="Món chính">Món chính</option>
            <option value="Tráng miệng">Tráng miệng</option>
            <option value="Đồ uống">Đồ uống</option>
          </select>
        </div>

        <div class="pe-3 table-responsive">
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
                <td><input type="checkbox" class="menu-checkbox" data-price="60000" /></td>
                <td>Salad rau củ</td>
                <td><button class="btn btn-clean btn-sm">Topping</button></td>
                <td>60.000 đ</td>
              </tr>
              <tr data-category="Món chính">
                <td><input type="checkbox" class="menu-checkbox" data-price="380000" /></td>
                <td>Lẩu cua đồng</td>
                <td><button class="btn btn-clean btn-sm">Topping</button></td>
                <td>380.000 đ</td>
              </tr>
              <tr data-category="Món chính">
                <td><input type="checkbox" class="menu-checkbox" data-price="250000" /></td>
                <td>Gà nướng mật ong</td>
                <td><button class="btn btn-clean btn-sm">Topping</button></td>
                <td>250.000 đ</td>
              </tr>
              <tr data-category="Tráng miệng">
                <td><input type="checkbox" class="menu-checkbox" data-price="20000" /></td>
                <td>Rau câu dừa</td>
                <td><button class="btn btn-clean btn-sm">Topping</button></td>
                <td>20.000 đ</td>
              </tr>
              <tr data-category="Đồ uống">
                <td><input type="checkbox" class="menu-checkbox" data-price="40000" /></td>
                <td>Trà sữa</td>
                <td><button class="btn btn-clean btn-sm">Topping</button></td>
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
        <button class="btn btn-clean btn-delete btn-sm" id="deleteSelected">Xoá</button>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-clean btn-sm" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-clean btn-sm">Lưu lại</button>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import { useMenu } from '@/stores/use-menu'
import router from '@/router';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import numeral from 'numeral';

export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    }},
  setup() {
    useMenu().onSelectedKeys(['admin-roles'])
    
    const combo = ref([]);

    const fetchCombos = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/admin/combos');
        combo.value = res.data;
        console.log(combo.value);
      } catch (error) {
        console.log(error);
      }
    }

    onMounted(() => {
      fetchCombos();
    });

    return {
      combo,
      fetchCombos
    }
  }
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
  appearance: none;
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
.btn-delete {
  border-color: #c92c3c !important;
  color: #c92c3c !important;
}

.btn-delete:hover {
  background-color: #c92c3c !important;
  color: white !important;
}
@media (max-width: 576px) {
  .img_thumbnail {
    width: 36px;
    height: 36px;
  }

  .clean-input,
  .clean-select,
  .custom-select {
    width: 100% !important;
    margin-top: 5px;
  }

  .btn-outline,
  .btn-clean {
    padding: 4px 8px;
    font-size: 0.8rem;
  }
}

</style>
