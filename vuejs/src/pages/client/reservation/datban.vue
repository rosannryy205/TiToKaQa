<template>
    <div class="container py-4">
      <!-- Bộ lọc -->
      <div class="row justify-content-center g-3 mb-4">
        <div class="col-sm-3">
          <label class="form-label">Chọn ngày</label>
          <input type="date" class="form-control" v-model="date" />
        </div>
        <div class="col-sm-3">
          <label class="form-label">Chọn giờ</label>
          <input type="time" class="form-control" v-model="time" />
        </div>
        <div class="col-sm-3">
          <label class="form-label">Số người</label>
          <select class="form-select" v-model="people">
            <option value="2">2 người</option>
            <option value="4">4 người</option>
            <option value="6">6 người</option>
          </select>
        </div>
        <div class="col-sm-2 d-grid align-self-end">
          <button class="btn btn-find" @click="searchTables">Tìm bàn</button>
        </div>
      </div>
  
      <!-- Danh sách bàn -->
      <div class="tables-wrapper">
        <div
          v-for="table in tables"
          :key="table.id"
          class="table-slot"
          :class="{ occupied: table.status === 'occupied' }"
        >
          <div class="chair"></div>
          <button class="btn btn-table" :disabled="table.status === 'occupied'">
            <span class="table-icon" aria-hidden="true">🍽️</span>
            {{ table.name }}
          </button>
          <div class="chair"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        date: "2025-05-22",
        time: "19:00",
        people: "2",
        tables: [
          { id: 1, name: "Bàn 1", status: "free" },
          { id: 2, name: "Bàn 2", status: "occupied" },
          { id: 3, name: "Bàn 3", status: "free" },
          { id: 4, name: "Bàn 4", status: "free" },
          { id: 5, name: "Bàn 5", status: "occupied" },
        ],
      };
    },
    methods: {
      searchTables() {
        // Giả lập tìm bàn - bạn thay bằng logic thật
        alert(
          `Tìm bàn cho ngày: ${this.date}, giờ: ${this.time}, số người: ${this.people}`
        );
      },
    },
  };
  </script>
  
  <style scoped>
  body {
    background-color: #fcecec;
    font-family: Arial, sans-serif;
  }
  
  /* Reset Bootstrap mặc định */
  .form-select,
  .form-control,
  .btn {
    box-shadow: none !important;
    border-radius: 0 !important;
  }
  
  /* Ô tìm kiếm */
  .form-label {
    font-weight: bold;
    font-size: 14px;
  }
  
  .btn-find {
    background-color: #d32f2f;
    color: white;
    font-weight: bold;
    transition: background-color 0.3s ease;
    border-radius: 5px;
  }
  
  .btn-find:hover {
    background-color: #b71c1c;
  }
  
  /* Phần bàn */
  .tables-wrapper {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start; /* Căn hàng đầu dòng */
    gap: 24px;
    margin-top: 30px;
  }
  
  .table-slot {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Căn trái */
    background: white;
    padding: 12px 20px;
    border-radius: 12px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease, transform 0.2s ease;
    width: 140px;
    opacity: 0;
    animation: fadeInUp 0.5s ease forwards;
  }
  
  .table-slot:nth-child(even) {
    animation-delay: 0.1s;
  }
  
  .table-slot:nth-child(odd) {
    animation-delay: 0.2s;
  }
  
  .table-slot:hover {
    box-shadow: 0 6px 12px rgba(211, 47, 47, 0.5);
    transform: translateY(-4px);
  }
  
  .occupied {
    opacity: 0.6;
  }
  
  .chair {
    width: 50px;
    height: 8px;
    background-color: #e0e0e0;
    border-radius: 4px;
    margin: 6px 0;
  }
  
  .btn-table {
    background-color: #d32f2f;
    color: white;
    font-weight: bold;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    min-width: 100px;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  
  .btn-table:hover:not(:disabled) {
    background-color: #b71c1c;
    transform: scale(1.05);
  }
  
  .btn-table:disabled {
    background-color: #999;
    cursor: not-allowed;
  }
  
  .table-icon {
    font-size: 18px;
  }
  
  .table-status {
    margin-top: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #555;
  }
  
  /* Responsive */
  @media (max-width: 576px) {
    .btn-table {
      padding: 8px 16px;
      font-size: 14px;
      min-width: 80px;
    }
    .table-slot {
      width: 100%;
    }
  }
  
  @keyframes fadeInUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  </style>
  