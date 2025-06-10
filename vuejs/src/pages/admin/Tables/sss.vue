<template>
<div class="tabs-container">
  <ul class="tab-list">
    <li class="tab-item active" data-key="Tất cả">Tất cả</li>
    <li class="tab-item" data-key="Chờ xác nhận">Chờ xác nhận</li>
    <li class="tab-item" data-key="Đã xác nhận">Đã xác nhận</li>
    <li class="tab-item" data-key="Đang xử lý">Đang xử lý</li>
    <li class="tab-item" data-key="Đang giao hàng">Đang giao hàng</li>
    <li class="tab-item" data-key="Giao thành công">Giao thành công</li>
    <li class="tab-item" data-key="Giao thất bại">Giao thất bại</li>
    <li class="tab-item" data-key="Đã hủy">Đã hủy</li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="Tất cả-content">Nội dung cho tab Tất cả</div>
    <div class="tab-pane" id="Chờ xác nhận-content">Nội dung cho tab Chờ xác nhận</div>
    <div class="tab-pane" id="Đã xác nhận-content">Nội dung cho tab Đã xác nhận</div>
    <div class="tab-pane" id="Đang xử lý-content">Nội dung cho tab Đang xử lý</div>
    <div class="tab-pane" id="Đang giao hàng-content">Nội dung cho tab Đang giao hàng</div>
    <div class="tab-pane" id="Giao thành công-content">Nội dung cho tab Giao thành công</div>
    <div class="tab-pane" id="Giao thất bại-content">Nội dung cho tab Giao thất bại</div>
    <div class="tab-pane" id="Đã hủy-content">Nội dung cho tab Đã hủy</div>
  </div>
</div>zz

</template>
<script setup>
import { ref, computed, reactive } from 'vue';
import axios from 'axios';
import { onMounted } from 'vue';
import { message } from 'ant-design-vue';


const allowedStatuses = [
  'Chờ xác nhận',
  'Đã xác nhận',
  'Đang xử lý',
  'Đang giao hàng',
  'Giao thành công',
  'Giao thất bại',
  'Đã hủy'
];

// Quy ước thứ tự trạng thái (để kiểm soát nhảy bậc và lùi)
const statusOrder = {
  'Chờ xác nhận': 1,
  'Đã xác nhận': 2,
  'Đang xử lý': 3,
  'Đang giao hàng': 4,
  'Giao thành công': 5,
  'Giao thất bại': 5,  // cùng bậc với giao thành công
  'Đã hủy': 6
};

const activeStatusTab = ref('Tất cả'); // Trạng thái đang được chọn trên tab



</script>
