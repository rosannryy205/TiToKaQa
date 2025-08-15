<template v-if="hasPermission('update_luckyprize')">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-stats card-raised">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h3 class="text-danger fw-bold mb-0">Cập nhật quà vòng quay</h3>
              <router-link to="/admin/luckyprizes" class="btn btn-outline-secondary rounded-0">
                <i class="bi bi-arrow-counterclockwise"></i> Quay lại
              </router-link>
            </div>
  
            <div v-if="loading">Đang tải…</div>
            <div v-else-if="error" class="text-danger">{{ error }}</div>
  
            <form v-else class="row g-3" :key="form.type">
              <div class="col-12 col-md-3">
                <label class="form-label">Loại quà <span class="text-danger">*</span></label>
                <select v-model="form.type" class="form-select">
                  <option disabled value="">-- Chọn loại --</option>
                  <option value="discount">Mã giảm giá</option>
                  <option value="food">Món ăn</option>
                  <option value="tpoint">T-Point</option>
                </select>
              </div>
  
              <div class="col-12 col-md-5">
                <label class="form-label">Tên quà <span class="text-danger">*</span></label>
                <input
                  v-model.trim="form.name"
                  type="text"
                  class="form-control"
                  placeholder="VD: Mã FREESHIP / Deal Coca / 3K T-Point"
                />
              </div>
  
              <div class="col-12 col-md-2">
                <label class="form-label">Xác suất (%) <span class="text-danger">*</span></label>
                <input
                v-model.number="form.probability"
                @blur="normalizeProbability"
                type="number" min="0" max="100"
                class="form-control"
                :disabled="submitting"
              />
              </div>
  
              <!-- discount -->
              <div class="col-12 col-md-6" v-if="form.type === 'discount'">
                <label class="form-label">Chọn mã giảm giá <span class="text-danger">*</span></label>
                <select v-model="form.data.code" class="form-select">
                  <option disabled value="">-- Chọn mã --</option>
                  <option v-for="d in discounts" :key="d.id" :value="d.code">
                    {{ d.code }} — {{ d.name }}
                  </option>
                </select>
              </div>
  
              <!-- food -->
              <div class="col-12 col-md-6" v-if="form.type === 'food'">
                <label class="form-label">Chọn món ăn <span class="text-danger">*</span></label>
                <select v-model.number="form.data.food_id" class="form-select">
                  <option disabled :value="null">-- Chọn món --</option>
                  <option v-for="f in foods" :key="f.id" :value="Number(f.id)">
                    {{ f.name }} (ID: {{ f.id }})
                  </option>
                </select>
              </div>
  
              <!-- tpoint -->
              <div class="col-12 col-md-6" v-if="form.type === 'tpoint'">
                <label class="form-label">Điểm (usable_points) <span class="text-danger">*</span></label>
                <input
                  v-model.number="form.data.usable_points"
                  type="number"
                  min="1"
                  class="form-control"
                  placeholder="VD: 3000"
                />
              </div>
            </form>
            <div  v-if="!loading && !error" class="mt-3">
            <button
              @click="updatePrize"
              :disabled="!canSubmit || submitting"
              type="button"
              class="btn btn-danger-save"
            >
            {{ submitting ? 'Đang lưu...' : 'Cập nhật Quà' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, onMounted, nextTick, computed } from 'vue'
  import axios from 'axios'
  import Swal from 'sweetalert2'
  import { useRouter, useRoute } from 'vue-router'
  import { Discounts } from '@/stores/discount'
  import { FoodList } from '@/stores/food'
  import { Permission } from '@/stores/permission'
  
  
  const userId = ref(null)
  const { hasPermission } = Permission(userId)
  
  const router = useRouter()
  const route = useRoute()
  const luckyPrizeId = computed(() => route.params.id || null)
  
  const { discounts, getAllDiscount } = Discounts()
  const { foods, getFood } = FoodList.setup?.() ?? FoodList()
  
  const loading = ref(false)
  const error = ref(null)
  const submitting = ref(false)
  const form = ref({
    type: '',
    name: '',
    probability: 10,
    data: {},
  })
  
  function safeParse(v) {
    try { return typeof v === 'string' ? JSON.parse(v) : v } catch { return v }
  }
  
  const isBootstrapping = ref(false)
  
  watch(
    () => form.value.type,
    (t, prev) => {
      if (isBootstrapping.value) return
      if (t === prev) return
      if (t === 'discount')      form.value.data = { code: '' }
      else if (t === 'food')     form.value.data = { food_id: null }
      else if (t === 'tpoint')   form.value.data = { usable_points: null }
      else                       form.value.data = {}
    }
  )
  
  async function loadLuckyPrize() {
    if (!luckyPrizeId.value) {
      await Swal.fire({
        icon: 'error',
        title: 'Thiếu ID',
        text: 'Không xác định được quà cần xem.',
        confirmButtonColor: '#c92c3c',
      })
      router.push('/admin/luckyprizes')
      return
    }
  
    loading.value = true
    error.value = null
    isBootstrapping.value = true
    try {
      const res  = await axios.get(`http://127.0.0.1:8000/api/admin/luckyprizes/${luckyPrizeId.value}`)
      const data = res.data?.data ?? res.data
      const type = String(data?.type ?? '').toLowerCase()
      const raw  = safeParse(data?.data ?? {})
  
      form.value.type        = type
      form.value.name        = data?.name ?? ''
      form.value.probability = Number(data?.probability ?? 10)
  
      if (type === 'discount')      form.value.data = { code: raw?.code ?? '' }
      else if (type === 'food')     form.value.data = { food_id: raw?.food_id != null ? Number(raw.food_id) : null }
      else if (type === 'tpoint')   form.value.data = { usable_points: raw?.usable_points != null ? Number(raw.usable_points) : null }
      else                          form.value.data = {}
  
      await nextTick()
    } catch (err) {
      error.value = err?.response?.data?.message || 'Không tải được dữ liệu.'
      await Swal.fire({
        icon: 'error',
        title: 'Không tải được dữ liệu',
        text: error.value,
        confirmButtonColor: '#c92c3c',
      })
      router.push('/admin/luckyprizes')
    } finally {
      isBootstrapping.value = false
      loading.value = false
    }
  }
  const canSubmit = computed(() => {
  const prob = Number(form.value.probability)
  if (!form.value.type || !form.value.name || Number.isNaN(prob) || prob < 0 || prob > 100) return false
  if (form.value.type === 'discount') return !!form.value.data?.code
  if (form.value.type === 'food')     return form.value.data?.food_id != null
  if (form.value.type === 'tpoint')   return (Number(form.value.data?.usable_points) || 0) > 0
  return false
})

function normalizeProbability() {
  let p = Number(form.value.probability)
  if (Number.isNaN(p)) p = 0
  if (p < 0) p = 0
  if (p > 100) p = 100
  form.value.probability = p
}

async function updatePrize() {
  if (!canSubmit.value || submitting.value) return
  const { isConfirmed } = await Swal.fire({
    icon: 'question',
    title: 'Cập nhật quà vòng quay?',
    text: 'Bạn có chắc muốn lưu thay đổi?',
    showCancelButton: true,
    confirmButtonText: 'Lưu',
    cancelButtonText: 'Huỷ',
    confirmButtonColor: '#c92c3c',
  })
  if (!isConfirmed) return

  submitting.value = true
  try {
    // build data theo type
    let dataField = {}
    if (form.value.type === 'discount') dataField = { code: form.value.data.code }
    if (form.value.type === 'food')     dataField = { food_id: Number(form.value.data.food_id) }
    if (form.value.type === 'tpoint')   dataField = { usable_points: Number(form.value.data.usable_points) }

    const payload = {
      name: form.value.name,
      type: form.value.type,                               
      probability: Number(form.value.probability) || 0,
      data: dataField,                                    
    }

    await axios.put(`http://127.0.0.1:8000/api/admin/luckyprize/update/${luckyPrizeId.value}`, payload)

    await Swal.fire({
      toast: true, position: 'top-end', icon: 'success',
      title: 'Đã cập nhật quà', showConfirmButton: false, timer: 1200, timerProgressBar: true
    })
    router.push('/admin/luckyprizes')
  } catch (e) {
    await Swal.fire({ icon:'error', title:'Cập nhật thất bại', text: e?.response?.data?.message || 'Vui lòng kiểm tra lại dữ liệu.' })
  } finally {
    submitting.value = false
  }
}
  onMounted(async () => {
    await Promise.all([
      typeof getFood === 'function' ? getFood() : Promise.resolve(),
      typeof getAllDiscount === 'function' ? getAllDiscount({ source: 'lucky_wheel' }) : Promise.resolve(),
    ])
    await loadLuckyPrize()
  })
  </script>
  

<style scoped>
.btn-danger-save {
  background: none;
  color: #1d54bc;
  border: 1px solid #1d54bc;
  padding: 6px 12px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    color 0.2s ease;
}
.btn-danger-save:hover {
  background-color: #1d54bc;
  color: #fff;
}

.form-select:focus,
.form-control:focus {
  border-color: #c92c3c;
  box-shadow: none;
}
</style>
