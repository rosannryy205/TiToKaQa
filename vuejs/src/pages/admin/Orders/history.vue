<template>
  <div>
    <h2>Lịch sử đơn hàng</h2>

    <a-tabs v-model:activeKey="activeStatusTab" @change="handleStatusTabChange" style="margin-bottom: 16px;">
      <a-tab-pane key="Tất cả" tab="Tất cả"></a-tab-pane>
      <a-tab-pane key="Chờ xác nhận" tab="Chờ xác nhận"></a-tab-pane>
      <a-tab-pane key="Đã xác nhận" tab="Đã xác nhận"></a-tab-pane>
      <a-tab-pane key="Đang xử lý" tab="Đang xử lý"></a-tab-pane>
      <a-tab-pane key="Đang giao hàng" tab="Đang giao hàng"></a-tab-pane>
      <a-tab-pane key="Giao thành công" tab="Giao thành công"></a-tab-pane>
      <a-tab-pane key="Giao thất bại" tab="Giao thất bại"></a-tab-pane>
      <a-tab-pane key="Đã hủy" tab="Đã hủy"></a-tab-pane>
    </a-tabs>

    <a-row :gutter="[16, 16]" style="margin-bottom: 24px" align="middle">
      <a-col :xs="24" :sm="12" :md="8" :lg="6" style="display: flex; align-items: center;">
        <span style="margin-right: 8px; white-space: nowrap;">Hiển thị:</span>
        <a-select v-model:value="pagination.pageSize" style="width: 80px" @change="handlePageSizeChange">
          <a-select-option :value="5">5</a-select-option>
          <a-select-option :value="10">10</a-select-option>
          <a-select-option :value="20">20</a-select-option>
          <a-select-option :value="50">50</a-select-option>
        </a-select>
      </a-col>
      <a-col :xs="24" :sm="12" :md="8" :lg="6">
        <a-select v-model:value="selectedOrderType" placeholder="Lọc theo loại đơn" style="width: 100%" allow-clear
          @change="handleOrderTypeChange">
          <a-select-option value="Tất cả">Tất cả loại đơn</a-select-option>
          <a-select-option value="Mang về">Mang về</a-select-option>
          <a-select-option value="Đặt bàn">Đặt bàn</a-select-option>
        </a-select>
      </a-col>
      <a-col :xs="24" :sm="24" :md="8" :lg="12">
        <a-input-search v-model:value="searchText" placeholder="Tìm theo Mã ĐH, Tên KH, SĐT..." allow-clear
          @search="handleSearch" @change="e => { if (e.target.value === '') handleSearch('') }" />
      </a-col>
    </a-row>

    <a-table :columns="columns" :data-source="paginatedData" :row-key="record => record.id" :pagination="pagination"
      @change="handleTableChange" bordered :scroll="{ x: 1200 }">
      <template #bodyCell="{ column, record, index }">
        <template v-if="column.key === 'stt'">
          <span>{{ (pagination.current - 1) * pagination.pageSize + index + 1 }}</span>
        </template>
        <template v-if="column.key === 'customerInfo'">
          <div>{{ record.customerInfo.name }}</div>
          <div>{{ record.customerInfo.phone }}</div>
        </template>
        <template v-if="column.key === 'totalAmount'">
          <span>{{ formatCurrency(record.totalAmount) }}</span>
        </template>
        <template v-if="column.key === 'status'">
          <a-tag :color="getStatusColor(record.status)">{{ record.status }}</a-tag>
        </template>
        <template v-if="column.key === 'action'">
          <a-space size="middle">
            <a-button type="link" @click="viewOrderDetails(record)">Xem chi tiết</a-button>
            <a-button type="link" @click="printInvoice(record)">In hóa đơn</a-button>
          </a-space>
        </template>
      </template>
    </a-table>

    <a-modal v-model:open="isDetailModalVisible" title="Chi tiết đơn hàng" @ok="isDetailModalVisible = false"
      :footer="null" width="700px">
      <div v-if="selectedOrder">
        <p><strong>Mã đơn hàng:</strong> {{ selectedOrder.id }}</p>
        <p><strong>Ngày đặt:</strong> {{ selectedOrder.orderDate }}</p>
        <p><strong>Khách hàng:</strong> {{ selectedOrder.customerInfo.name }} - {{ selectedOrder.customerInfo.phone }}
        </p>
        <p><strong>Khu vực - Bàn:</strong> {{ selectedOrder.areaTable }}</p>
        <p><strong>Loại đơn:</strong> {{ selectedOrder.orderType }}</p>
        <p><strong>Trạng thái:</strong>
          <a-select :value="selectedOrder.status" style="width: 160px"
            @change="handleStatusChange(selectedOrder.id, $event)">
            <a-select-option value="Chờ xác nhận">Chờ xác nhận</a-select-option>
            <a-select-option value="Đã xác nhận">Đã xác nhận</a-select-option>
            <a-select-option value="Đang xử lý">Đang xử lý</a-select-option>
            <a-select-option value="Đang giao hàng">Đang giao hàng</a-select-option>
            <a-select-option value="Giao thành công">Giao thành công</a-select-option>
            <a-select-option value="Giao thất bại">Giao thất bại</a-select-option>
            <a-select-option value="Đã hủy">Đã hủy</a-select-option>
          </a-select>
        </p>

        <p><strong>Tổng tiền:</strong> {{ formatCurrency(selectedOrder.totalAmount) }}</p>
        <h4>Các món đã đặt:</h4>
        <a-list bordered :data-source="selectedOrder.items">
          <template #renderItem="{ item }">
            <a-list-item>
              <a-list-item-meta :description="`Số lượng: ${item.quantity} - Đơn giá: ${formatCurrency(item.price)}`">
                <template #title>
                  {{ item.name }}
                </template>
              </a-list-item-meta>
              <div>Thành tiền: {{ formatCurrency(item.quantity * item.price) }}</div>
            </a-list-item>
          </template>
        </a-list>
      </div>
    </a-modal>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import axios from 'axios';
import { onMounted } from 'vue';
import { message } from 'ant-design-vue';
// Import các icon nếu cần cho nút hoặc các phần khác
// import { EyeOutlined, PrinterOutlined } from '@ant-design/icons-vue';


const orders = ref([]);

const fetchOrders = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/get_all_orders`);
    const apiOrders = response.data.orders;



    orders.value = apiOrders.map(order => {
      return {
        id: order.id.toString(),
        customerInfo: {
          name: order.guest_name,
          phone: order.guest_phone,
        },
        orderDate: order.order_time,
        areaTable: order.tables?.map(t => `Bàn ${t.table_number}`).join(', ') || 'Mang về',
        orderType: order.tables?.length > 0 ? 'Đặt bàn' : 'Mang về',
        totalAmount: parseFloat(order.total_price),
        status: order.order_status,
        items: order.details.map(detail => ({
          name: detail.food_name || `Món #${detail.food_id}`,
          quantity: detail.quantity,
          price: parseFloat(detail.price),
          toppings: detail.toppings?.map(t => ({
            name: t.topping_name || `Topping #${t.food_toppings_id}`,
            price: parseFloat(t.price)
          })) || []
        })),
      };
    });

    pagination.total = orders.value.length;

  } catch (error) {
    console.error('Lỗi khi lấy đơn hàng:', error);
  }
};

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

const handleStatusChange = async (orderId, newStatus) => {
  const order = orders.value.find(order => order.id === orderId);
  if (!order) return;

  const currentStatus = order.status;

  // Không cho cập nhật từ trạng thái đã hủy hoặc giao thất bại
  if (currentStatus === 'Đã hủy' || currentStatus === 'Giao thất bại' || currentStatus === 'Giao thành công') {
    message.warning('Không thể cập nhật trạng thái.');
    return;
  }

  // Kiểm tra newStatus hợp lệ
  if (!allowedStatuses.includes(newStatus)) {
    message.warning('Trạng thái không hợp lệ.');
    return;
  }

  // Logic kiểm soát cập nhật trạng thái:

  // 1. Nếu đang giao hàng, cho phép nhảy lên giao thành công hoặc giao thất bại
  if (currentStatus === 'Đang giao hàng') {
    if (newStatus === 'Giao thành công' || newStatus === 'Giao thất bại') {
      // Cho phép
    } else if (statusOrder[newStatus] !== statusOrder[currentStatus]) {
      // Nếu không phải 2 trạng thái trên mà cố nhảy khác thì lỗi
      message.warning('Chỉ có thể cập nhật trạng thái thành công hoặc thất bại khi đang giao hàng.');
      return;
    }
  } else {
    // 2. Nếu đang ở chờ xác nhận hoặc đã xác nhận thì có thể hủy
    if (newStatus === 'Đã hủy') {
      if (currentStatus !== 'Chờ xác nhận' && currentStatus !== 'Đã xác nhận') {
        message.warning('Chỉ có thể hủy đơn khi đơn ở trạng thái chờ xác nhận hoặc đã xác nhận.');
        return;
      }
    } else {
      // 3. Không được nhảy bậc (bỏ qua trạng thái bằng nhau)
      if (statusOrder[newStatus] !== statusOrder[currentStatus] + 1) {
        message.warning('Không thể nhảy trạng thái không theo thứ tự.');
        return;
      }
    }
  }

  try {
    const response = await axios.put(`http://127.0.0.1:8000/api/update/${orderId}/status`, {
      order_status: newStatus,
    });

    if (response.data.success) {
      // Cập nhật trạng thái trong orders và selectedOrder
      const index = orders.value.findIndex(order => order.id === orderId);
      if (index !== -1) {
        orders.value[index].status = newStatus;
      }
      if (selectedOrder.value?.id === orderId) {
        selectedOrder.value.status = newStatus;
      }
      message.success('Cập nhật trạng thái thành công');
    } else {
      message.error('Cập nhật trạng thái thất bại');
    }
  } catch (error) {
    console.error('Lỗi cập nhật trạng thái:', error);
    message.error('Có lỗi xảy ra khi cập nhật trạng thái');
  }
};







const activeStatusTab = ref('Tất cả'); // Trạng thái đang được chọn trên tab
const selectedOrderType = ref('Tất cả'); // Loại đơn hàng đang được chọn
const searchText = ref('');

// Modal state
const isDetailModalVisible = ref(false);
const selectedOrder = ref(null);

const columns = [
  { title: 'STT', key: 'stt', width: 60, fixed: 'left' },
  { title: 'Mã ĐH', dataIndex: 'id', key: 'orderId', width: 100, fixed: 'left', sorter: (a, b) => a.id.localeCompare(b.id) },
  { title: 'Khu vực-Bàn', dataIndex: 'areaTable', key: 'areaTable', width: 150 },
  { title: 'Thông tin KH', key: 'customerInfo', width: 150 },
  {
    title: 'Loại đơn', dataIndex: 'orderType', key: 'orderType', width: 80,
    filters: [
      { text: 'Đặt bàn', value: 'Đặt bàn' },
      { text: 'Mang về', value: 'Mang về' },
    ],
    onFilter: (value, record) => record.orderType.includes(value),
  },
  { title: 'Tổng tiền', dataIndex: 'totalAmount', key: 'totalAmount', width: 100, sorter: (a, b) => a.totalAmount - b.totalAmount, align: 'right' },
  {
    title: 'Trạng thái', dataIndex: 'status', key: 'status', width: 120, fixed: 'right',
    filters: [
      { text: 'Chờ xác nhận', value: 'Chờ xác nhận' },
      { text: 'Đã xác nhận', value: 'Đã xác nhận' },
      { text: 'Đang xử lý', value: 'Đang xử lý' },
      { text: 'Đang giao hàng', value: 'Đang giao hàng' },
      { text: 'Giao thành công', value: 'Giao thành công' },
      { text: 'Giao thất bại', value: 'Giao thất bại' },
      { text: 'Đã hủy', value: 'Đã hủy' },
    ],
    onFilter: (value, record) => record.status.includes(value),
  },
  { title: 'Hành động', key: 'action', width: 200, fixed: 'right', align: 'center' },
];

// Cấu hình phân trang
const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: orders.value.length,
  showSizeChanger: false, // Đã có selector riêng
  showTotal: (total, range) => `${range[0]}-${range[1]} của ${total} mục`,
});

// Dữ liệu được lọc dựa trên các điều kiện
const filteredData = computed(() => {
  let tempData = [...orders.value];

  // Lọc theo trạng thái từ Tabs
  if (activeStatusTab.value !== 'Tất cả') {
    tempData = tempData.filter(order => order.status === activeStatusTab.value);
  }

  // Lọc theo loại đơn từ Select
  if (selectedOrderType.value !== 'Tất cả') {
    tempData = tempData.filter(order => order.orderType === selectedOrderType.value);
  }

  // Lọc theo tìm kiếm (Mã ĐH, Tên KH, SĐT)
  if (searchText.value) {
    const lowerSearchText = searchText.value.toLowerCase();
    tempData = tempData.filter(order =>
      order.id.toLowerCase().includes(lowerSearchText) ||
      order.customerInfo.name.toLowerCase().includes(lowerSearchText) ||
      order.customerInfo.phone.includes(lowerSearchText) // SĐT thường không phân biệt hoa thường
    );
  }

  // Cập nhật tổng số mục cho phân trang sau khi lọc
  // Dùng setTimeout để tránh lỗi lặp vô hạn khi computed property cập nhật reactive property
  setTimeout(() => {
    pagination.total = tempData.length;
    if (pagination.current * pagination.pageSize > tempData.length && tempData.length > 0) {
      pagination.current = Math.ceil(tempData.length / pagination.pageSize) || 1;
    } else if (tempData.length === 0) {
      pagination.current = 1;
    }
  }, 0);

  return tempData;
});

// Dữ liệu hiển thị trên trang hiện tại (sau khi đã lọc và phân trang)
const paginatedData = computed(() => {
  const start = (pagination.current - 1) * pagination.pageSize;
  const end = start + pagination.pageSize;
  return filteredData.value.slice(start, end);
});


// Hàm xử lý
const handleStatusTabChange = (key) => {
  activeStatusTab.value = key;
  pagination.current = 1; // Reset về trang đầu khi đổi tab
};

const handleOrderTypeChange = (value) => {
  selectedOrderType.value = value || 'Tất cả'; // Nếu clear thì về Tất cả
  pagination.current = 1;
};

const handleSearch = (value) => {
  searchText.value = value;
  pagination.current = 1;
}

const handlePageSizeChange = (size) => {
  pagination.pageSize = size;
  pagination.current = 1;
};

const handleTableChange = (pager, filters, sorter) => {
  pagination.current = pager.current;
  pagination.pageSize = pager.pageSize;
  // Logic sắp xếp có thể được thêm ở đây nếu bạn muốn sắp xếp dữ liệu gốc (orders.value)
  // Hoặc để a-table tự sắp xếp dữ liệu trên trang hiện tại (filteredData)
};

const getStatusColor = (status) => {
  switch (status) {
    case 'Chờ xác nhận': return 'orange';
    case 'Đã xác nhận': return 'blue';
    case 'Đang xử lý': return 'processing'; // AntD processing có animation
    case 'Đang giao hàng': return 'geekblue';
    case 'Giao thành công': return 'green';
    case 'Giao thất bại': return 'red';
    case 'Đã hủy': return 'default';
    default: return 'default';
  }
};

const formatCurrency = (value) => {
  if (typeof value !== 'number') return value;
  return value.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
};

const viewOrderDetails = (record) => {
  selectedOrder.value = record;
  isDetailModalVisible.value = true;
  console.log('Xem chi tiết:', record);
};

const printInvoice = (record) => {
  console.log('In hóa đơn cho đơn hàng:', record);
  // Logic in hóa đơn (có thể mở một tab mới với giao diện in)
  alert(`Đang chuẩn bị in hóa đơn cho đơn hàng ${record.id}`);
};

onMounted(() => {
  fetchOrders();
});

</script>

<style scoped></style>
