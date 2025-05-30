<template>
  <h2 class="mb-3">Quản lý món ăn</h2>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-food' }" class="btn btn-danger1">
      + Thêm món ăn
    </router-link>
    <span class="vd ">Tìm kiếm</span>
    <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm món ăn" />
    <span class="vd">Lọc</span>
    <select class="form-select w-auto" v-model="selectedCategory">
      <option value="">Tất cả danh mục</option>

      <template v-for="category in categories" :key="category.id">
        <option :value="category.id">{{ category.name }}</option>
        <option v-for="child in category.children" :key="'child-' + child.id" :value="child.id">
          &nbsp;&nbsp;↳ {{ child.name }}
        </option>
      </template>
    </select>
  </div>

  <!-- Table Responsive -->
  <!-- <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered rounded">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>Món ăn</th>
          <th>Danh mục</th>
          <th>Giá thành</th>
          <th>Tồn kho</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><input type="checkbox" /></td>
          <td>
            <img src="/img/food/mykimchihaisan.webp" alt="Mỳ kim chi hải sản" class="me-2 img_thumbnail" />
            Mỳ kim chi hải sản
          </td>
          <td>Mỳ cay</td>
          <td>55,000 VNĐ</td>
          <td>10</td>
          <td class="d-flex gap-2">
            <button type="button" class="btn btn-primary">Sửa</button>
            <button class="btn btn-danger-delete">Xoá</button>
            <button class="btn btn-warning">Ẩn</button>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#toppingModal">Toppings</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <button class="btn btn-danger-delete delete_desktop">Xoá</button> -->


  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" /></th>
          <th>Món ăn</th>
          <th>Danh mục</th>
          <th>Giá thành</th>
          <th>Số lượng</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="food in foods" :key="food.id">
          <td><input type="checkbox" /></td>
          <td style="max-width: 220px;" class="text-start">
            <img :src="'http://127.0.0.1:8000/storage/img/food/' + food.image" :alt="food.name"
              class="me-2 img_thumbnail" />
            {{ food.name }}
          </td>
          <td>{{ food.category.name }}</td>
          <td>{{ food.price.toLocaleString('vi-VN') }} VNĐ</td>
          <td>{{ food.stock }}</td>
          <td class="d-flex gap-2">
            <router-link :to="{ name: 'update-food' , params: { id: food.id }}">
              <button type="button" class="btn btn-primary">
                Sửa
              </button>
            </router-link>
            <button class="btn btn-danger-delete" @click="deleteFood(food.id)">Xoá</button>
            <button class="btn btn-warning">Ẩn</button>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#toppingModal">Toppings</button>
          </td>
        </tr>
      </tbody>
    </table>
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

  <button class="btn btn-danger-delete delete_desktop">Xoá</button>


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
            <p class="card-text"><strong>Tồn kho:</strong> 10</p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
            <button class="btn btn-warning btn-sm">Ẩn</button><button class="btn btn-dark btn-sm" data-bs-toggle="modal"
              data-bs-target="#toppingModal">Toppings</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <button class="btn btn-danger-delete delete_mobile">Xoá</button>



  <!-- Modal topping-->
  <div class="modal fade" id="toppingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Danh sách các lựa chọn</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <button type="button" class="btn btn-primary mb-2">
            +Thêm Topping
          </button>
          <div><strong>Cấp độ</strong></div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="level" id="level1">
            <label class="form-check-label" for="level1">Cấp 1</label><br>
            <input class="form-check-input" type="radio" name="level" id="level2">
            <label class="form-check-label" for="level2">Cấp 2</label><br>
            <input class="form-check-input" type="radio" name="level" id="level3">
            <label class="form-check-label" for="level3">Cấp 3</label><br>
            <input class="form-check-input" type="radio" name="level" id="level4">
            <label class="form-check-label" for="level4">Cấp 4</label><br>
            <input class="form-check-input" type="radio" name="level" id="level5">
            <label class="form-check-label" for="level5">Cấp 5</label>
          </div>

          <div><strong>Món thêm</strong></div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="topping2">
            <label class="form-check-label" for="topping2">Bò mỹ (+30.000đ)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="topping3">
            <label class="form-check-label" for="topping3">Kim chi (+10.000đ)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="topping4">
            <label class="form-check-label" for="topping4">Bắp cải tím (+12.000đ)</label>
          </div>
          <button class="btn btn-danger-delete mt-3">Xoá</button>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary">Lưu</button>
        </div>

      </div>
    </div>
  </div>

</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import * as bootstrap from 'bootstrap';
const foods = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const limit = ref(10);
const selectedCategory = ref('');
const newFood = ref({
  name: '',
  price: '',
  sale_price: '',
  stock: '',
  category_id: '',
  description: '',
  image: null
});
const errorAdd = ref({});

const handleImageChange = (e) => {
  newFood.value.image = e.target.files[0]
}


const addFood = async () => {
  try {
    const formData = new FormData()
    formData.append('name', newFood.value.name)
    formData.append('price', newFood.value.price)
    formData.append('sale_price', newFood.value.sale_price || '')
    formData.append('image', newFood.value.image)
    formData.append('stock', newFood.value.stock)
    formData.append('category_id', newFood.value.category_id)
    formData.append('description', newFood.value.description || '')


    // Xóa lỗi cũ
    errorAdd.value = {}

    await axios.post('http://127.0.0.1:8000/api/admin/foods', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    // Làm mới danh sách món ăn
    fetchFoods()

    // Reset form
    newFood.value = {
      name: '',
      price: '',
      sale_price: '',
      stock: '',
      category_id: '',
      description: '',
      image: null
    }

    document.querySelector('#addCategoryModal .btn-close').click()
    alert('Thêm món ăn thành công!')
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const allErrors = error.response.data.errors
      const priorityOrder = [
        'name',
        'price',
        'image',
        'sale_price',
        'stock',
        'category_id',
        'description'
      ]

      // Xoá lỗi cũ
      errorAdd.value = {}

      for (const field of priorityOrder) {
        if (allErrors[field]) {
          errorAdd.value[field] = allErrors[field]
          break // chỉ hiển thị lỗi đầu tiên theo thứ tự ưu tiên
        }
      }
    } else {
      alert('Có lỗi xảy ra, vui lòng thử lại.')
    }
  }
}


const fetchFoods = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/admin/foods', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      },
      params: {
        limit: limit.value,
        page: currentPage.value,
        categoryId: selectedCategory.value || '' // Đảm bảo categoryId được gửi đúng
      }
    })
    foods.value = response.data.data
    currentPage.value = response.data.current_page
    lastPage.value = response.data.last_page
  } catch (error) {
    console.error('Lỗi khi load danh sách món ăn:', error)
  }
}


watch(limit, () => {
  currentPage.value = 1
  fetchFoods()
})

onMounted(() => {
  fetchFoods()
  fetchCategories()
})

// Khi thay đổi trang
watch(currentPage, () => {
  fetchFoods()
})

watch([limit, selectedCategory], () => {
  currentPage.value = 1
  fetchFoods()
})


const deleteFood = async (id) => {
  try {
    const response = await axios.delete(`http://localhost:8000/api/admin/food/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });
    alert(response.data.message);
    fetchFoods();
  } catch (error) {
    console.error(error);
    alert(error.response?.data?.message || 'Lỗi khi xoá món ăn');
  }
}


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


const editingFoodId = ref(null)

const openEditModal = (food) => {
  newFood.value = {
    name: food.name,
    price: food.price,
    sale_price: food.sale_price,
    stock: food.stock,
    category_id: food.category_id,
    description: food.description,
    image: null, // Reset image, vì không thể hiển thị lại File
  }
  editingFoodId.value = food.id
  errorAdd.value = {}

  const modal = new bootstrap.Modal(document.getElementById('editFoodModal'))
  modal.show()
}

const updateFood = async () => {
  try {
    const formData = new FormData()
    formData.append('name', newFood.value.name)
    formData.append('price', newFood.value.price)
    formData.append('sale_price', newFood.value.sale_price || '')
    if (newFood.value.image) formData.append('image', newFood.value.image)
    formData.append('stock', newFood.value.stock)
    formData.append('category_id', newFood.value.category_id)
    formData.append('description', newFood.value.description || '')

    await axios.post(`http://127.0.0.1:8000/api/admin/update-food/${editingFoodId.value}`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    fetchFoods()
    editingFoodId.value = null
    newFood.value = {
      name: '',
      price: '',
      sale_price: '',
      stock: '',
      category_id: '',
      description: '',
      image: null
    }

    document.querySelector('#editFoodModal .btn-close').click()
    alert('Cập nhật món ăn thành công!')
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const allErrors = error.response.data.errors
      errorAdd.value = {}

      const priorityOrder = ['name', 'price', 'image', 'sale_price', 'stock', 'category_id', 'description']
      for (const field of priorityOrder) {
        if (allErrors[field]) {
          errorAdd.value[field] = allErrors[field]
          break
        }
      }
    } else {
      alert('Có lỗi xảy ra khi cập nhật.')
    }
  }
}



</script>


<style scoped>
.img_thumbnail {
  width: 50px;
}

.delete_mobile {
  display: none;
}

.btn-danger-delete {
  background-color: #C92C3C;
  color: white;
}

.btn-danger-delete:hover {
  background-color: #a51928;
  color: white;
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
