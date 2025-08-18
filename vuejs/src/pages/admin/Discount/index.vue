<template v-if="hasPermission('view_discounts')">
  <div class="row">
    <div class="col-md-12" v-if="filteredDiscounts">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <h3 class="title">Quản lý Mã Giảm Giá</h3>

          <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <router-link :to="{ name: 'insert-discount' }" class="btn btn-add" v-if="hasPermission('create_discounts')">
              + Thêm Mã Giảm Giá
            </router-link>

            <input v-model="searchQuery" type="text" class="clean-input" placeholder="Tìm kiếm" />
          </div>
          <div class="d-flex border-bottom mb-3" style="gap: 20px; font-size: 14px">
            <div v-for="(tab, index) in tabs" :key="index" @click="activeTab = index" class="pb-2 position-relative"
              :class="{
                'fw-bold text-danger': activeTab === index,
                'text-muted': activeTab !== index,
              }" style="cursor: pointer">
              {{ tab.label }}
              <span v-if="tab.count" class="text-secondary">({{ tab.count }})</span>
              <span v-if="activeTab === index" class="position-absolute start-0 bottom-0 w-100"
                style="height: 2px; background-color: #d9363e"></span>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Mã</th>
                  <th>Loại</th>
                  <th>Danh Mục</th>
                  <th>Giảm</th>
                  <th>Đơn tối thiểu</th>
                  <th>Giảm tối đa</th>
                  <th>Nguồn</th>
                  <th>Hiệu lực</th>
                  <th class="text-center">Tùy chọn</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in filteredDiscounts" :key="index">
                  <td>
                    <strong>{{ item.code }}</strong>
                  </td>
                  <td>{{ mapDiscountType(item.discount_type) }}</td>
                  <td>{{ getCategoryFullName(item.category_id) }}</td>
                  <td>
                    {{
                      item.discount_method === 'percent'
                        ? item.discount_value + '%'
                        : formatNumber(item.discount_value) + 'đ'
                    }}
                  </td>
                  <td>{{ formatNumber(item.min_order_value || 0) }}đ</td>
                  <td>
                    {{
                      item.max_discount_amount
                        ? formatNumber(item.max_discount_amount) + 'đ'
                        : 'Không giới hạn'
                    }}
                  </td>
                  <td>
                    <span>{{ item.source }}</span>
                  </td>
                  <td>
                    <span v-if="item.start_date && item.end_date">
                      {{ formatDate(item.start_date) }} - {{ formatDate(item.end_date) }}
                    </span>
                    <span v-else>Không giới hạn</span>
                  </td>
                  <td>
                    <div class="d-flex justify-content-center gap-2 flex-nowrap">
                      <router-link v-if="activeTab === 2 || hasPermission('edit_discounts')" :to="`/admin/update-discount/${item.id}`"
                        class="btn btn-update">
                        Gia hạn
                      </router-link>
                      <template v-else>
                        <button v-if="hasPermission('hidden_discounts')" class="btn btn-outline btn-sm" :disabled="loadingIds.has(item.id)"
                          @click="togglePrizeStatus(item)" >
                          {{ (item.status || '').toLowerCase() === 'inactive' ? 'Hiện' : 'Ẩn' }}
                        </button>
                        <router-link :to="`/admin/update-discount/${item.id}`" class="btn btn-update" v-if="hasPermission('edit_discounts')">
                          Sửa
                        </router-link>
                      </template>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Permission } from '@/stores/permission'
import { Discounts } from '@/stores/discount'
import { FoodList } from '@/stores/food'
import dayjs from 'dayjs'
import Swal from 'sweetalert2'
import axios from 'axios'
import { API_URL } from '@/config'
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission } = Permission(userId)

const activeTab = ref(0)
const tabs = [
  { label: 'Tất cả', count: 0 },
  { label: 'Mã đã ẩn', count: 0 },
  { label: 'Mã hết hạn', count: 0 },
]

const searchQuery = ref('')
const { discounts, getAllDiscount } = Discounts()
const { categorys, getCategoryForAdmin } = FoodList.setup()

const getCategoryFullName = (id) => {
  const category = categorys.value.find((c) => c.id === id)
  if (!category) return '-'

  const parent = categorys.value.find((c) => c.id === category.parent_id)
  return parent ? `${category.name}` : category.name
}

const formatNumber = (number) => Number(number).toLocaleString('vi-VN')
const formatDate = (date) => dayjs(date).format('DD/MM/YYYY')

const mapDiscountType = (type) => {
  switch (type) {
    case 'freeship':
      return 'Miễn phí vận chuyển'
    case 'salefood':
      return 'Giảm món ăn'
    default:
      return type
  }
}

const discountInactive = ref([])
const getInactiveDiscounts = async () => {
  try {
    const res = await axios.get(`${API_URL}/discounts`, {
      params: { status: 'inactive' },
    })
    discountInactive.value = res.data
  } catch (error) {
    console.error('Lỗi khi lấy mã giảm giá inactive:', error)
    discountInactive.value = []
  }
}

//loc
const isExpired = (discounts) => {
  const expiry = discounts.end_date
  return expiry && new Date(expiry) < new Date()
}

const filteredDiscounts = computed(() => {
  const query = searchQuery.value.toLowerCase()
  const currentTab = activeTab.value
  const all = discounts.value || []
  const inactive = discountInactive.value || []

  let filtered = []
  switch (currentTab) {
    case 1:
      filtered = inactive
      break
    case 2:
      filtered = all.filter((d) => isExpired(d))
      break
    default:
      filtered = all.filter((d) => !isExpired(d))
      break
  }
  if (query) {
    filtered = filtered.filter(
      (d) => d.name.toLowerCase().includes(query) || d.code.toLowerCase().includes(query),
    )
  }

  return filtered
})

//an hien
const loadingIds = ref(new Set())

async function togglePrizeStatus(item) {
  const isInactive = String(item.status || '').toLowerCase() === 'inactive'
  const nextStatus = isInactive ? 'active' : 'inactive'

  const { isConfirmed } = await Swal.fire({
    icon: 'question',
    title: isInactive ? 'Hiện lại quà này?' : 'Ẩn quà này?',
    text: isInactive
      ? 'Quà sẽ xuất hiện lại ngay trong vòng quay.'
      : 'Quà sẽ ngừng xuất hiện ngay lập tức.',
    showCancelButton: true,
    confirmButtonText: isInactive ? 'Hiện lại' : 'Ẩn ngay',
    cancelButtonText: 'Huỷ',
    confirmButtonColor: '#c92c3c',
  })
  if (!isConfirmed) return

  try {
    loadingIds.value.add(item.id)
    await axios.patch(
      `${API_URL}/admin/discounts/${item.id}/status`,
      { status: nextStatus },
      { headers: { 'Content-Type': 'application/json' } },
    )
    item.status = nextStatus

    // Cách 2: nếu bạn có 2 danh sách active/inactive, di chuyển item giữa 2 mảng
    // if (nextStatus === 'inactive') {
    //   prizes.value = prizes.value.filter(p => p.id !== item.id)
    //   prizesInactive.value.unshift({ ...item, status: 'inactive' })
    // } else {
    //   prizesInactive.value = prizesInactive.value.filter(p => p.id !== item.id)
    //   prizes.value.unshift({ ...item, status: 'active' })
    // }

    await Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: isInactive ? 'Đã hiện lại mã giảm giá' : 'Đã ẩn mã giảm giá',
      showConfirmButton: false,
      timer: 1200,
      timerProgressBar: true,
    })
    await Promise.all([getAllDiscount(), getInactiveDiscounts()])
    activeTab.value
  } catch (err) {
    await Swal.fire({
      icon: 'error',
      title: 'Không cập nhật được trạng thái',
      text: err?.response?.data?.message || err.message || 'Vui lòng thử lại.',
      confirmButtonColor: '#c92c3c',
    })
  } finally {
    loadingIds.value.delete(item.id)
  }
}
onMounted(async () => {
  await Promise.all([getAllDiscount(), getInactiveDiscounts(), getCategoryForAdmin()])
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
