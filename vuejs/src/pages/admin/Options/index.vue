<template>
  <h2 class="mb-3">Quản lý toppings</h2>

    <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
        <button type="button" class="btn btn-danger1" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            +Thêm toppings
        </button>
        <span class="vd">Tìm kiếm</span>
        <input type="text" class="form-control rounded" style="max-width: 200px" placeholder="Tìm kiếm" />
        <span class="vd">Lọc</span>
        <select class="form-select w-auto rounded" style="max-width: 250px">
            <option selected>Lọc theo danh mục</option>
            <option>Danh mục 1</option>
            <option>Danh mục 2</option>
        </select>


        <span class="vd">Hiển thị</span>
        <select class="form-select w-auto rounded">
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
          <th>Tên</th>
          <th>Danh mục</th>
          <th>Giá</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="top in topping" :key="topping.id">
          <td><input type="checkbox" /></td>
          <td style="max-width: 220px;" class="text-start">{{ top.name }}</td>
          <td>{{ top.category_topping.name }}</td>
          <td>{{ top.price }} VNĐ</td>
          <td class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-primary">Sửa</button>
            <button class="btn btn-danger-delete
                        ">Xoá</button>
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
  <button class="btn btn-danger-delete
     delete_desktop">Xoá</button>


  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox">
          1
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Cấp 1</h5>
            <p class="card-text"><strong>Danh mục:</strong> Cấp độ </p>
            <p class="card-text"><strong>Giá:</strong> </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete
             btn-sm">Xoá</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox">
          2
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">Gà</h5>
            <p class="card-text"><strong>Danh mục:</strong> Món thêm </p>
            <p class="card-text"><strong>Giá:</strong> 25.000 VNĐ </p>
            <button class="btn btn-primary btn-sm">Sửa</button>
            <button class="btn btn-danger-delete
             btn-sm">Xoá</button>
          </div>
        </div>
      </div>


    </div>


  </div>
  <button class="btn btn-danger-delete
   delete_mobile">Xoá</button>



  <!-- Modal Thêm topping -->
  <div class="modal fade" id="addToppingModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thêm Toppings</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Tên topping -->
          <div class="mb-3">
            <div v-if="errorAdd.name" class="text-danger errorAddFood mt-1">{{ errorAdd.name[0] }}</div>
            <input type="text" v-model="newTopping.name" class="form-control add_food__input" id="name"
              placeholder="Tên món ăn">

          </div>

          <!-- Giá -->
          <div class="mb-3">
            <div v-if="errorAdd.price" class="text-danger errorAddFood mt-1">{{ errorAdd.price[0] }}</div>
            <input type="number" v-model="newTopping.price" class="form-control add_food__input" id="price"
              placeholder="Giá">
          </div>

          <select id="category" v-model="newTopping.category_id" class="form-select mb-3" required>
            <option disabled value="">Chọn danh mục</option>
            <option v-for="cate in cateTop" :key="cate.id" :value="cate.id">
              {{ cate.name }}
            </option>
          </select>



        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary" @click="addTopping">Thêm</button>
        </div>
      </div>
    </div>
  </div>



</template>

<script>
import { useMenu } from '@/stores/use-menu';
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
export default {
  setup() {
    useMenu().onSelectedKeys(['admin-roles'])

    const currentPage = ref(1);
    const limit = ref(10)
    const topping = ref([]);
    const selectedCategory = ref('');
    const lastPage = ref(1);
    const cateTop = ref([])

    const fetchTopping = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/admin/toppings', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          },
          params: {
            limit: limit.value,
            page: currentPage.value,
            categoryId: selectedCategory.value || ''

          }
        })
        topping.value = response.data.data
        currentPage.value = response.data.current_page
        lastPage.value = response.data.last_page
      } catch (error) {

      }
    }
    watch(limit, () => {
      currentPage.value = 1
      fetchTopping()
    })

    onMounted(() => {
      fetchTopping();
      fetchCateTopping();
    })

    watch(currentPage, () => {
      fetchTopping()
    })

    watch([limit, selectedCategory], () => {
      currentPage.value = 1
      fetchTopping()
    })

    const fetchCateTopping = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/admin/catetop', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        cateTop.value = response.data
      } catch (error) {
        console.error('lỗi khi load danh mục', error)
      }
    }

    const newTopping = ref({
      name: '',
      price: '',
      category_id: '',
    })

    const errorAdd = ref({});

    const addTopping = async () => {
      try {


        await axios.post('http://127.0.0.1:8000/api/admin/toppings', {
          name: newTopping.value.name,
          price: newTopping.value.price,
          category_id: newTopping.value.category_id
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            Accept: 'application/json'
          }
        })

        fetchTopping()

        newTopping.value = {
          name: '',
          price: '',
          category_id: '',
        }
        document.querySelector('#addToppingModal .btn-close').click()
        alert('Thêm món ăn thành công!')

      } catch (error) {
        if (error.response && error.response.status === 422) {
          const allErrors = error.response.data.errors
          const priorityOrder = [
            'name',
            'price',
            'category_id',
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
    return {
      currentPage,
      limit,
      topping,
      selectedCategory,
      lastPage,
      cateTop,
      fetchTopping,
      fetchCateTopping,
      newTopping,
      errorAdd,
      addTopping
    }
  },

}
</script>

<style scoped>
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
