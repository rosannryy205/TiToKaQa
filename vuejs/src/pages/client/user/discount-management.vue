<template>
  <div class="container col-12 col-md-8 col-lg-9 py-4">
    <h4 class="fw-bold mb-3">Kho Voucher</h4>

    <!-- Nhập mã -->
    <div class="d-flex align-items-center mb-3" style="gap: 10px">
      <input
        v-model="voucherCode"
        type="text"
        class="form-control"
        placeholder="Nhập mã voucher tại đây"
        style="max-width: 400px; font-size: 14px; border-radius: 0.25rem"
      />
      <button class="btn btn-save-discount px-4">Lưu</button>
    </div>

    <!-- Tabs -->
    <div class="d-flex border-bottom mb-3" style="gap: 20px; font-size: 14px">
      <div
        v-for="(tab, index) in tabs"
        :key="index"
        @click="activeTab = index"
        class="pb-2 position-relative"
        :class="{ 'fw-bold text-danger': activeTab === index, 'text-muted': activeTab !== index }"
        style="cursor: pointer"
      >
        {{ tab.label }}
        <span v-if="tab.count" class="text-secondary">({{ tab.count }})</span>
        <span
          v-if="activeTab === index"
          class="position-absolute start-0 bottom-0 w-100"
          style="height: 2px; background-color: #d9363e"
        ></span>
      </div>
    </div>

    <!-- Danh sách voucher -->
    <div class="row g-3">
      <div class="col-md-6" v-for="discount in userDiscounts" :key="discount.id">
        <div class="d-flex shadow-sm bg-white rounded overflow-hidden" style="min-height: 110px">
          <!-- Cột trái -->
          <div
            class="text-white d-flex flex-column justify-content-center align-items-center"
            :style="`background-color: ${
              discount.discount_type === 'freeship' ? '#00bfa5' : '#f44336'
            }; width: 28%`"
          >
            <img :src="getImageByType(discount.discount_type)" alt="icon" style="width: 40px" />
            <div class="fw-bold small mt-2 text-center" style="font-size: 12px">
              {{ discount.discount_type === 'freeship' ? 'FREESHIP' : 'GIẢM GIÁ' }}
            </div>
          </div>

          <!-- Cột phải -->
          <div class="flex-grow-1 px-3 py-2" style="width: 72%">
            <div class="fw-bold mb-1 text-truncate">Mã: {{ discount.name }}</div>
            <div
                class="text-muted small mb-1 text-truncate d-block"
                style="max-width: 100%; overflow: hidden"
              >
                <i class="bi bi-clock me-1"></i>Hạn dùng: {{ discount.pivot.expiry_at }}
              </div>
            <div class="text-muted small mb-1 text-truncate">
              <a
                href="#"
                class="ms-1 text-primary"
                @click.prevent="showConditionModal(discount.condition, discount.name)"
              >
                Điều kiện
              </a>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bold coins-exchange d-flex align-items-center">
                {{ formatCurrency(discount.cost) }}
                <img class="coins ms-1" src="/img/xubac.png" alt="coin" />
              </div>
              <button class="btn btn-outline-danger btn-sm float-end">Dùng Sau</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!---->
  <div
    class="modal fade"
    id="voucherConditionModal"
    tabindex="-1"
    aria-labelledby="voucherConditionModalLabel"
    aria-hidden="true"
    ref="conditionModalRef"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title fw-bold me-5" id="voucherConditionModalLabel">Điều kiện voucher</h6>
          <button type="button" class="btn-close" @click="hideConditionModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-0 text-dark fs-5 text-center">Mã: {{ selectedVoucherName }}</p>
        </div>
        <div class="modal-body">
          <p class="mb-0 small text-danger text-center">
            {{ selectedVoucherCondition || 'Không có điều kiện cụ thể.' }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { Modal } from 'bootstrap'
import axios from 'axios'
import { Discounts } from '@/stores/discount'
const voucherCode = ref('')
const activeTab = ref(0)
const {
    getImageByType,
    formatCurrency,
    userDiscounts,
    fetchUserDiscounts
    } = Discounts()
const tabs = [
  { label: 'Tất cả', count: 0 },
  { label: 'Mã Giảm Món', count: 0 },
  { label: 'Mã FreeShip', count: 0 },
  { label: 'Mã Theo Danh Mục', count: 0 },
  { label: 'Lịch Sử Đổi Mã', count: 0 },
]
const conditionModalRef = ref(null)
let conditionModalInstance = null
const selectedVoucherCondition = ref('')
const selectedVoucherName = ref('')

onMounted(async () => {
  fetchUserDiscounts()
  if (conditionModalRef.value) {
    conditionModalInstance = new Modal(conditionModalRef.value)
  }
})



const showConditionModal = (condition, name) => {
  selectedVoucherCondition.value = condition
  selectedVoucherName.value = name
  conditionModalInstance?.show()
}

const hideConditionModal = () => {
  conditionModalInstance?.hide()
}
</script>
<style scoped>
.category-icon {
  width: 50px;
  height: 50px;
  object-fit: contain;
}
.voucher-card {
  padding: 5px;
}

.voucher-card img {
  width: auto;
  height: 120px;
  display: block;
  margin: 0 auto 10px auto;
}

.voucher-card .card-body {
  padding: 5px;
}

.voucher-card h6 {
  font-size: 14px;
  margin-bottom: 6px;
}
.coins-exchange {
  color: rgb(119, 119, 119) !important;
}
.coins {
  width: 15px !important;
  height: 15px !important;
  margin: 6px 5px 5px 5px !important;
}
.title-cate-discount,
.title-discount-hot {
  color: #c92c3c;
}

@media (max-width: 576px) {
  .category-icon {
    width: 18px;
    height: 18px;
  }

  .voucher-card img {
    height: 120px;
  }
}
.coins-small {
  width: 24px;
  height: 24px;
}
@media (max-width: 576px) {
  .coins-small {
    width: 20px;
    height: 20px;
  }
}
.btn-sm {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}
.btn-save-discount {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}
.btn-save-discount:hover {
  background-color: #c92c3c;
  color: white;
}
.has-voucher {
  color: #007d00;
  border: 1px solid #007d00;
}
.btn-sm:hover {
  background-color: #c92c3c;
  color: white;
}
.voucher-brand-btn.active {
  background-color: #c92c3c;
  color: white;
}

.btn-save-discount {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}
.btn-save-discount:hover {
  background-color: #c92c3c;
  color: white;
}
</style>
