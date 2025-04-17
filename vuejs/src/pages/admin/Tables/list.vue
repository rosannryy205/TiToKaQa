<template>
    <div class="container">
        <h2 class="mb-4 fw-bold">Sơ Đồ Bàn</h2>

        <!-- Tabs bộ lọc trạng thái -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active">Tất cả</a>
            </li>
            <li class="nav-item">
                <a class="nav-link">Bàn trống</a>
            </li>
            <li class="nav-item">
                <a class="nav-link">Có khách</a>
            </li>
        </ul>

        <!-- Bộ lọc khu vực
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <label class="me-2">Khu vực:</label>
                <select class="form-select w-auto d-inline-block">
                    <option>Tất cả</option>
                    <option>Ngoài trời</option>
                    <option>Trong phòng</option>
                </select>
            </div>
        </div> -->

        <!-- Danh sách bàn -->
        <div class="row row-cols-2 row-cols-md-4 g-4">
          <div class="col" v-for="table in tables" :key="table.id">
            <div class="table-card p-3 text-center" :class="table.status">
              <div class="icon-wrap mb-2">
                <i class="bi bi-person-fill"></i>
              </div>
              <h5 class="table-number">Bàn {{ table.table_number }}</h5>
              <p class="status-text">{{ (table.status) }} - {{ table.capacity }} người</p>
            </div>
          </div>

        </div>
    </div>
</template>
<script>
import axios from 'axios';
import { ref, onMounted } from 'vue';
export default{
  setup(){
    const tables = ref([])

    const getTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/tables')
        tables.value = res.data
        console.log(tables.value);

      } catch (error) {
        console.log(error);
      }
    }


    onMounted(() => {
      getTable();

    })

    return{
      tables,
    }
  }
}
</script>


<style scoped>
.bi-person-fill{
  color: #c62c37;
  font-size: 30px;
}
.table-card {
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 20px;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
}
.table-card:hover {
    transform: scale(1.05);
}
.table-number {
    color: black;
    font-weight: bold;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.status-text {
    margin-top: 10px;
}
.area{
    color: #6c757d;
}
.nav-tabs .nav-link {
    cursor: pointer;
}
</style>
