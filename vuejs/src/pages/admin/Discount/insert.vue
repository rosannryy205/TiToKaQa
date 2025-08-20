<template v-if="hasPermission('create_discount')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h3 class="text-danger fw-bold">Thêm Mã Giảm Giá</h3>
            <div>
              <!-- Nút quay lại -->
              <router-link to="/admin/discounts">
                <a href="#" class="btn btn-outline-secondary rounded-0">
                  <i class="bi bi-arrow-counterclockwise"></i> Quay lại
                </a>
              </router-link>
            </div>
          </div>

          <!-- Form -->
          <form class="row mt-2">
            <!-- Cột trái -->
            <div class="col-md-8">
              <div class="card rounded-0 border-0 shadow mb-4">
                <div class="card-body">
                  <!-- Code & Name -->
                  <div class="row">
                    <div class="col mb-3">
                      <label for="code" class="form-label">
                        Mã giảm giá <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model="form.code"
                        type="text"
                        class="form-control rounded-0"
                        id="code"
                        placeholder="Nhập mã..."
                        required
                      />
                    </div>
                    <div class="col mb-3">
                      <label for="name" class="form-label">
                        Tên mã <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model="form.name"
                        type="text"
                        class="form-control rounded-0"
                        id="name"
                        placeholder="Nhập tên..."
                        required
                      />
                    </div>
                  </div>

                  <!-- Giá trị giảm & Hình thức -->
                  <div class="row">
                    <div class="col mb-3">
                      <label for="discount_value" class="form-label">
                        Giá trị giảm <span class="text-danger">*</span>
                      </label>
                      <input
                        v-model.number="form.discount_value"
                        type="number"
                        class="form-control rounded-0"
                        id="discount_value"
                        min="1"
                        placeholder="Nhập giá trị"
                        required
                      />
                    </div>

                    <div class="col mb-3">
                      <label for="discount_method" class="form-label">
                        Hình thức <span class="text-danger">*</span>
                      </label>
                      <select
                        v-model="form.discount_method"
                        class="form-select rounded-0"
                        id="discount_method"
                        required
                      >
                        <option value="">Chọn</option>
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Số tiền cố định</option>
                      </select>
                    </div>
                  </div>

                  <!-- Loại mã & Giảm tối đa -->
                  <div class="row">
                    <div class="col mb-3">
                      <label for="discount_type" class="form-label">
                        Loại mã <span class="text-danger">*</span>
                      </label>
                      <select
                        v-model="form.discount_type"
                        class="form-select rounded-0"
                        id="discount_type"
                        required
                      >
                        <option value="">Chọn</option>
                        <option value="freeship">Freeship</option>
                        <option value="salefood">Giảm giá món</option>
                      </select>
                    </div>

                    <!-- Chỉ hiện nếu là percent -->
                    <div class="col mb-3" v-if="needMaxDiscount">
                      <label for="max_discount_amount" class="form-label">Giảm tối đa</label>
                      <input
                        v-model.number="form.max_discount_amount"
                        type="number"
                        class="form-control rounded-0"
                        id="max_discount_amount"
                        min="0"
                        placeholder="Nhập số tiền"
                      />
                    </div>
                  </div>

                  <!-- Giá trị đơn tối thiểu -->
                  <div class="mb-3">
                    <label for="min_order_value" class="form-label">Giá trị đơn tối thiểu</label>
                    <input
                      v-model.number="form.min_order_value"
                      type="number"
                      class="form-control rounded-0"
                      id="min_order_value"
                      min="0"
                      placeholder="Nhập giá trị đơn"
                    />
                  </div>

                  <!-- Danh mục -->
                  <div class="mb-3">
                    <label for="category_id" class="form-label">Danh mục áp dụng</label>
                    <select
                      v-model="form.category_id"
                      class="form-select rounded-0"
                      id="category_id"
                    >
                      <option value="">Không chọn</option>
                      <option v-for="item in categorys" :key="item.id" :value="item.id">
                        {{ item.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Ngày bắt đầu & kết thúc -->
                  <div class="row">
                    <div class="col mb-3">
                      <label for="start_date" class="form-label">Ngày bắt đầu</label>
                      <input
                        v-model="form.start_date"
                        type="date"
                        class="form-control rounded-0"
                        id="start_date"
                      />
                    </div>
                    <div class="col mb-3">
                      <label for="end_date" class="form-label">Ngày kết thúc</label>
                      <input
                        v-model="form.end_date"
                        type="date"
                        class="form-control rounded-0"
                        id="end_date"
                      />
                    </div>
                  </div>

                  <!-- Ghi chú điều kiện -->
                  <div class="mb-3">
                    <label for="custom_condition_note" class="form-label">Ghi chú điều kiện</label>
                    <textarea
                      v-model="form.custom_condition_note"
                      class="form-control rounded-0"
                      id="custom_condition_note"
                      rows="3"
                      placeholder="Nhập ghi chú..."
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cột phải -->
            <div class="col-md-4 mb-4">
              <div class="card rounded-0 border-0 shadow">
                <div class="card-body">
                  <!-- Trạng thái -->
                  <div class="mb-3">
                    <label for="status" class="form-label">
                      Trạng thái <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.status"
                      class="form-select rounded-0"
                      id="status"
                      required
                    >
                      <option value="active">Hoạt động</option>
                      <option value="inactive">Ngừng</option>
                    </select>
                  </div>

                  <!-- Giới hạn sử dụng -->
                  <div class="mb-3">
                    <label for="usage_limit" class="form-label">
                      Giới hạn sử dụng <span class="text-danger">*</span>
                    </label>
                    <input
                      v-model.number="form.usage_limit"
                      type="number"
                      class="form-control rounded-0"
                      id="usage_limit"
                      min="1"
                      placeholder="Nhập số lượt"
                      required
                    />
                  </div>

                  <!-- Nguồn -->
                  <div class="mb-3">
                    <label for="source" class="form-label">
                      Nguồn <span class="text-danger">*</span>
                    </label>
                    <select
                      v-model="form.source"
                      class="form-select rounded-0"
                      id="source"
                      required
                    >
                      <option value="">Chọn</option>
                      <option value="system">Hệ thống</option>
                      <option value="point_exchange">Đổi điểm</option>
                      <option value="lucky_wheel">Vòng quay</option>
                      <option value="for_users">Theo cấp bậc user</option>
                    </select>
                  </div>

                  <!-- Cấp bậc áp dụng (chỉ khi for_users) -->
                  <div class="mb-3" v-if="needUserLevel">
                    <label for="user_level" class="form-label">Cấp bậc áp dụng</label>
                    <select
                      v-model="form.user_level"
                      class="form-select rounded-0"
                      id="user_level"
                    >
                      <option value="">Tất cả</option>
                      <option value="new">New</option>
                      <option value="silver">Silver</option>
                      <option value="gold">Gold</option>
                      <option value="diamond">Diamond</option>
                    </select>
                  </div>

                  <!-- Chi phí điểm (chỉ khi point_exchange) -->
                  <div class="mb-3" v-if="needCost">
                    <label for="cost" class="form-label">Chi phí (điểm)</label>
                    <input
                      v-model.number="form.cost"
                      type="number"
                      class="form-control rounded-0"
                      id="cost"
                      min="0"
                      placeholder="Nhập điểm"
                    />
                  </div>

                  <!-- Điều kiện -->
                  <div class="mb-3">
                    <label for="condition" class="form-label">Điều kiện</label>
                    <textarea
                      v-model="form.condition"
                      class="form-control rounded-0"
                      id="condition"
                      rows="3"
                      placeholder="Nhập điều kiện áp dụng..."
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <!-- Nút submit -->
          <button
            type="button"
            class="btn btn-danger-save"
            @click="submitCreateDiscount"
            :disabled="loading"
          >
            {{ loading ? 'Đang lưu...' : 'Thêm Mã Giảm Giá' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// ============================
// Import & khởi tạo store / lib
// ============================
import { onMounted, reactive, ref, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

import { Permission } from '@/stores/permission'
import { FoodList } from '@/stores/food'
import { useRouter } from 'vue-router'
import { API_URL } from '@/config'
const router = useRouter()
// ============================
// Lấy danh mục (category) từ store của bạn
// ============================
const { categorys, getCategoryForAdmin } = FoodList.setup()

// ============================
// Quyền người dùng (để dùng hasPermission trong template)
// ============================
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)

// ============================
// State form: map 1-1 với backend validation
// - Lưu ý: giữ tên field giống hệt backend để 422 trả về khớp
// ============================
const form = reactive({
  code: '',
  name: '',
  discount_value: null,
  discount_method: 'fixed',
  discount_type: 'freeship',
  max_discount_amount: null,
  min_order_value: 0,
  category_id: '',
  start_date: '',
  end_date: '',
  status: 'active',
  usage_limit: 1,
  source: 'system',
  user_level: '', // required_if source=for_users
  cost: null, // required_if source=point_exchange
  condition: '',
  custom_condition_note: '',
})

// ============================
// UI conditions: điều khiển hiển thị field theo required_if
// ============================
const needMaxDiscount = computed(() => form.discount_method === 'percent')
const needUserLevel = computed(() => form.source === 'for_users')
const needCost = computed(() => form.source === 'point_exchange')

// ============================
// Loading & Errors
// - errors: object { fieldName: [msg1, msg2, ...], ... } theo chuẩn Laravel 422
// ============================
const loading = ref(false)
const errors = ref({})

// ============================
// Helpers
// ============================

/**
 * Lấy dòng lỗi đầu tiên của 1 field (để show dưới input)
 */
function firstError(field) {
  const arr = errors.value?.[field]
  return Array.isArray(arr) && arr.length ? arr[0] : ''
}

/**
 * Loại bỏ field null/undefined/'' để payload gọn, tránh gửi rác
 * (backend vẫn validate được các field cần thiết)
 */
function prunePayload(obj) {
  const out = {}
  for (const k of Object.keys(obj)) {
    const v = obj[k]
    const isEmptyString = typeof v === 'string' && v.trim() === ''
    if (v === undefined || v === null || isEmptyString) continue
    out[k] = v
  }
  return out
}

function buildPayload() {
  const base = { ...form }

  // '' -> null để backend hiểu là "không giới hạn danh mục"
  if (base.category_id === '') base.category_id = null

  // Nếu không phải percent thì không gửi max_discount_amount
  if (!needMaxDiscount.value) base.max_discount_amount = null

  // Nếu không phải for_users thì không gửi user_level
  if (!needUserLevel.value) base.user_level = null

  // Nếu không phải point_exchange thì không gửi cost
  if (!needCost.value) base.cost = null

  // Cắt bớt field rỗng
  return prunePayload(base)
}

/**
 * Reset form về giá trị mặc định sau khi tạo thành công
 */
function resetForm() {
  form.code = ''
  form.name = ''
  form.discount_value = null
  form.discount_method = 'fixed'
  form.discount_type = 'freeship'
  form.max_discount_amount = null
  form.min_order_value = 0
  form.category_id = ''
  form.start_date = ''
  form.end_date = ''
  form.status = 'active'
  form.usage_limit = 1
  form.source = 'system'
  form.user_level = ''
  form.cost = null
  form.condition = ''
  form.custom_condition_note = ''
}
// ============================
// Submit: gọi API tạo mã giảm giá
// - Dùng SweetAlert để thông báo thành công/lỗi
// - Bắt 422 để gom lỗi validate và hiển thị rõ
// ============================
async function submitCreateDiscount() {
  loading.value = true
  errors.value = {}

  try {
    const payload = buildPayload()

    // Nếu dùng axios với baseURL & interceptor token thì không cần set headers thủ công
    const res = await axios.post(`${API_URL}/admin/discounts/create`, payload, {
      headers: { 'Content-Type': 'application/json' },
    })

    await Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Tạo mã thành công!',
      showConfirmButton: false,
      timer: 1500,
      timerProgressBar: true,
    })

    resetForm()
    router.push('/admin/discounts')
  } catch (err) {
    // Laravel Validation Error
    if (err?.response?.status === 422) {
      errors.value = err.response.data.errors || {}

      // Ghép tất cả lỗi thành 1 chuỗi (mỗi lỗi 1 dòng) để show Swal gọn gàng
      const allErrors = Object.values(errors.value).flat().join('\n')

      await Swal.fire({
        icon: 'error',
        title: 'Lỗi dữ liệu',
        text: allErrors || 'Vui lòng kiểm tra lại các trường nhập.',
        confirmButtonColor: '#c92c3c',
      })
    } else {
      // Lỗi khác (500, 403, network, ...)
      await Swal.fire({
        icon: 'error',
        title: 'Có lỗi xảy ra',
        text: err?.response?.data?.message || err.message || 'Không thể tạo mã giảm giá.',
        confirmButtonColor: '#c92c3c',
      })
    }
  } finally {
    loading.value = false
  }
}

// ============================
// Lifecycle
// ============================
onMounted(async () => {
  await getCategoryForAdmin()
})
</script>

<style>
.themsp {
  width: 200px;
}

.img_thumbnail {
  width: 50px;
}

.btn-danger-delete {
  background: none;
  color: #c92c3c;
  border: 1px solid #c92c3c;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition:
    background-color 0.3s ease,
    color 0.3s ease;
}

.btn-danger-delete:hover {
  background-color: #c92c3c;
  color: #fff;
}

.btn-danger-save {
  background: none;
  color: #1d54bc;
  border: 1px solid #1d54bc;
  padding: 4px 10px;
  border-radius: 4px;
  font-weight: normal;
  cursor: pointer;
  transition:
    background-color 0.3s ease,
    color 0.3s ease;
}

.btn-danger-save:hover {
  background-color: #1d54bc;
  color: #fff;
}

.form-select:focus {
  border-color: #c92c3c;
  box-shadow: none;
}

#menuModal > div > div > div.modal-header > button {
  background-color: #fff !important;
}

#menuModal .modal-header .btn-outline-secondary {
  border: none !important;
  background: none !important;
  padding: 0.25rem 0.5rem;
  font-size: 1.5rem;
  line-height: 1;
}

#menuModal .modal-header .btn-outline-secondary:hover {
  background: none !important;
  color: inherit !important;
}
</style>
