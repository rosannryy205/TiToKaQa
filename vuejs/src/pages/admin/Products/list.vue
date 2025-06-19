<template>
  <h3 class="title">Quản lý món ăn</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">

    <router-link :to="{ name: 'insert-food' }" class="btn btn-add">
      + Thêm món ăn
    </router-link>
    <span class="vd">Tìm kiếm</span>
    <input type="text" v-model="searchText" placeholder="Tìm món ăn..." class="clean-input" />


    <span class="vd">Lọc</span>
    <select class="clean-select" v-model="selectedCategory">
      <option value="">Tất cả danh mục</option>

      <template v-for="category in categories" :key="category.id">
        <option :value="category.id">{{ category.name }}</option>
        <option v-for="child in category.children" :key="'child-' + child.id" :value="child.id">
          &nbsp;&nbsp;-- {{ child.name }}
        </option>
      </template>
    </select>

    <span class="vd">Hiển thị</span>
    <select class="custom-select" v-model="limit">
      <option :value="5">5</option>
      <option :value="10">10</option>
      <option :value="15">15</option>
    </select>
  </div>



  <!-- Table Responsive -->
  <!-- <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered rounded">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>STT</th>
          <th class="d-none d-md-table-cell">Ảnh sản phẩm</th>
          <th>Tên sản phẩm</th>
          <th>Danh mục cha</th>
          <th>Giá sản phẩm</th>
          <th>Trạng thái sản phẩm</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" /></td>
          <td>1</td>
          <td>1</td>
          <td>Chưa phân loại</td>
          <td>Chưa phân loại</td>
          <td>Chưa phân loại</td>
          <td>Danh mục mặc định</td>
          <td class="d-flex justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline">Sửa</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" /></td>
          <td>2</td>
          <td>Mỳ</td>
          <td></td>
          <td class="d-flex justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>
          </td>
        </tr>
        <tr>
          <td><input type="checkbox" /></td>
          <td>3</td>
          <td>Mỳ trộn</td>
          <td>Mỳ</td>
          <td class="d-flex justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Thêm sản phẩm</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <button class="btn btn-danger-delete delete_desktop">Xoá</button> -->


  <div class="table-responsive ">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" /></th>
          <th>Món ăn</th>
          <th>Danh mục</th>
          <th>Giá thành</th>
          <th>Số lượng</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="food in foods" :key="food.id">
          <td><input type="checkbox" :value="food.id" v-model="selectedFoods" /></td>
          <td style="max-width: 220px;" class="text-start">
            <img :src="'http://127.0.0.1:8000/storage/img/food/' + food.image" :alt="food.name"
              class="me-2 img_thumbnail" style="width:80px" />
            {{ food.name }}
          </td>
          <td>{{ food.category?.name || 'Không có danh mục' }}</td>
          <td>{{ food.price.toLocaleString('vi-VN') }} VNĐ</td>
          <td>{{ food.stock }}</td>
          <td class="d-flex gap-2">
            <router-link :to="{ name: 'update-food', params: { id: food.id } }">
              <button type="button" class="btn btn-update ">
                Sửa
              </button>
            </router-link>
            <button class="btn btn-clean btn-delete btn-sm" @click="deleteFood(food.id)">Xoá</button>

            <button @click="toggleStatus(food)" class="btn btn-toggle-status"
              :class="food.status === 'active' ? 'btn-outline-secondary' : 'btn-outline-success'"
              style="min-width: 60px">
              {{ food.status === 'active' ? 'Ẩn' : 'Hiện' }}
            </button>
            <button class="btn btn-outline-primary btn-sm" @click="openToppingModal(food)">
              Topping
            </button>

            <!-- <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#toppingModal">Toppings</button> -->
          </td>
        </tr>
      </tbody>
    </table>
  </div>


  <!-- Modal chọn topping -->
  <div class="modal fade" id="toppingModal" tabindex="-1" aria-labelledby="toppingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content shadow-sm">
        <div class="modal-header bg-light">
          <h5 class="modal-title text-danger text-center" id="toppingModalLabel">
            Chọn topping cho: {{ selectedFood?.name }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div v-if="loadingToppingModal" class="text-center py-5">
            <div class="spinner-border text-danger" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Đang tải topping...</p>
          </div>
          <div v-else>
            <div v-if="Array.isArray(toppings) && toppings.length === 0">
              Không có topping để hiển thị.
            </div>


            <div v-else class="table-responsive">
              <table class="table table-bordered align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Chọn</th>
                    <th>Tên topping</th>
                    <th>Giá</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="topping in toppings" :key="topping.id">
                    <td class="text-center">
                      <input class="form-check-input" type="checkbox" :value="topping.id"
                        v-model="selectedToppingIds" />
                    </td>
                    <td>{{ topping.name }}</td>
                    <td>{{ formatNumber(topping.price) }} đ</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <div class="modal-footer bg-light">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button class="btn btn-primary" @click="saveToppings">Lưu</button>
        </div>
      </div>
    </div>
  </div>




  <nav class="mt-4">
    <ul class="pagination">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">«</button>
      </li>

      <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: currentPage === page }">
        <button class="page-link" @click="currentPage = page">{{ page }}</button>
      </li>

      <li class="page-item" :class="{ disabled: currentPage === lastPage }">
        <button class="page-link" @click="currentPage++" :disabled="currentPage === lastPage">»</button>
      </li>
    </ul>
  </nav>
  <button class="btn btn-danger-delete delete_desktop" @click="deleteSelectedFoods"
    :disabled="selectedFoods.length === 0">Xoá đã chọn({{ selectedFoods.length }})</button>



  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 d-flex p-1">
          <input type="checkbox" name="" id="">
          <img src="/img/food/mykimchihaisan.webp" alt="Mỳ kim chi hải sản" class="img-fluid rounded" />
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Mỳ kim chi hải sản</h5>
            <p class="card-text"><strong>Danh mục:</strong> Mỳ cay</p>
            <p class="card-text"><strong>Giá:</strong> 55,000 VNĐ</p>
            <p class="card-text"><strong>Số lượng:</strong> 10</p>
            <button class="btn-outline btn-primary btn-sm">Sửa</button>
            <button class="btn-outline btn-danger-delete btn-sm">Xoá</button>
            <button class="btn-outline btn-warning btn-sm">Ẩn</button><button class="btn btn-dark btn-sm"
              data-bs-toggle="modal" data-bs-target="#toppingModal">Toppings</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <button class="btn-outline btn-danger-delete delete_mobile">Xoá</button>


</template>
<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import * as bootstrap from 'bootstrap'

const foods = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const limit = ref(10);
const selectedCategory = ref('');
const selectedFoods = ref([]);
const searchText = ref('');


const newFood = ref({
  name: '',
  price: '',
  sale_price: '',
  stock: '',
  category_id: '',
  description: '',
  image: null
});


const formatNumber = (number) => {
  return new Intl.NumberFormat('vi-VN').format(number);
};




const categories = ref([])

const fetchCategories = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/admin/categories', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    categories.value = response.data
  } catch (error) {
    console.error('Lỗi khi load danh mục:', error)
  }
}



const handleImageChange = (e) => {
  newFood.value.image = e.target.files[0]
}

const fetchFoods = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/admin/manage/foods', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      },
      params: {
        limit: limit.value,
        page: currentPage.value,
        categoryId: selectedCategory.value || '',
        search: searchText.value || ''
      }
    });

    foods.value = response.data.data || [];
    currentPage.value = response.data.current_page || 1;
    lastPage.value = response.data.last_page || 1;
  } catch (error) {
    console.error('Lỗi khi load danh sách món ăn:', error.response?.data || error.message);
  }
};






onMounted(() => {
  fetchFoods()
  fetchCategories()
})

watch(currentPage, () => {
  fetchFoods()
})

watch(searchText, () => {
  currentPage.value = 1
  fetchFoods()
})

watch([limit, selectedCategory], () => {
  currentPage.value = 1
  fetchFoods()
})



const deleteFood = async (id) => {
  const confirmResult = await Swal.fire({
    title: 'Bạn có chắc muốn xoá?',
    text: "Hành động này không thể hoàn tác!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Xoá',
    cancelButtonText: 'Huỷ'
  });

  if (confirmResult.isConfirmed) {
    try {
      const response = await axios.delete(`http://127.0.0.1:8000/api/admin/food/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      });

      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: response.data.message || 'Xoá thành công',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });

      fetchFoods();

    } catch (error) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: error.response?.data?.message || 'Xoá thất bại',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
      });
    }
  }
};






const isAllSelected = computed(() => {
  return foods.value.length > 0 && selectedFoods.value.length === foods.value.length;
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedFoods.value = [];
  } else {
    selectedFoods.value = foods.value.map(food => food.id);
  }
};

const deleteSelectedFoods = async () => {
  const confirmResult = await Swal.fire({
    title: 'Xác nhận xoá?',
    text: `Bạn có chắc muốn xoá ${selectedFoods.value.length} món đã chọn?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Xoá',
    cancelButtonText: 'Huỷ'
  });

  if (confirmResult.isConfirmed) {
    try {
      await axios.post('http://127.0.0.1:8000/api/admin/foods/delete-multiple', {
        ids: selectedFoods.value
      }, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      });

      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Đã xoá thành công',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });

      selectedFoods.value = [];
      fetchFoods();

    } catch (error) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: error.response?.data?.message || 'Xoá thất bại',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true
      });
    }
  }



};

const toggleStatus = async (food) => {
  const newStatus = food.status === 'active' ? 'inactive' : 'active';

  console.log('food:', food); // Kiểm tra toàn bộ object
  console.log('Gửi ID:', food.id);
  console.log('Trạng thái mới:', newStatus);

  try {
    await axios.put(`http://127.0.0.1:8000/api/admin/food/${food.id}/status`, {
      status: newStatus,
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    food.status = newStatus;

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã cập nhật trạng thái thành công',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });

  } catch (err) {
    console.error('Lỗi khi gọi API:', err); // In rõ lỗi hơn
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Có lỗi khi cập nhật trạng thái',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    });
  }
}

// topping
const selectedFood = ref(null); // Món ăn đang chọn
const toppings = ref([]);       // Toàn bộ topping
const selectedToppingIds = ref([]); // ID các topping đã được chọn
const loadingToppingModal = ref(false);

const openToppingModal = async (food) => {
  selectedFood.value = food;
  loadingToppingModal.value = true;

  await fetchAllToppings();
  await fetchSelectedToppings(food.id);

  loadingToppingModal.value = false;

  const modalEl = document.getElementById('toppingModal');
  const modal = new bootstrap.Modal(modalEl);
  modal.show();
};



// Lấy toàn bộ topping
const fetchAllToppings = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/admin/topping-food', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      }
    });
    toppings.value = res.data.data;
    console.log('topping:', toppings)
  } catch (err) {
    console.error('Lỗi khi lấy topping:', err);
  }
};

// Lấy topping đã được chọn của món
const fetchSelectedToppings = async (foodId) => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/admin/food/topping/${foodId}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      }
    });

    // cập nhật danh sách tất cả topping
    toppings.value = res.data.data

    // cập nhật danh sách topping đã chọn
    selectedToppingIds.value = res.data.selected_ids;

  } catch (err) {
    console.error('Lỗi khi lấy topping của món:', err);
  }
};

const saveToppings = async () => {
  try {
    await axios.post(`http://127.0.0.1:8000/api/admin/food/topping/${selectedFood.value.id}`, {
      topping_ids: selectedToppingIds.value
    }, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      }
    });

    Swal.fire({
      icon: 'success',
      title: 'Đã lưu topping',
      toast: true,
      position: 'top-end',
      timer: 2000,
      showConfirmButton: false
    });

  } catch (err) {
    console.error('Lỗi khi lưu topping:', err);

    // Lấy thông điệp lỗi chi tiết
    const errorMessage = err.response?.data?.message || 'Lỗi không xác định';
    const validationErrors = err.response?.data?.errors;

    let detailMessage = errorMessage;

    // Nếu có lỗi validate, gộp lại
    if (validationErrors) {
      detailMessage += '\n' + Object.values(validationErrors).flat().join('\n');
    }

    Swal.fire({
      icon: 'error',
      title: 'Lỗi khi lưu topping',
      text: detailMessage,
      toast: false,
      confirmButtonText: 'Đã hiểu'
    });
  }
};



</script>


<script>
import { useMenu } from '@/stores/use-menu';

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
}

.btn-add,
.btn-danger-delete {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
}

.btn-add:hover,
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
  cursor: pointer;
}

.btn-outline:hover {
  background-color: #eee;
  color: #333;
}

.btn-add {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
}

.btn-add:hover {
  background-color: #c92c3c;
  color: #fff;
}

.btn-update {
  background: none;
  color: #ab9c00;
  border: 1px solid #ab9c00;
  padding: 7px 10px;
  border-radius: 4px;
  font-weight: normal;
}

.btn-update:hover {
  background-color: #ab9c00;
  color: #fff;
}

.btn-outline {
  background: none;
  border: 1px solid #ccc;
  padding: 4px 10px;
  border-radius: 4px;
  color: #555;
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

.delete_mobile {
  display: none;
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

.img_thumbnail {
  width: 50px;
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
    display: block;
  }

}
</style>
