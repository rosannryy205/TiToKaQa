<template>


  <div class="row d-flex text-center">
    <div class="title-shops1 d-sm-block fw-bold mt-5">
      <span>ĐẶT BÀN CÙNG CHÚNG TÔI!</span>
    </div>
  </div>

  <div class="container custom-container">
    <div class="row">
      <div class="col-md-4 booking-image-">
        <img class="img-fluid img-reservation" src="/img/mo-hinh-lau-nuong-truyen-thong.png" alt="Bản đồ bàn"
          style="height: 500px; object-fit: cover" />
      </div>

      <div class="col-md-8 col-12 form-section mt-2">
        <form @submit.prevent="findTable">
          <div class="row g-2 mb-3">
            <div class="col-md-3">
              <input type="date" class="form-control rounded" v-model="date" :min="today" />
            </div>
            <div class="col-md-3">
              <select class="form-control custom-select rounded" v-model="time">
                <option value="">Chọn giờ</option>
                <option v-for="time in filteredTimeOptions" :key="time" :value="time">
                  {{ time }}
                </option>
              </select>
            </div>
            <div class="col-md-3">
              <input type="number" class="form-control rounded" placeholder="Số lượng người" v-model="guest_count"
                min="1" />
            </div>
            <div class="col-md-3">
              <button class="btn btn-danger1 w-100 fw-bold">Tìm bàn</button>
            </div>
          </div>
        </form>

        <hr />
        <div class="fs-6 fw-bold mb-3">Kết quả tìm kiếm</div>

        <div class="table-container" v-if="availableTables.length !== 0">
          <div class="table-block" v-for="ban in availableTables" :key="ban.id" @click="chooseTable(ban.id)">
            <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
              <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="n"></div>
            </div>
            <div class="table-rect" :class="{
              medium: getChairCount(ban.capacity) === 2,
              large: getChairCount(ban.capacity) === 3,
            }">
              B{{ ban.table_number }}
            </div>
            <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
              <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="'b' + n"></div>
            </div>
          </div>
        </div>

        <div v-if="combine == true" class="no-table-alert text-center my-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="none" viewBox="0 0 24 24" stroke="#888">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 9l4-4h10l4 4v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9h18M9 13l3 3 3-3" />
          </svg>
          <p>Vui lòng liên hệ để được xếp bàn!</p>
        </div>

        <div v-if="combine == false" class="no-table-alert text-center my-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="none" viewBox="0 0 24 24" stroke="#888">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 9l4-4h10l4 4v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9h18M9 13l3 3 3-3" />
          </svg>
          <p>Rất tiếc, hiện tại đã hết bàn phù hợp với số lượng khách yêu cầu!</p>
        </div>


        <div v-if="isLoading" class="loader-wrapper">
          <div class="loader"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { User } from '@/stores/user'
import axios from 'axios'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { reactive } from 'vue'
import Swal from 'sweetalert2';
import { Info } from '@/stores/info-order-reservation'
import { onMounted } from 'vue'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
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
    const errors = reactive({})
    const availableTables = ref([])
    const table_id = ref(null)
    const combine = ref(false)
    const { info, getInfo, orderId, formatDateTime } = Info.setup()
    const { form, user } = User.setup()

    const findTable = async () => {
      if (!date.value || !time.value || !guest_count.value) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'info',
          title: 'Vui lòng điền đầy đủ thông tin để tìm bàn!',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
        return
      }
      const selectedDateTime = new Date(`${date.value}T${time.value}:00`)
      try {
        isLoading.value = true

        const reservedFrom = selectedDateTime
        const reservedTo = new Date(reservedFrom.getTime() + 2 * 60 * 60 * 1000)

        const reserved_from = formatDateTime(reservedFrom)
        const reserved_to = formatDateTime(reservedTo)

        const res = await axios.post(`${API_URL}/available-tables`, {
          reserved_from,
          reserved_to,
          number_of_guests: guest_count.value,
        })

        availableTables.value = res.data.tables || []
        combine.value = res.data.combinedGroup
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Tìm bàn thành công!',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      } catch (error) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Lỗi khi lấy danh sách bàn có thể đặt',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
        console.error('Lỗi:', error)
      } finally {
        isLoading.value = false
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
      const result = await Swal.fire({
        title: 'Bạn muốn đặt bàn này?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
      });
      if (result.isConfirmed) {
        try {
          const reservations_time = `${date.value} ${time.value}`

          const res = await axios.post(`${API_URL}/choose-table`, {
            user_id: user.value?.id,
            table_id: table_id,
            reserved_from: reservations_time,
            guest_count: guest_count.value,
          })

          const orderId = res.data.order_id
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Đã đặt bàn thành công!',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
          })
          router.push({
            name: 'reservation-form',
            params: { orderId },
          })
        } catch (error) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Có lỗi xảy ra, vui lòng thử lại sau.',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          console.log(error)
        }
      }
    }

    onMounted(() => {
      for (let hour = 8; hour <= 20; hour++) {
        let hourStr = hour < 10 ? '0' + hour : '' + hour
        timeOptions.value.push(hourStr + ':00')
        if (hour < 21) {
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
      errors,
      info,
      getInfo,
      orderId,
      formatDateTime,
      findTable,
      availableTables,
      filteredTimeOptions,
      getChairCount,
      chooseTable,
      combine
    }
  },
}
</script>
<style scoped>
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
  margin: 2px 0;
}

.loader-wrapper {
  height: 50vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* loader */
.loader {
  width: 50px;
  --b: 8px;
  aspect-ratio: 1;
  border-radius: 50%;
  padding: 1px;
  background: conic-gradient(#0000 10%, #f03355) content-box;
  -webkit-mask:
    repeating-conic-gradient(#0000 0deg, #000 1deg 20deg, #0000 21deg 36deg),
    radial-gradient(farthest-side, #0000 calc(100% - var(--b) - 1px), #000 calc(100% - var(--b)));
  -webkit-mask-composite: destination-in;
  mask-composite: intersect;
  animation: l4 1s infinite steps(10);
}

@keyframes l4 {
  to {
    transform: rotate(1turn);
  }
}

.chair {
  width: 40px;
  height: 6px;
  background-color: #ddd;
  border-radius: 3px;
}

.table-rect {
  background-color: #f4f4f4;
  color: rgb(81, 73, 73);
  padding: 10px 20px;
  border-radius: 10px;
  text-align: center;
  border: 5px solid #ddd;
  min-width: 80px;
  font-weight: bold;
  font-size: 16px;
}

.table-rect.medium {
  min-width: 120px;
}

.table-rect.large {
  min-width: 160px;
}

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

  .title-shops1>span {
    font-size: 25px;
  }

}
</style>
