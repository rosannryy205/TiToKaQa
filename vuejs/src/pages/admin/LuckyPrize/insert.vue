<template v-if="hasPermission('view_luckyprizes')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h3 class="text-danger fw-bold">Thêm Quà Vòng Quay</h3>
            <div>
              <!-- Nút quay lại -->
              <router-link to="/admin/prizes">
                <a href="#" class="btn btn-outline-secondary rounded-0">
                  <i class="bi bi-arrow-counterclockwise"></i> Quay lại
                </a>
              </router-link>
            </div>
          </div>

          <!-- Form -->
          <form class="row g-3">
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
                type="number"
                min="0"
                max="100"
                class="form-control"
              />
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label">Trạng thái<span class="text-danger">*</span></label>
              <select v-model="form.status" class="form-select">
                <option value="active">Hoạt động</option>
                <option value="inactive">Ngừng</option>
              </select>
            </div>

            <div class="col-12 col-md-6" v-if="form.type === 'discount'">
              <label class="form-label">Chọn mã giảm giá <span class="text-danger">*</span></label>
              <select v-model="form.data.code" class="form-select">
                <option disabled value="">-- Chọn mã --</option>
                <option v-for="d in discounts" :key="d.id" :value="d.code">
                  {{ d.code }} — {{ d.name }}
                </option>
              </select>
            </div>
            <div class="col-12 col-md-6" v-if="form.type === 'food'">
              <label class="form-label">Chọn món ăn <span class="text-danger">*</span></label>
              <select v-model="form.data.food_id" class="form-select">
                <option disabled value="">-- Chọn món --</option>
                <option v-for="f in foods" :key="f.id" :value="f.id">
                  {{ f.name }} (ID: {{ f.id }})
                </option>
              </select>
            </div>

            <div class="col-12 col-md-6" v-if="form.type === 'tpoint'">
              <label class="form-label"
                >Điểm (usable_points) <span class="text-danger">*</span></label
              >
              <input
                v-model.number="form.data.usable_points"
                type="number"
                min="1"
                class="form-control"
                placeholder="VD: 3000"
              />
            </div>
          </form>

          <!-- Nút submit -->
          <button 
          @click="submitPrize"
           type="button" class="btn btn-danger-save">Thêm Quà</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'
import { Discounts } from '@/stores/discount'
import { FoodList } from '@/stores/food'
import Swal from 'sweetalert2'
import { useRouter } from 'vue-router'

const router = useRouter()
const submitting = ref(false)
const { discounts, getAllDiscount } = Discounts()
const { foods, getFood } = FoodList.setup()
const form = ref({
  type: '',
  name: '',
  probability: 10,
  status: 'active',
  data: {},
})

watch(
  () => form.value.type,
  (t) => {
    if (t === 'discount') form.value.data = { code: '' }
    if (t === 'food') form.value.data = { food_id: '' }
    if (t === 'tpoint') form.value.data = { usable_points: null }
  },
)

const canSubmit = computed(() => {
  if (!form.value.type || !form.value.name || form.value.probability == null) return false
  if (form.value.type === 'discount') return !!form.value.data.code
  if (form.value.type === 'food') return !!form.value.data.food_id
  if (form.value.type === 'tpoint') return (Number(form.value.data.usable_points) || 0) > 0
  return false
})

async function submitPrize() {
  if (!canSubmit.value || submitting.value) return

  const { isConfirmed } = await Swal.fire({
    icon: 'question',
    title: 'Thêm quà vòng quay?',
    text: 'Bạn có chắc muốn lưu quà này?',
    showCancelButton: true,
    confirmButtonText: 'Lưu',
    cancelButtonText: 'Huỷ',
    confirmButtonColor: '#c92c3c',
  })
  if (!isConfirmed) return

  submitting.value = true
  try {
    const payload = {
      name: form.value.name,
      type: form.value.type,
      probability: Number(form.value.probability) || 0,
      status: form.value.status,
      data: {},
    }

    if (form.value.type === 'discount') payload.data = { code: form.value.data.code }
    if (form.value.type === 'food')     payload.data = { food_id: form.value.data.food_id }
    if (form.value.type === 'tpoint')   payload.data = { usable_points: form.value.data.usable_points }

    payload.data = JSON.stringify(payload.data)

    await axios.post('http://localhost:8000/api/admin/luckyprize/create', payload)

    resetForm()
    await Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Đã thêm quà',
      showConfirmButton: false,
      timer: 1200,
      timerProgressBar: true,
    })

    router.push('/admin/luckyprizes')
  } catch (e) {
    console.error('Lỗi khi lưu:', e)
    await Swal.fire({
      icon: 'error',
      title: 'Thêm quà thất bại',
      text: e?.response?.data?.message || 'Vui lòng kiểm tra lại dữ liệu.',
    })
  } finally {
    submitting.value = false
  }
}

function resetForm() {
  form.value = { type: '', name: '', probability: 10, status: 'active', data: {} }
}

onMounted(async () => {
  await Promise.all([getFood, getAllDiscount({ source: 'lucky_wheel' })])
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
