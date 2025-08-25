<template v-if="hasPermission('view_discounts')">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
          <h3 class="title">Quản lý Mã Giảm Giá</h3>

          <div class="mb-4 d-flex align-items-center gap-3 flex-wrap">
            <router-link
              v-if="hasPermission('create_discounts')"
              :to="{ name: 'insert-discounts' }"
              class="btn btn-add"
            >
              + Thêm Mã Giảm Giá
            </router-link>

            <input v-model="searchQuery" type="text" class="clean-input" placeholder="Tìm kiếm" />
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
                <tr
                  v-for="(item, index) in filteredDiscounts"
                  :key="index"
                  :class="{ 'table-secondary opacity-50': item.status === 'inactive' }"
                >
                  <td><strong>{{ item.code }}</strong></td>
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
    <button
      class="btn btn-outline btn-sm"
      :class="item.status === 'inactive' ? 'btn-secondary' : 'btn-warning'"
    >
      {{ item.status === 'inactive' ? 'Hiện' : 'Ẩn' }}
    </button>
    <router-link
      :to="`/admin/discount/${item.id}`"
      class="btn btn-update btn-sm"
    >
      Sửa
    </router-link>
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

const userId = ref(null)
const { hasPermission } = Permission(userId)

const searchQuery = ref('')
const { discounts, getAllDiscount } = Discounts()
const { categorys, getCategoryForAdmin } = FoodList.setup()

const getCategoryFullName = (id) => {
  const category = categorys.value.find(c => c.id === id)
  if (!category) return '-'

  const parent = categorys.value.find(c => c.id === category.parent_id)
  return parent ? `${category.name}` : category.name
}

const filteredDiscounts = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return discounts.value.filter(
    (d) =>
      d.name.toLowerCase().includes(query) || d.code.toLowerCase().includes(query)
  )
})

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

onMounted(async () => {
  await Promise.all([getAllDiscount(), getCategoryForAdmin()])
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