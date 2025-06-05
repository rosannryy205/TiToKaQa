<template>
  <div>
    <h2>Lịch sử đơn hàng</h2>
    <div style="display: flex; align-items: center; gap: 18px; padding-top: 10px">
      <!-- Tabs -->
      <div class="">
        <input type="date" class="form-control rounded" />
      </div>
      <div class="" style="width: 120px">
        <select class="form-control rounded">
          <option value="">Chọn giờ</option>
          <option></option>
        </select>
      </div>
      <div style="width: 180px">
        <select class="form-control rounded">
          <option value="">Lọc theo trạng thái</option>
          <option>Đã đặt trước</option>
          <option>Đã đặt trước</option>
          <option>Đã đặt trước</option>
        </select>
      </div>
      <div>
        <button
          type="submit"
          class="btn btn-danger1 w-100 form-control rounded"
          style="font-size: 14px; font-weight: 400"
        >
          Tìm bàn
        </button>
      </div>

      <!-- Table Status -->
      <div class="table-status-box">
        <strong>Trạng thái:</strong>
        <div class="status-item"><span class="status-dot billed"></span>Đã đăt trước</div>
        <div class="status-item"><span class="status-dot reservation"></span>Đang sử dụng</div>
      </div>
    </div>
    <hr />
    <div class="col-md-12 form-section mt-2">
      <div class="table-container">
        <div class="table-block" v-for="ban in tables" :key="ban.id">
          <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
            <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="n"></div>
          </div>
          <div
            :class="{
              'table-rect': true,
              medium: getChairCount(ban.capacity) === 2,
              large: getChairCount(ban.capacity) === 3,
              billed: ban.status === 'Đã đặt trước',
              'billed-text': ban.status === 'Đã đặt trước',
              reservation: ban.status === 'Có khách',
              'reservation-text': ban.status === 'Có khách',
            }"
          >
            Bàn {{ ban.table_number }}
          </div>
          <div class="chairs" :class="'ghe-' + getChairCount(ban.capacity)">
            <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="'b' + n"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ref, onMounted } from 'vue'
export default {
  setup() {
    const tables = ref([])

    const getTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/tables')
        tables.value = res.data
        console.log(tables.value)
      } catch (error) {
        console.log(error)
      }
    }

    const getChairCount = (seats) => {
      if (seats <= 2) return 1
      if (seats <= 4) return 2
      return 3
    }

    onMounted(() => {
      getTable()
      getChairCount()
    })

    return {
      tables,
      getChairCount,
      getTable,
    }
  },
}
</script>

<style scoped>
.table-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
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
  /* background-color: #f4f4f4; */
  color: rgb(81, 73, 73);
  padding: 10px 20px;
  border-radius: 10px;
  text-align: center;
  border: 5px solid #ddd;
  min-width: 80px;
}

.table-rect.medium {
  min-width: 120px;
}

.table-rect.large {
  min-width: 160px;
}
.table-status-box {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  border-radius: 12px;
  font-size: 14px;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.status-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.occupied {
  background-color: #f4f4f4; /* blue */
}

.billed {
  background-color: #f1be26; /* yellow */
}
.billed-text,
.reservation-text {
  color: white;
}

.reservation {
  background-color: #c0392b; /* red */
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
