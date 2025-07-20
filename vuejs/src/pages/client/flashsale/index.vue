<template>
    <div class="flash-sale-container">
      <!-- Header -->
      <div class="flash-sale-header">
        <h1>🔥 TITOKAQA FLASH SALE</h1>
        <div class="time-slots mt-3">
          <div
            v-for="(slot, index) in timeSlots"
            :key="index"
            :class="['slot', { active: currentSlot === slot }]"
          >
            {{ slot }} |
            <span>{{ currentSlot === slot ? 'Đang diễn ra' : 'Sắp diễn ra' }}</span>
          </div>
        </div>
        <div class="tags">
          <div class="tag selected">⚡ DEAL HOT HÔM NAY</div>
          <div class="tag">🚀 ĐANG BÁN CHẠY</div>
          <div class="tag">50% GIẢM</div>
          <div class="tag">25% GIẢM</div>
        </div>
      </div>
      <div class="countdown">
        ⏰ KẾT THÚC TRONG <span id="countdown-timer">{{ countdown }}</span>
      </div>
      <div class="product-list">
        <div class="product-card" v-for="(product, index) in products" :key="index">
          <img :src="product.image" :alt="product.name" />
          <div class="product-info">
            <div class="discount-tag">-{{ product.discount_percent }}%</div>
            <h3>{{ product.name }}</h3>
            <p>{{ product.store }}</p>
            <div class="price">
              <del>{{ formatCurrency(product.original_price) }}</del>
              <strong>{{ formatCurrency(product.sale_price) }}</strong>
            </div>
            <div class="progress-bar">
              <div class="fill" :style="{ width: product.progress + '%' }"></div>
              <span>{{ product.progress_label }}</span>
            </div>
          </div>
          <button class="buy-btn">Mua ngay</button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import dayjs from 'dayjs'
  
  const timeSlots = ['13:00', '16:00', '19:00', '21:00']
  
  const products = ref([
    {
      name: 'COMBO 179K',
      image: 'https://via.placeholder.com/300x200',
      store: 'Phúc Long Coopmart',
      original_price: 230000,
      sale_price: 179000,
      discount_percent: 22,
      progress: 80,
      progress_label: 'ĐANG BÁN CHẠY',
    },
    {
      name: 'Trà Sữa Trân Châu Phô Mai',
      image: 'https://via.placeholder.com/300x200',
      store: 'Trà Sữa Maycha',
      original_price: 35000,
      sale_price: 24500,
      discount_percent: 30,
      progress: 40,
      progress_label: '8 ĐÃ BÁN',
    },
    {
      name: 'Trà Sữa Ba Anh Em',
      image: 'https://via.placeholder.com/300x200',
      store: 'ToCoToCo',
      original_price: 39000,
      sale_price: 10000,
      discount_percent: 74,
      progress: 60,
      progress_label: 'BÁN CHẠY',
    },
  ])
  
  const currentSlot = computed(() => {
    const now = dayjs()
    for (let i = timeSlots.length - 1; i >= 0; i--) {
      const [hour] = timeSlots[i].split(':').map(Number)
      if (now.hour() >= hour) return timeSlots[i]
    }
    return timeSlots[0]
  })
  
  const countdown = ref('')
  onMounted(() => {
    setInterval(() => {
      const [hour] = currentSlot.value.split(':').map(Number)
      const end = dayjs().hour(hour + 1).startOf('hour')
      const diff = end.diff(dayjs(), 'second')
  
      if (diff <= 0) {
        countdown.value = '00:00:00'
        return
      }
  
      const h = String(Math.floor(diff / 3600)).padStart(2, '0')
      const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0')
      const s = String(diff % 60).padStart(2, '0')
  
      countdown.value = `${h}:${m}:${s}`
    }, 1000)
  })
  
  function formatCurrency(value) {
    return value.toLocaleString('vi-VN') + 'đ'
  }
  </script>
  
  <style scoped>
  .flash-sale-container {
    max-width: 1200px;
    margin: auto;
    padding: 50px 20px 100px;
  }
  
  .flash-sale-header h1 {
    font-size: 28px;
    color: #d9363e;
    margin-bottom: 16px;
  }
  
  .time-slots {
    display: flex;
    gap: 15px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }
  
  .slot {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    text-align: center;
    font-size: 14px;
  }
  
  .slot.active {
    border-color: #d9363e;
    background: #fff0f0;
    color: #d9363e;
    font-weight: bold;
  }
  
  .tags {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }
  
  .tag {
    background: #f2f2f2;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    cursor: pointer;
  }
  
  .tag.selected {
    background: #d9363e;
    color: white;
  }
  
  .countdown {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 24px;
    color: #d9363e;
  }
  
  .product-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
  }
  
  .product-card {
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
    background: white;
    padding: 10px;
    transition: box-shadow 0.2s;
  }
  
  .product-card:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
  }
  
  .product-card img {
    width: 100%;
    height: auto;
    border-radius: 6px;
  }
  
  .product-info {
    padding: 12px 0;
  }
  
  .discount-tag {
    background: #ffc107;
    color: #fff;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
    display: inline-block;
    margin-bottom: 5px;
  }
  
  .price {
    margin-top: 6px;
  }
  
  .price del {
    color: #999;
    margin-right: 8px;
  }
  
  .price strong {
    color: #d9363e;
    font-size: 16px;
  }
  
  .progress-bar {
    background: #f2f2f2;
    height: 10px;
    border-radius: 5px;
    margin: 12px 0;
    position: relative;
  }
  
  .progress-bar .fill {
    background: linear-gradient(to right, #f35, #d9363e);
    height: 100%;
    border-radius: 5px;
  }
  
  .progress-bar span {
    position: absolute;
    top: -20px;
    right: 0;
    font-size: 12px;
    color: #d9363e;
  }
  
  .buy-btn {
    background: #d9363e;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 16px;
  }
  
  .buy-btn:hover {
    background: #b72d31;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .flash-sale-header h1 {
      font-size: 22px;
    }
  
    .tag,
    .slot {
      font-size: 12px;
      padding: 6px 10px;
    }
  
    .product-card {
      padding: 8px;
    }
  
    .buy-btn {
      font-size: 14px;
    }
  }
  </style>
  
  