<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>
  <div class="d-flex justify-content-between">
    <h3 class="text-danger fw-bold">Thêm đơn đặt bàn</h3>
    <div>
      <a href="#" class="btn btn-outline-secondary rounded-0">
        <i class="bi bi-arrow-counterclockwise"></i> Quay lại
      </a>
    </div>
  </div>
  <form class="row mt-2 d-flex justify-content-center">
    <div class="col">
      <div class="card rounded-0 border-0 shadow mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col mb-3">
              <label for="name" class="form-label"
                >Tên khách hàng <span class="text-danger">*</span></label
              >
              <input type="text" class="form-control rounded-0" id="name" required />
            </div>
            <div class="col mb-3">
              <label for="name" class="form-label"
                >Số điện thoại <span class="text-danger">*</span></label
              >
              <input type="text" class="form-control rounded-0" id="name" required />
            </div>
          </div>
          <div class="row">
            <div class="col mb-3">
              <label for="category" class="form-label"
                >Email <span class="text-danger">*</span></label
              >
              <div class="input-group">
                <input type="text" class="form-control rounded-0" id="name" required />
              </div>
            </div>

            <div class="col mb-3">
              <label for="category" class="form-label"
                >Ghi chú <span class="text-danger">*</span></label
              >
              <div class="input-group">
                <textarea class="form-control rounded-0" id="description" rows="1"></textarea>
              </div>
            </div>
          </div>
          <div class="col mb-3">
            <label for="category" class="form-label"
              >Tìm bàn <span class="text-danger">*</span></label
            >
            <form @change="findTable">
              <div class="row g-2 mb-3">
                <div class="col-md-4">
                  <input type="date" class="form-control rounded" v-model="date" :min="today" />
                </div>
                <div class="col-md-4">
                  <select class="form-control rounded" v-model="time">
                    <option value="">Chọn giờ</option>
                    <option v-for="time in filteredTimeOptions" :key="time" :value="time">
                      {{ time }}
                    </option>
                  </select>
                </div>
                <div class="col-md-4">
                  <input
                    type="number"
                    class="form-control rounded"
                    placeholder="Số lượng người"
                    v-model="guest_count"
                  />
                </div>

                <div class="table-container">
                  <div
                    class="table-block"
                    v-for="ban in availableTables"
                    :key="ban.id"
                    @click="chooseTable(ban.id)"
                  >
                    <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
                      <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="n"></div>
                    </div>
                    <div
                      class="table-rect"
                      :class="{
                        medium: getChairCount(ban.capacity) === 2,
                        large: getChairCount(ban.capacity) === 3,
                      }"
                    >
                      Bàn {{ ban.name || ban.id }}
                    </div>
                    <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
                      <div
                        class="chair"
                        v-for="n in getChairCount(ban.capacity)"
                        :key="'b' + n"
                      ></div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Thêm món</label>
            <div class="table-responsive d-none d-lg-block">
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th><input type="checkbox" /></th>
                    <th>Món ăn</th>
                    <th>Giá bán</th>
                    <th>Số lượng</th>
                    <th>Tuỳ chọn</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type="checkbox" /></td>
                    <td>
                      <img
                        src="/img/food/mykimchihaisan.webp"
                        alt="Mỳ kim chi hải sản"
                        class="me-2 img_thumbnail"
                      />

                      Combo 1
                    </td>
                    <td>25,000 VNĐ</td>
                    <td>
                      <div class="qty-control px-2 py-1">
                        <button type="button" class="btn-sm" style="background-color: #fff">
                          -
                        </button>
                        <span>1</span>
                        <button type="button" class="btn-sm" style="background-color: #fff">
                          +
                        </button>
                      </div>
                    </td>
                    <td class="d-flex justify-content-center gap-2">
                      <button class="btn btn-danger-delete">Xoá</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button
              class="btn btn-danger-delete"
              data-bs-toggle="modal"
              data-bs-target="#menuModal"
            >
              Thêm món
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
  <button type="button" class="btn btn-danger1 themsp">+ Thêm Combo</button>
</template>

<script>
import { User } from '@/stores/user'
import axios from 'axios'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import { Info } from '@/stores/info-order-reservation'
import { onMounted } from 'vue'
export default {
  setup() {
    const isLoading = ref(false)
    const today = new Date().toISOString().split('T')[0]
    const date = ref()
    const timeOptions = ref([])
    const time = ref('')
    const note = ref('')
    const guest_count = ref(null)
    const router = useRouter()
    const availableTables = ref([])
    const table_id = ref(null)

    const { info, getInfo, orderId, formatDateTime } = Info.setup()
    const { form, user } = User.setup()

    const findTable = async () => {
      if (date.value && time.value && guest_count.value) {
        const selectedDateTime = new Date(`${date.value}T${time.value}:00`)
        try {
          isLoading.value = true

          const reservedFrom = selectedDateTime
          const reservedTo = new Date(reservedFrom.getTime() + 2 * 60 * 60 * 1000)

          const reserved_from = formatDateTime(reservedFrom)
          const reserved_to = formatDateTime(reservedTo)

          const res = await axios.post('http://127.0.0.1:8000/api/available-tables', {
            reserved_from,
            reserved_to,
            number_of_guests: guest_count.value,
          })

          availableTables.value = res.data.tables || []

          toast.success('Tìm bàn thành công!')
        } catch (error) {
          toast.error('Lỗi khi lấy danh sách bàn có thể đặt')
          console.error('Lỗi:', error)
        } finally {
          isLoading.value = false
        }
      }
    }

    const getChairCount = (seats) => {
      if (seats <= 2) return 1
      if (seats <= 4) return 2
      return 3
    }

    const filteredTimeOptions = computed(() => {
      if (!date.value) {
        return timeOptions.value
      }

      const selectedDate = new Date(date.value)
      const now = new Date()

      if (selectedDate.toDateString() === now.toDateString()) {
        return timeOptions.value.filter((timeStr) => {
          const [hours, minutes] = timeStr.split(':').map(Number)
          const timeDate = new Date(selectedDate)
          timeDate.setHours(hours, minutes, 0)

          return timeDate > now
        })
      }

      return timeOptions.value
    })

    const chooseTable = async (table_id) => {
      try {
        const reservations_time = `${date.value} ${time.value}`

        const res = await axios.post('http://127.0.0.1:8000/api/choose-table', {
          user_id: user.value?.id,
          table_id: table_id,
          reserved_from: reservations_time,
          guest_count: guest_count.value,
        })

        const orderId = res.data.order_id
        router.push({
          name: 'reservation-form',
          params: { orderId },
        })
      } catch (error) {
        toast.error('Có lỗi xảy ra, vui lòng thử lại sau.')
        console.log(error)
      }
    }

    onMounted(() => {
      for (let hour = 1; hour <= 21; hour++) {
        let hourStr = hour < 10 ? '0' + hour : '' + hour
        timeOptions.value.push(hourStr + ':00')
        if (hour !== 20) {
          timeOptions.value.push(hourStr + ':30')
        }
      }
    })
    return {
      time,
      today,
      timeOptions,
      note,
      guest_count,
      table_id,
      form,
      user,
      date,
      isLoading,
      info,
      getInfo,
      orderId,
      formatDateTime,
      findTable,
      availableTables,
      filteredTimeOptions,
      getChairCount,
      chooseTable,
    }
  },
}
</script>
<style>
.themsp {
  width: 200px;
}

.img_thumbnail {
  width: 50px;
}

.btn-danger-delete {
  background-color: #c92c3c;
  color: white;
}

.btn-danger-delete:hover {
  background-color: #a51928;
  color: white;
}
.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(148, 142, 142, 0.8);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.table-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  margin-top: 20px;
}

.table-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}
.table-block:hover {
  cursor: pointer;
}
.chairs {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin: 5px 0;
}

.chair {
  width: 40px;
  height: 6px;
  background-color: #ddd;
  border-radius: 3px;
}

.table-rect {
  color: rgb(81, 73, 73);
  padding: 10px 20px;
  border-radius: 10px;
  text-align: center;
  font-weight: bold;
  border: 5px solid #ddd;
  min-width: 80px;
  background-color: #f4f4f4; /* blue */
}

.table-rect.medium {
  min-width: 120px;
}

.table-rect.large {
  min-width: 160px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .table-container {
    gap: 10px;
  }

  .table-block {
    flex: 1 1 100px;
  }

  .chair {
    width: 30px;
  }

  .table-rect {
    font-size: 0.85rem;
  }
}
</style>
