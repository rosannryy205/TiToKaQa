<template v-if="hasPermission('view_luckyprizes')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <h3 class="title">Quản lý Quà Vòng Quay</h3>

          <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <router-link :to="{ name: 'insert-luckyprize' }" class="btn btn-add">
              + Thêm Quà Vòng Quay
            </router-link>
            <input v-model="searchQuery" type="text" class="clean-input" placeholder="Tìm kiếm" />
          </div>

          <!-- Tabs -->
          <div class="d-flex border-bottom mb-3" style="gap: 20px; font-size: 14px">
            <div
              v-for="(tab, index) in tabs"
              :key="index"
              @click="activeTab = index"
              class="pb-2 position-relative"
              :class="{
                'fw-bold text-danger': activeTab === index,
                'text-muted': activeTab !== index,
              }"
              style="cursor: pointer"
            >
              {{ tab.label }}
              <span
                v-if="activeTab === index"
                class="position-absolute start-0 bottom-0 w-100"
                style="height: 2px; background-color: #d9363e"
              ></span>
            </div>
          </div>

          <div v-if="loading">Đang tải…</div>
          <div v-else-if="error" class="text-danger">{{ error }}</div>

          <div v-else class="table-responsive">
            <table class="table table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Tên quà</th>
                  <th>Loại</th>
                  <th>Dữ liệu</th>
                  <th>Xác suất</th>
                  <th>Trạng thái</th>
                  <th class="text-center">Tùy chọn</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredPrizes" :key="item.id">
                  <td>{{ item.name }}</td>
                  <td class="text-capitalize">{{ item.type }}</td>
                  <td>
                    <span v-if="item.type === 'food'">food_id: {{ item.data?.food_id }}</span>
                    <span v-else-if="item.type === 'discount'">code: {{ item.data?.code }}</span>
                    <span v-else-if="item.type === 'tpoint'"
                      >points: {{ item.data?.usable_points }}</span
                    >
                    <span v-else>{{ stringify(item.data) }}</span>
                  </td>
                  <td>{{ Number(item.probability) || 0 }}%</td>
                  <td>{{ item.status }}</td>
                  <td>
                    <div class="d-flex justify-content-center gap-2 flex-nowrap">
  <button
    class="btn btn-outline btn-sm"
    :disabled="loadingIds.has(item.id)"
    @click="togglePrizeStatus(item)"
  >
    {{ (item.status || '').toLowerCase() === 'inactive' ? 'Hiện' : 'Ẩn' }}
  </button>

  <router-link
    :to="`/admin/update-luckyprize/${item.id}`"
    class="btn btn-update"
  >
    Sửa
  </router-link>
</div>

                  </td>
                </tr>
                <tr v-if="filteredPrizes.length === 0">
                  <td colspan="6" class="text-center text-muted">Không có kết quả phù hợp</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4" class="text-center">Tổng xác suất</td>
                  <td class="text-center fw-bold">
                    {{ sumProbability }}%
                    <br />
                    <span v-if="sumProbability >= 101" class="text-danger">
                      Tổng xác suất đang vượt mức có thể gây lỗi cho người dùng!
                    </span>
                  </td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import { Permission } from '@/stores/permission'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'

const userId = ref(null)
const { hasPermission } = Permission(userId)
const router = useRouter()
/* ---------- State ---------- */
const prizes = ref([])
const prizesInactive = ref([])
const loading = ref(false)
const error = ref(null)
const searchQuery = ref('')

const activeTab = ref(0)
const tabs = ref([
  { label: 'Tất cả', count: 0 },
  { label: 'Quà đã ẩn', count: 0 },
])
/* ---------- Utils ---------- */
function safeParse(s) {
  try {
    return typeof s === 'string' ? JSON.parse(s) : s
  } catch {
    return s
  }
}
function stringify(v) {
  try {
    return typeof v === 'string' ? v : JSON.stringify(v)
  } catch {
    return String(v)
  }
}

/* ---------- API ---------- */
const API = 'http://localhost:8000/api/lucky-wheel/prizes'

const getPrizesForAdmin = async () => {
  try {
    const res = await axios.get(API) // mặc định trả active
    const raw = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
    prizes.value = raw.map((p) => ({
      ...p,
      data: safeParse(p.data),
    }))
  } catch (e) {
    console.error(e)
    error.value = 'Không tải được danh sách phần thưởng (active).'
  }
}

const getInactivePrizes = async () => {
  try {
    const res = await axios.get(API, { params: { status: 'inactive' } })
    const raw = Array.isArray(res.data) ? res.data : (res.data?.data ?? [])
    prizesInactive.value = raw.map((p) => ({
      ...p,
      data: safeParse(p.data),
    }))
  } catch (e) {
    console.error(e)
    // giữ im lặng tab inactive, không chặn toàn trang
    prizesInactive.value = []
  }
}

/* ---------- Derived ---------- */
const currentList = computed(() =>
  activeTab.value === 1 ? prizesInactive.value || [] : prizes.value || [],
)

const filteredPrizes = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return currentList.value

  return currentList.value.filter((p) => {
    const inName = p.name?.toLowerCase().includes(q)
    const inType = p.type?.toLowerCase().includes(q)
    const inData =
      (p.type === 'food' &&
        String(p.data?.food_id ?? '')
          .toLowerCase()
          .includes(q)) ||
      (p.type === 'discount' &&
        String(p.data?.code ?? '')
          .toLowerCase()
          .includes(q)) ||
      (p.type === 'tpoint' &&
        String(p.data?.usable_points ?? '')
          .toLowerCase()
          .includes(q))
    return inName || inType || inData
  })
})

const sumProbability = computed(() => {
  return currentList.value.reduce((total, p) => total + (Number(p.probability) || 0), 0)
})


const loadingIds = ref(new Set())
async function togglePrizeStatus(item) {
  const isInactive = String(item.status || '').toLowerCase() === 'inactive'
  const nextStatus = isInactive ? 'active' : 'inactive'

  const { isConfirmed } = await Swal.fire({
    icon: 'question',
    title: isInactive ? 'Hiện lại quà này?' : 'Ẩn quà này?',
    text: isInactive ? 'Quà sẽ xuất hiện lại ngay trong vòng quay.' : 'Quà sẽ ngừng xuất hiện ngay lập tức.',
    showCancelButton: true,
    confirmButtonText: isInactive ? 'Hiện lại' : 'Ẩn ngay',
    cancelButtonText: 'Huỷ',
    confirmButtonColor: '#c92c3c',
  })
  if (!isConfirmed) return

  try {
    loadingIds.value.add(item.id)
    await axios.patch(
      `http://127.0.0.1:8000/api/admin/luckyprize/${item.id}/status`,
      { status: nextStatus },
      { headers: { 'Content-Type': 'application/json' } }
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
      title: isInactive ? 'Đã hiện lại quà' : 'Đã ẩn quà',
      showConfirmButton: false,
      timer: 1200,
      timerProgressBar: true,
    })
    await Promise.all([ getPrizesForAdmin(), getInactivePrizes() ])
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
/* ---------- Keep tab counts in sync ---------- */
watch(
  [prizes, prizesInactive],
  () => {
    tabs.value[0].count = prizes.value.length
    tabs.value[1].count = prizesInactive.value.length
  },
  { immediate: true },
)

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    await Promise.all([getPrizesForAdmin(), getInactivePrizes()])
  } catch (e) {
    console.log(e)
  } finally {
    loading.value = false
  }
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
