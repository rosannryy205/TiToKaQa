<template>
  <h3 class="title">Quản lý toppings</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addToppingModal">
      + Thêm toppings
    </button>
    <div class="col-12 col-md-6 col-lg-3 fram" style="max-width: 250px;">
      <v-select v-model="selectTopping" :options="toppings" label="name" placeholder="Nhập tên món ăn" :clearable="true"
        @input="onToppingSearch" class="uniform-input" />
    </div>

    <div class="d-flex align-items-center me-2">
      <span class="vd me-2 text-nowrap">Danh mục</span>
      <select class="custom-select uniform-input" v-model="selectedCateId">
        <option selected disabled>Lọc theo danh mục</option>
        <option v-for="category in category_toppings" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
    </div>
    <div class="d-flex align-items-center">
      <span class="vd me-2 text-nowrap">Hiển thị</span>
      <select class="custom-select uniform-input" v-model.number="itemsPerPageToppings" style="max-width: 80px;">
        <option :value="5">5</option>
        <option :value="10">10</option>
        <option :value="15">15</option>
      </select>
    </div>
  </div>


  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered rounded">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <!-- <th><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"/></th> -->
          <th>Tên</th>
          <th>Danh mục</th>
          <th>Giá</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(topping) in paginatedToppings" :key="topping.id">
          <td><input type="checkbox" :value="topping.id" /></td>
          <!-- <td><input type="checkbox" :value="topping.id" v-model="selectedToppingIds" /></td> -->
          <td>{{ topping.name }}</td>
          <td>{{ topping.category_name }}</td>
          <td>{{ formatNumber(topping.price) }} VNĐ</td>
          <td class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline btn-sm" data-bs-toggle="modal"
              data-bs-target="#updateToppingModal"
              @click="getToppingById(topping.id); selectedToppingId = topping.id">Sửa</button>
            <button class="btn btn-danger-delete btn-sm" @click="deletedTopping(topping.id)">Xoá</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <button class="btn btn-danger-delete delete_desktop">Xoá</button>

  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3">
      <div class=" row g-0 align-items-center" v-for="(topping, index) in paginatedToppings" :key="topping.id">
        <div class="col-3 d-flex align-items-center gap-2 p-2">
          <input type="checkbox" :value="topping.id" />
          <span>{{ index + 1 }}</span>
        </div>
        <div class="col-9">
          <div class="card-body p-2">
            <h5 class="card-title mb-1">{{ topping.name }}</h5>
            <p class="card-text mb-1"><span class="label">Danh mục:</span> {{ topping.category_name }}</p>
            <p class="card-text mb-2"><span class="label">Giá:</span> {{ formatNumber(topping.price) }} VNĐ</p>
            <button class="btn btn-outline btn-sm me-2">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
          </div>
        </div>
      </div>
    </div>

    <!-- <div class="card mb-3">
      <div class="row g-0 align-items-center" >
        <div class="col-3 d-flex align-items-center gap-2 p-2">
          <input type="checkbox" />
          <span>2</span>
        </div>
        <div class="col-9">
          <div class="card-body p-2">
            <h5 class="card-title mb-1">Gà</h5>
            <p class="card-text mb-1"><span class="label">Danh mục:</span> Món thêm</p>
            <p class="card-text mb-2"><span class="label">Giá:</span> 25,000 VNĐ</p>
            <button class="btn btn-outline btn-sm me-2">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
          </div>
        </div>
      </div>
    </div> -->
  </div>
  <div class="d-flex justify-content-center mt-3 w-100">
    <nav>
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage.toppings === 1 }">
          <button type="button" class="page-link" @click="goToPage(currentPage.toppings - 1, 'toppings')">
            «
          </button>
        </li>

        <li v-for="page in totalPagesToppings" :key="page" class="page-item"
          :class="{ active: currentPage.toppings === page }">
          <button type="button" class="page-link" @click="goToPage(page, 'toppings')">
            {{ page }}
          </button>
        </li>

        <li class="page-item" :class="{ disabled: currentPage.toppings === totalPagesToppings }">
          <button type="button" class="page-link" @click="goToPage(currentPage.toppings + 1, 'toppings')">
            »
          </button>
        </li>
      </ul>
    </nav>
  </div>


  <button class="btn btn-danger-delete delete_mobile">Xoá</button>

  <!-- Modal Thêm topping -->
  <div class="modal fade" id="addToppingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header position-relative">
          <h5 class="modal-title position-absolute top-50 start-50 translate-middle">Thêm Toppings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Tên topping <span class="text-danger">*</span></label>
            <input type="text" id="name" class="form-control" placeholder="Nhập tên" v-model="formTopping.name"
              required />
          </div>
          <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
            <select id="category_id" class="form-select" v-model.number="formTopping.category_id" required>
              <option selected disabled>Chọn danh mục</option>
              <option v-for="category in category_toppings" :key="category.id" :value="category.id">
                {{ category.name }}</option>
            </select>
          </div>
          <div>
            <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
            <input type="number" id="price" class="form-control" placeholder="Nhập giá"
              v-model.number="formTopping.price" min="0" required />
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-add" @click="addTopping">Thêm</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal sửa topping -->
  <div class="modal fade" id="updateToppingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header position-relative">
          <h5 class="modal-title position-absolute top-50 start-50 translate-middle">Cập nhật Topping</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Tên topping</label>
            <input type="text" id="name" class="form-control" placeholder="Nhập tên" v-model="formTopping.name"
              required />
          </div>
          <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục</label>
            <select id="category_id" class="form-select" v-model.number="formTopping.category_id" required>
              <option selected disabled>Chọn danh mục</option>
              <option v-for="category in category_toppings" :key="category.id" :value="category.id">
                {{ category.name }}</option>
            </select>
          </div>
          <div>
            <label for="price" class="form-label">Giá</label>
            <input type="number" id="price" class="form-control" placeholder="Nhập giá"
              v-model.number="formTopping.price" min="0" required />
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-add" @click="updateTopping(selectedToppingId)">Cập nhật</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useMenu } from '@/stores/use-menu';
import axios from 'axios';
import { ref, onMounted, computed, watch } from 'vue';
import numeral from 'numeral';
import { toast } from "vue3-toastify";
import Swal from 'sweetalert2';

export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format("0,0");
    },
    // getImageUrl(image) {
    //   return `/img/food/${image}`;
    // },
  },
  setup() {
    useMenu().onSelectedKeys(['admin-roles'])
    const toppings = ref([]);
    const category_toppings = ref([]);
    const selectTopping = ref(null)
    const searchToppingTerm = ref('')
    const currentPage = ref({ toppings: 1 })
    const itemsPerPageToppings = ref(15)
    const selectedToppingId = ref(null)
    const formTopping = ref({
      name: '',
      price: '',
      category_id: '',
    })

    const token = localStorage.getItem('token');
    const fetchTopping = async () => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/admin/toppings`, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        });
        toppings.value = res.data.map(topping => {
          const category = category_toppings.value.find(cate => cate.id === topping.category_id);
          return {
            ...topping,
            category_name: category ? category.name : "Chưa phân Loại"
          }
        });
        console.log("Lấy topping: ", toppings.value)


      } catch (error) {
        console.error("Không lấy được topping", error);
        toast.error("Không thể tải topping.");
      }
    }

    const fetchCategoryToppings = async () => {
      if (!token) return;
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/admin/category_topping`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        category_toppings.value = res.data;
      } catch (error) {
        console.error("Lỗi khi tải danh mục:", error);
        toast.error("Không thể tải danh mục.");
      }
    };

    const addTopping = async () => {
      try {
        await axios.post(`http://127.0.0.1:8000/api/admin/toppings`, formTopping.value, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        })
        // toast.success("Thêm topping thành công.");

        await Swal.fire({
          title: 'Thêm topping thành công',
          icon: 'success',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
        });
        fetchTopping();
        formTopping.value = { name: '', price: '', category_id: '' };
      } catch (error) {
        console.log(error);
        toast.error("Thêm topping thất bại.");
      }
    }

    const getToppingById = async (id) => {
      try {
        const res = await axios.get(`http://127.0.0.1:8000/api/admin/toppingById/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        })
        formTopping.value = {
          name: res.data.name,
          price: res.data.price,
          category_id: res.data.category_id,
        };
      } catch (error) {
        console.log(error);
        toast.error("Chưa lấy được topping thất bại.");
      }
    }

    const updateTopping = async (id) => {
      try {
        await axios.patch(`http://127.0.0.1:8000/api/admin/toppings/${id}`, formTopping.value, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        })
        // toast.success("Cập nhật topping thành công.");
        await Swal.fire({
          title: 'Cập nhật topping thành công',
          icon: 'success',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
        });
        fetchTopping();
      } catch (error) {
        console.log(error);
        toast.error("Cập nhật topping thất bại.");
      }
    }


    const deletedTopping = async (id) => {
      try {
        await axios.delete(`http://127.0.0.1:8000/api/admin/toppings/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        })
        // toast.success("Xóa topping thành công.");
        fetchTopping();
        await Swal.fire({
          title: 'Xóa topping thành công',
          icon: 'success',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
        });
      } catch (error) {
        console.log(error);
        toast.error("Xóa topping thất bại.");
      }
    }

    const selectedCateId = ref(null);
    const filteredToppings = computed(() => {
      if (!selectedCateId.value)
        return toppings.value;
      return toppings.value.filter(t => t.category_id === selectedCateId.value);

    })
    const paginatedToppings = computed(() => {
      const limit = Number(itemsPerPageToppings.value) || 1;
      const filtered = toppings.value.filter((topping) =>
        topping.name.toLowerCase().includes(searchToppingTerm.value.toLowerCase())
        && (!selectedCateId.value || topping.category_id === selectedCateId.value)
      )
      const start = (currentPage.value.toppings - 1) * limit
      return filtered.slice(start, start + limit)
    })
    const totalPagesToppings = computed(() => {
      const limit = Number(itemsPerPageToppings.value) || 1;
      const filtered = toppings.value.filter((topping) => {
        const matchSearch = topping.name.toLowerCase().includes(searchToppingTerm.value.toLowerCase())
        const matchCategory = !selectedCateId.value || topping.category_id === selectedCateId.value
        return matchSearch && matchCategory
      })
      return Math.ceil(filtered.length / limit)
    })

    const goToPage = (page, key) => {
      if (page >= 1 && page <= totalPagesToppings.value) {
        currentPage.value[key] = page
      }
    }
    const onToppingSearch = (event) => {
      searchToppingTerm.value = event.target.value
      currentPage.value.toppings = 1
    }
    watch(selectTopping, (newValue) => {
      searchToppingTerm.value = newValue?.name || ''
    })
    onMounted(() => {
      fetchCategoryToppings()
      fetchTopping()
    })
    return {
      toppings,
      category_toppings,

      selectTopping,
      selectedCateId,
      filteredToppings,
      currentPage,
      itemsPerPageToppings,
      goToPage,
      paginatedToppings,
      totalPagesToppings,
      onToppingSearch,

      addTopping,
      formTopping,
      getToppingById,
      selectedToppingId,
      updateTopping,
      deletedTopping
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

.label {
  font-weight: normal;
  color: #555;
}

.custom-input,
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

.custom-input:focus,
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

.btn-danger-delete {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-danger-delete:hover {
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

.delete_mobile {
  display: none;
}

.fram {
  margin-bottom: 13px;
}

.uniform-input {
  height: 33px !important;
  padding: 6px 12px !important;
  font-size: 14px;
  border-radius: 4px;
}

.vd {
  font-weight: 500;
}

@media (max-width: 768px) {
  .table-responsive {
    display: none;
  }

  .vd {
    display: none;
  }

  .delete_desktop {
    display: none;
  }

  .delete_mobile {
    display: inline-block;
  }

  .custom-input,
  .custom-select {
    width: 100%;
    max-width: 100%;
    font-size: 14px;
    height: 32px;
  }
}
</style>
