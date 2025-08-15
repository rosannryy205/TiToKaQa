<template v-if="hasPermission('update_discount')">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-stats card-raised">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <h3 class="text-danger fw-bold">Cập nhật Mã Giảm Giá</h3>
              <div>
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
  
                      <!-- Chỉ hiện khi discount_method=percent -->
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
                        <option value="">Chọn</option>
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
  
            <!-- Nút submit (Update-only) -->
            <button
              type="button"
              class="btn btn-danger-save"
              @click="submit"
              :disabled="loading"
            >
              {{ loading ? 'Đang lưu...' : 'Cập nhật Mã Giảm Giá' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { onMounted, reactive, ref, computed } from 'vue'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  import { Permission } from '@/stores/permission'
  import { FoodList } from '@/stores/food'
  import { useRouter, useRoute } from 'vue-router'
  
  // Router/Route
  const router = useRouter()
  const route = useRoute()
  const discountId = computed(() => route.params.id || null)
  
  // Category store
  const { categorys, getCategoryForAdmin } = FoodList.setup()
  
  // Permission
  const userId = ref(null)
  const userString = localStorage.getItem('user')
  if (userString) {
    const user = JSON.parse(userString)
    if (user && user.id !== undefined) {
      userId.value = user.id
    }
  }
  const { hasPermission } = Permission(userId)
  
  // Form state
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
    user_level: '',
    cost: null,
    condition: '',
    custom_condition_note: '',
  })
  
  // Conditional fields
  const needMaxDiscount = computed(() => form.discount_method === 'percent')
  const needUserLevel   = computed(() => form.source === 'for_users')
  const needCost        = computed(() => form.source === 'point_exchange')
  
  // Misc
  const loading = ref(false)
  const errors  = ref({})
  
  // Utils
  function prunePayload(obj) {
    const out = {}
    Object.keys(obj).forEach(k => {
      const v = obj[k]
      const isEmptyString = typeof v === 'string' && v.trim() === ''
      if (v === undefined || v === null || isEmptyString) return
      out[k] = v
    })
    return out
  }
  function toDateInput(val) {
    if (!val) return ''
    if (/^\d{4}-\d{2}-\d{2}$/.test(val)) return val
    const d = new Date(val)
    if (isNaN(d.getTime())) return ''
    const m = `${d.getMonth() + 1}`.padStart(2, '0')
    const day = `${d.getDate()}`.padStart(2, '0')
    return `${d.getFullYear()}-${m}-${day}`
  }
  function buildPayload() {
    const base = { ...form }
    if (base.category_id === '') base.category_id = null
    if (!needMaxDiscount.value) base.max_discount_amount = null
    if (!needUserLevel.value)   base.user_level = null
    if (!needCost.value)        base.cost = null
    return prunePayload(base)
  }
  function fillForm(data) {
    form.code = data.code ?? ''
    form.name = data.name ?? ''
    form.discount_value = data.discount_value ?? null
    form.discount_method = data.discount_method ?? 'fixed'
    form.discount_type = data.discount_type ?? 'freeship'
    form.max_discount_amount = data.max_discount_amount ?? null
    form.min_order_value = data.min_order_value ?? 0
    form.category_id = data.category_id ?? ''
    form.start_date = toDateInput(data.start_date)
    form.end_date = toDateInput(data.end_date)
    form.status = data.status ?? 'active'
    form.usage_limit = data.usage_limit ?? 1
    form.source = data.source ?? 'system'
    form.user_level = data.user_level ?? ''
    form.cost = data.cost ?? null
    form.condition = data.condition ?? ''
    form.custom_condition_note = data.custom_condition_note ?? ''
  }
  
  async function loadDiscount() {
    if (!discountId.value) {
      await Swal.fire({
        icon: 'error',
        title: 'Thiếu ID',
        text: 'Không xác định được mã cần cập nhật.',
        confirmButtonColor: '#c92c3c',
      })
      router.push('/admin/discounts')
      return
    }
    loading.value = true
    try {
      const res = await axios.get(`http://127.0.0.1:8000/api/admin/discounts/${discountId.value}`)
      fillForm(res.data?.data || res.data)
    } catch (err) {
      await Swal.fire({
        icon: 'error',
        title: 'Không tải được dữ liệu',
        text: err?.response?.data?.message || err.message || 'Vui lòng thử lại.',
        confirmButtonColor: '#c92c3c',
      })
      router.push('/admin/discounts')
    } finally {
      loading.value = false
    }
  }

  async function submit() {
    if (!discountId.value) {
      await Swal.fire({
        icon: 'error',
        title: 'Thiếu ID',
        text: 'Không xác định được mã cần cập nhật.',
        confirmButtonColor: '#c92c3c',
      })
      return
    }
  
    loading.value = true
    errors.value  = {}
  
    try {
      const payload = buildPayload()
      await axios.put(`http://127.0.0.1:8000/api/admin/discounts/update/${discountId.value}`, payload, {
        headers: { 'Content-Type': 'application/json' },
      })
  
      await Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Cập nhật thành công!',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      })
      router.push('/admin/discounts')
    } catch (err) {
      if (err?.response?.status === 422) {
        errors.value = err.response.data.errors || {}
        const allErrors = Object.values(errors.value).flat().join('\n')
        await Swal.fire({
          icon: 'error',
          title: 'Lỗi dữ liệu',
          text: allErrors || 'Vui lòng kiểm tra lại các trường nhập.',
          confirmButtonColor: '#c92c3c',
        })
      } else {
        await Swal.fire({
          icon: 'error',
          title: 'Có lỗi xảy ra',
          text: err?.response?.data?.message || err.message || 'Không thể lưu mã giảm giá.',
          confirmButtonColor: '#c92c3c',
        })
      }
    } finally {
      loading.value = false
    }
  }
  
  // Lifecycle
  onMounted(async () => {
    await getCategoryForAdmin()
    await loadDiscount()
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