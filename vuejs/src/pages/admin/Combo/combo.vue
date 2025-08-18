<template v-if="hasPermission('view_combo')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <h3 class="title">Quản lý combo</h3>

          <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <router-link
              v-if="hasPermission('create_combo')"
              :to="{ name: 'insert-combo' }"
              class="btn btn-add"
            >
              + Thêm Combo
            </router-link>

            <input v-model="searchQuery" type="text" class="clean-input" placeholder="Tìm kiếm" />
          </div>

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Tên combo</th>
                  <th>Giá bán</th>
                  <th class="d-none d-md-table-cell">Tuỳ chọn</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in filteredCombos"
                  :key="index"
                  :class="{ 'table-secondary opacity-50': item.status === 'inactive' }"
                >
                  <td>
                    <img
                     :src="getImageUrl(item.image)"
                      :alt="item.name"
                      class="me-2 img_thumbnail"
                    />
                    {{ item.name }}
                    <div class="d-md-none mt-2 d-flex justify-content-center gap-2 flex-wrap">
                      <button type="button" class="btn btn-outline btn-sm"
                        v-if="hasPermission('edit_combo')">Sửa</button>
                      <button class="btn btn-outline btn-sm" @click="toggleComboStatus(item.id)"
                        v-if="hasPermission('hidden_combo')">
                        {{ item.status === 'inactive' ? 'Hiện' : 'Ẩn' }}
                      </button>
                      <button
                        class="btn btn-outline btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#menuModal"
                        @click="showComboDetail(item)"
                      >
                        Chi tiết
                      </button>
                    </div>
                  </td>
                  <td>{{ formatNumber(item.price) }} VNĐ</td>
                  <td class="d-none d-md-table-cell">
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                      <router-link
                        v-if="hasPermission('edit_combo') && item.status === 'active'"
                        :to="`/admin/update-combo/${item.id}`"
                        class="btn btn-update"
                      >
                        Sửa
                      </router-link>
                      <button
                        v-if="hasPermission('hidden_combo')"
                        class="btn btn-outline btn-sm"
                        :class="item.status === 'inactive' ? 'btn-secondary' : 'btn-warning'"
                        @click="toggleComboStatus(item.id)" >
                        {{ item.status === 'inactive' ? 'Hiện' : 'Ẩn' }}
                      </button>
                      <button
                        class="btn btn-outline btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#menuModal"
                        @click="showComboDetail(item)"
                      >
                        Chi tiết
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
            <nav class="mt-3">
            <ul class="pagination">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click="changePage(currentPage - 1)">«</a>
              </li>
              <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
                <a class="page-link" href="#" @click="changePage(page)">{{ page }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click="changePage(currentPage + 1)">»</a>
              </li>
            </ul>
          </nav>
          </div>
          <!--modal-->
          <div
            class="modal fade"
            id="menuModal"
            tabindex="-1"
            aria-labelledby="menuModalLabel"
            aria-hidden="true"
          >
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
              <div class="modal-content shadow-sm rounded-3">
                <div class="modal-header bg-light">
                  <h5 class="modal-title fw-semibold text-danger" id="menuModalLabel">
                    Chi tiết combo
                  </h5>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                  >
                    &times;
                  </button>
                </div>

                <div class="modal-body">
                  <div v-if="selectedCombo">
                    <div class="mb-4 d-flex flex-column flex-md-row align-items-start gap-3">
                      <img
                        :src="getImageUrl(selectedCombo.image)"
                        :alt="selectedCombo.name"
                        class="rounded"
                        style="width: 100px; height: 100px; object-fit: cover"
                      />
                      <div>
                        <h4 class="fw-bold mb-1">{{ selectedCombo.name }}</h4>
                        <p class="mb-0 text-muted">
                          Giá ưu đãi combo: {{ formatNumber(selectedCombo.price) }} VNĐ
                        </p>
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered align-middle">
                        <thead class="table-light">
                          <tr>
                            <th>STT</th>
                            <th>Bao gồm</th>
                            <th>Số lượng</th>
                            <th>Giá món</th>
                            <th>Thành tiền</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(food, index) in selectedCombo.foods" :key="food.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ food.name }}</td>
                            <td>{{ food.pivot.quantity }}</td>
                            <td>{{ formatNumber(food.price) }} đ</td>
                            <td>{{ formatNumber(food.pivot.quantity * food.price) }} đ</td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="4" class="text-end fw-semibold">Tổng giá gốc combo:</td>
                            <td class="fw-bold text-danger">{{ formatNumber(comboTotal) }} đ</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div v-else>
                    <p class="text-muted">Không có dữ liệu combo để hiển thị.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import numeral from 'numeral'
import { toast } from 'vue3-toastify'
import { Permission } from '@/stores/permission'
import { API_URL } from '@/config'
const combo = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const searchQuery = ref('')
const selectedCombo = ref(null)
const getImageUrl = (image) => {
  return `http://127.0.0.1:8000/storage/img/food/${image}`
}

function formatNumber(value) {
  return numeral(value).format('0,0')
}

const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission } = Permission(userId)

function showComboDetail(item) {
  selectedCombo.value = {
    ...item,
    foods: Array.isArray(item.foods) ? item.foods : [],
  }
}

const comboTotal = computed(() => {
  if (!selectedCombo.value || !Array.isArray(selectedCombo.value.foods)) return 0
  return selectedCombo.value.foods.reduce(
    (total, food) => total + food.price * food.pivot.quantity,
    0,
  )
})
const filteredCombos = computed(() => {
  return combo.value
})
async function fetchCombos(page = 1) {
  try {
    const params = {
      page: page,
      search: searchQuery.value.trim(),
    }

    const res = await axios.get(`${API_URL}/admin/combos`, { params })

    combo.value = res.data.data
    currentPage.value = res.data.current_page
    totalPages.value = res.data.last_page
  } catch (error) {
    console.error(error)
    toast.error('Lỗi khi tải dữ liệu combo.')
  }
}
watch(searchQuery, () => {
  fetchCombos(1)
})

function changePage(page) {
  if (page >= 1 && page <= totalPages.value) {
    fetchCombos(page)
  }
}

async function toggleComboStatus(comboId) {
  try {
    if (!confirm('Bạn có chắc muốn ẩn/hiện combo này?')) return

    const res = await axios.put(`${API_URL}/admin/combos/${comboId}/toggle-status`)
    toast.success(res.data.message)

    const index = combo.value.findIndex((c) => c.id === comboId)
    if (index !== -1) {
      combo.value[index].status = res.data.status
    }
  } catch (error) {
    console.error(error)
    toast.error('Lỗi khi cập nhật trạng thái combo.')
  }
}

onMounted(() => {
  fetchCombos()
})
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
}

.custom-select {
  box-shadow: none;
  border: 1px solid #bbb;
  padding: 2px 6px;
  height: 28px;
  font-size: 13px;
  border-radius: 4px;
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
