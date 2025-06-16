<template>
  <h3 class="title">Quản lý danh mục</h3>

  <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
    <router-link :to="{ name: 'insert-food-category' }" class="btn btn-add">+ Thêm danh mục</router-link>

    <span class="vd">Tìm kiếm</span>
    <input type="text" v-model="searchKeyword" class="custom-input" placeholder="Tìm danh mục..." />

    <span class="vd">Lọc</span>
    <select v-model="selectedParent" class="custom-select">
      <option value="">Tất cả danh mục cha</option>
      <option v-for="cate in allCategories" :key="cate.id" :value="cate.id">{{ cate.name }}</option>
    </select>

    <span class="vd">Hiển thị</span>
    <select v-model="perPage" @change="changePerPage" class="custom-select">
      <option value="5">5</option>
      <option value="10">10</option>
      <option value="15">15</option>
    </select>
  </div>

  <!-- Desktop Table -->
  <div class="table-responsive d-none d-lg-block">
    <table class="table table-bordered rounded">
      <thead class="table-light">
        <tr>
          <th><input type="checkbox" @change="toggleSelectAll" :checked="isAllSelected" /></th>
          <th>STT</th>
          <th>Tên</th>
          <th>Hình ảnh</th>
          <th>Danh mục cha</th>
          <th>Tuỳ chọn</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="categories.length === 0">
          <td colspan="5" class="text-center text-muted">Không có danh mục nào.</td>
        </tr>
        <template v-for="(item, index) in categories" :key="item.id">
          <tr>
            <td><input type="checkbox" :value="item.id" v-model="selectedIds" /></td>
            <td>{{ index + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>
              <img class="me-2 img_thumbnail"
                :src="item.images ? 'http://127.0.0.1:8000/storage/img/food/imgmenu/' + item.images : 'https://cdn-icons-png.flaticon.com/512/1375/1375106.png'"
                :alt="item.name">
            </td>

            <td>{{ item.parent_name || 'Không có (Danh mục cha)' }}</td>
            <td class="d-flex justify-content-center gap-2 flex-wrap">
              <router-link :to="{ name: 'update-food-category', params: { id: item.id } }" class="btn btn-outline btn-sm">
                Sửa
              </router-link>
              <button class="btn btn-danger-delete btn-sm" @click="handleDelete(item.id)">Xoá</button>

            </td>
          </tr>
          <tr v-for="(child, childIndex) in item.children" :key="child.id">
            <td><input type="checkbox" :value="child.id" v-model="selectedIds" /></td>
            <td>{{ index + 1 }}.{{ childIndex + 1 }}</td>

            <td>{{ child.name }}</td>
            <td>
              <img class="me-2 img_thumbnail"
                :src="child.images ? 'http://127.0.0.1:8000/storage/img/food/imgmenu/' + child.images : 'https://cdn-icons-png.flaticon.com/512/1375/1375106.png'"
                :alt="child.name">
            </td>

            <td>{{ item.name }}</td>
            <td class="d-flex justify-content-center gap-2 flex-wrap">
              <router-link :to="{ name: 'update-food-category', params: { id: child.id } }" class="btn btn-outline btn-sm">
                Sửa
              </router-link>
              <button class="btn btn-danger-delete btn-sm" @click="handleDelete(child.id)">Xoá</button>

            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav class="mt-3">
    <ul class="pagination">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">«</a>
      </li>
      <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
        <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
      </li>
      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
        <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">»</a>
      </li>
    </ul>
  </nav>

  <div class="mt-2 d-flex justify-content-start">
    <button class="btn btn-danger-delete delete_desktop" @click="handleDeleteSelected" :disabled="selectedIds.length === 0">
      Xoá đã chọn ({{ selectedIds.length }})
    </button>
  </div>



  <!-- Mobile View -->
  <div class="d-block d-lg-none">
    <div class="card mb-3" v-for="(item, index) in categories" :key="item.id">
      <div class="row g-0 align-items-center">
        <div class="col-3 fs-4 fw-bold ps-4">
          <input type="checkbox" />
          {{ index + 1 }}
        </div>
        <div class="col-9">
          <div class="card-body">
            <h5 class="card-title">{{ item.name }}</h5>
            <p class="card-text"><span class="label">Danh mục cha:</span> Không có</p>
            <button class="btn btn-outline btn-sm">Sửa</button>
            <button class="btn btn-danger-delete btn-sm">Xoá</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ref, onMounted, watch, computed } from 'vue'
import { debounce } from 'lodash'
import Swal from 'sweetalert2'


export default {
  setup() {
    const categories = ref([])
    const allCategories = ref([])
    const perPage = ref(10)
    const currentPage = ref(1)
    const totalPages = ref(1)
    const selectedParent = ref('')
    const searchKeyword = ref('')
    const selectedIds = ref([])
    const isLoading = ref(true) 


    const fetchCategories = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/admin/categories/list', {
          params: {
            per_page: perPage.value,
            page: currentPage.value,
            search: searchKeyword.value,
            parent_id: selectedParent.value
          },
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })

        let fetchedCategories = response.data.data

        if (selectedParent.value) {
          const parentItem = fetchedCategories.find(item => item.id == selectedParent.value)

          // Nếu có children thì gán thêm parent_name vào từng child
          if (parentItem && parentItem.children?.length > 0) {
            const childrenWithParent = parentItem.children.map(child => ({
              ...child,
              parent_name: parentItem.name  // gán tên cha vào
            }))
            categories.value = childrenWithParent
          } else {
            categories.value = []
          }
        } else {
          // Trường hợp không lọc => dùng nguyên data (cha + children)
          categories.value = fetchedCategories
        }

        totalPages.value = response.data.last_page
        currentPage.value = response.data.current_page
      } catch (error) {
        console.error('Lỗi khi load danh mục:', error)
      }
    }

    const fetchAllParents = async () => {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/admin/categories/parents/list', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        allCategories.value = response.data.data
      } catch (error) {
        console.error('Lỗi khi lấy danh mục cha:', error)
      }
    }

    const handleDelete = async (id) => {
      const result = await Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: 'Bạn sẽ không thể hoàn tác hành động này!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Xoá',
        cancelButtonText: 'Huỷ'
      })

      if (result.isConfirmed) {
        try {
          await axios.delete(`http://127.0.0.1:8000/api/admin/categories/${id}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          })

          Swal.fire({
            icon: 'success',
            title: 'Đã xoá thành công!',
            showConfirmButton: false,
            timer: 1500
          })

          fetchCategories()
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi xoá danh mục!',
            text: error?.response?.data?.message || 'Đã xảy ra lỗi không xác định',
          })
        }
      }
    }
    const isAllSelected = computed(() =>
      categories.value.flatMap(item => [item.id, ...(item.children || []).map(child => child.id)])
        .every(id => selectedIds.value.includes(id))
    )

    const toggleSelectAll = () => {
      const allIds = categories.value.flatMap(item => [item.id, ...(item.children || []).map(child => child.id)])
      if (isAllSelected.value) {
        selectedIds.value = []
      } else {
        selectedIds.value = allIds
      }
    }

    const handleDeleteSelected = async () => {
      const result = await Swal.fire({
        title: 'Xác nhận xoá',
        text: `Bạn có chắc chắn muốn xoá ${selectedIds.value.length} danh mục?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Xoá',
        cancelButtonText: 'Huỷ'
      })

      if (result.isConfirmed) {
        try {
          await axios.post('http://127.0.0.1:8000/api/admin/categories/delete-multiple', {
            ids: selectedIds.value
          }, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          })

          Swal.fire({
            icon: 'success',
            title: 'Đã xoá thành công!',
            showConfirmButton: false,
            timer: 1500
          })

          selectedIds.value = []
          fetchCategories()
        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi xoá danh mục!',
            text: error?.response?.data?.message || 'Đã xảy ra lỗi không xác định',
          })
        }
      }
    }


    const debounceFetch = debounce(() => {
      currentPage.value = 1
      fetchCategories()
    }, 300)

    const goToPage = (page) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
        fetchCategories()
      }
    }

    const changePerPage = () => {
      currentPage.value = 1
      fetchCategories()
    }

    watch(selectedParent, () => {
      currentPage.value = 1
      fetchCategories()
    })

    watch(searchKeyword, () => {
      debounceFetch()
    })

    onMounted(() => {
      fetchAllParents()
      fetchCategories()
    })

    return {
      categories,
      allCategories,
      perPage,
      currentPage,
      totalPages,
      selectedParent,
      searchKeyword,
      selectedIds,
      isAllSelected,
      goToPage,
      changePerPage,
      handleDelete,
      toggleSelectAll,
      handleDeleteSelected

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
}

.custom-input:focus,
.custom-select:focus {
  border-color: #999;
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
  padding: 4px 10px;
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

@media (max-width: 768px) {

  .table-responsive,
  .vd {
    display: none;
  }

  .custom-input,
  .custom-select {
    width: 100%;
    font-size: 14px;
    height: 32px;
  }
}

.img_thumbnail {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
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
