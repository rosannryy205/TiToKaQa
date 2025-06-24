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
          <a-select-option :value="100">100</a-select-option>
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
        <a-input-search v-model:value="searchText" placeholder="Tìm kiếm theo tên KH, SĐT" allowClear
          enter-button="Tìm" />

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
        <p><strong>Bàn:</strong> {{ selectedOrder.areaTable }}</p>
        <p><strong>Loại đơn:</strong> {{ selectedOrder.orderType }}</p>

        <!--Trạng thái thanh toán -->
        <p><strong>Trạng thái thanh toán:</strong> {{ selectedOrder.paymentStatus }}</p>

        <!--Phương thức thanh toán -->
        <p><strong>Phương thức thanh toán:</strong> {{ selectedOrder.paymentMethod }}</p>

        <p><strong>Trạng thái:</strong>
          <a-select :value="selectedOrder.status" style="width: 160px"
            @change="handleStatusChange(selectedOrder.id, $event)">
            <a-select-option v-for="status in allowedStatuses" :key="status" :value="status"
              :disabled="isStatusDisabled(status)">
              {{ status }}
            </a-select-option>
          </a-select>
        </p>

        <p><strong>Tổng tiền:</strong> {{ formatCurrency(selectedOrder.totalAmount) }}</p>
        <h4>Các món đã đặt:</h4>
        <a-list bordered :data-source="selectedOrder.items">
          <template #renderItem="{ item }">
            <a-list-item>
              <a-list-item-meta :description="`Số lượng: ${item.quantity} - Đơn giá: ${formatCurrency(item.price)}`">
                <template #title>
                  <div>{{ item.name }}</div>

                  <ul v-if="item.toppings.length" style="margin: 0; padding-left: 16px;">
                    <li v-for="(topping, index) in item.toppings" :key="index">
                      {{ topping.name }}<span v-if="topping.price"> - {{ formatCurrency(topping.price) }}</span>
                    </li>
                  </ul>
                </template>
              </a-list-item-meta>
            </a-list-item>
          </template>
        </a-list>

        <!-- Chỉ hiển thị tổng thành tiền một lần bên dưới -->
        <div style="text-align: right; font-weight: bold; margin-top: 16px;">
          <div v-show="selectedOrder.reduce_money">
            Voucher: {{ formatCurrency(selectedOrder.reduce_money) }}
          </div>
          <div>
            Thành tiền: {{ formatCurrency(selectedOrder.totalAmount) }}
          </div>
        </div>


      </div>
    </a-modal>

  </div>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue';
import axios from 'axios';
import { onMounted } from 'vue';
import { message } from 'ant-design-vue';
// Import các icon nếu cần cho nút hoặc các phần khác
// import { EyeOutlined, PrinterOutlined } from '@ant-design/icons-vue';


const orders = ref([]);
const ordersRaw = ref([]);


const fetchOrders = async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/get_all_orders`);
    const apiOrders = response.data.orders;

    const fillterOrder = apiOrders.filter(order => order.type_order !== 'takeaway');

    orders.value = fillterOrder.map(order => {
      return {
        id: order.id.toString(),
        customerInfo: {
          name: order.guest_name,
          phone: order.guest_phone,
        },
        orderDate: order.order_time,
        areaTable: order.tables?.map(t => `Bàn ${t.table_number}`).join(', ') || 'Trống',
        orderType: order.tables?.length > 0 ? 'Đặt bàn' : 'Mang về',
        totalAmount: parseFloat(order.total_price),
        status: order.order_status,
        reduce_money: parseFloat(order.money_reduce) || 0,
        paymentMethod: order.payment?.payment_method || 'Chưa cập nhật',
        paymentStatus: order.payment?.payment_status || 'Chưa cập nhật',
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

    ordersRaw.value = [...orders.value];


    pagination.total = orders.value.length;

  } catch (error) {
    console.error('Lỗi khi lấy đơn hàng:', error);
  }
};

const allowedStatuses = [
  'Chờ xác nhận',
  'Đã xác nhận',
  'Đang xử lý',
  'Bắt đầu giao',
  // 'Đang giao hàng',
  // 'Giao thành công',
  // 'Giao thất bại',
  // 'Đã hủy'
];

// Quy ước thứ tự trạng thái (để kiểm soát nhảy bậc và lùi)
const statusOrder = {
  'Chờ xác nhận': 1,
  'Đã xác nhận': 2,
  'Đang xử lý': 3,
  'Bắt đầu giao': 4,
  'Đang giao hàng': 5,
  'Giao thành công': 6,
  'Giao thất bại': 6,  // cùng bậc với giao thành công
  'Đã hủy': 7
};

const isStatusDisabled = (status) => {
  const currentLevel = statusOrder[selectedOrder.value.status]; // trạng thái hiện tại của đơn
  const optionLevel = statusOrder[status];                      // trạng thái đang xét (option trong dropdown)
  return optionLevel < currentLevel;
};

const handleStatusChange = async (orderId, newStatus) => {
  const order = orders.value.find(order => order.id === orderId);
  if (!order) return;

  const currentStatus = order.status;

  // Không cho cập nhật từ trạng thái đã hủy hoặc giao thất bại hoặc giao thành công
  if (['Đã hủy', 'Giao thất bại', 'Giao thành công'].includes(currentStatus)) {
    message.warning('Không thể cập nhật trạng thái.');
    return;
  }

  // Kiểm tra newStatus hợp lệ
  if (!allowedStatuses.includes(newStatus)) {
    message.warning('Trạng thái không hợp lệ.');
    return;
  }

  // Logic kiểm soát cập nhật trạng thái
  if (currentStatus === 'Đang giao hàng') {
    if (!['Giao thành công', 'Giao thất bại'].includes(newStatus)) {
      message.warning('Chỉ có thể cập nhật trạng thái thành công hoặc thất bại khi đang giao hàng.');
      return;
    }
  } else {
    if (newStatus === 'Đã hủy') {
      if (!['Chờ xác nhận', 'Đã xác nhận'].includes(currentStatus)) {
        message.warning('Chỉ có thể hủy đơn khi đơn ở trạng thái chờ xác nhận hoặc đã xác nhận.');
        return;
      }
    } else if (currentStatus !== 'Đang giao hàng' && !['Giao thành công', 'Giao thất bại'].includes(newStatus) && statusOrder[newStatus] !== statusOrder[currentStatus] + 1) {
      message.warning('Không thể nhảy trạng thái không theo thứ tự.');
      return;
    }
  }

  try {
    const response = await axios.put(`http://127.0.0.1:8000/api/update/${orderId}/status`, {
      order_status: newStatus,
    });

    if (response.data.success) {
      const index = orders.value.findIndex(order => order.id === orderId);
      if (index !== -1) {
        orders.value[index].status = newStatus;
      }
      if (selectedOrder.value?.id === orderId) {
        selectedOrder.value.status = newStatus;
      }
      message.success('Cập nhật trạng thái thành công');
      if (['Giao thành công', 'Giao thất bại', 'Đã hủy'].includes(newStatus)) {
        await fetchOrders();
        const updatedOrder = orders.value.find(order => order.id === orderId);
        if (updatedOrder) {
          selectedOrder.value = { ...updatedOrder };
        }
      }
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

watch(searchText, () => {
  pagination.current = 1; // reset trang về đầu
});


// Modal state
const isDetailModalVisible = ref(false);
const selectedOrder = ref(null);

const columns = [
  { title: 'STT', key: 'stt', width: 50, fixed: 'center', align: 'center' },
  { title: 'Mã ĐH', dataIndex: 'id', key: 'orderId', width: 70, fixed: 'center', sorter: (a, b) => a.id.localeCompare(b.id), align: 'center' },
  { title: 'Bàn số', dataIndex: 'areaTable', key: 'areaTable', width: 80, align: 'center' },
  { title: 'Thông tin KH', key: 'customerInfo', width: 150, align: 'center' },
  {
    title: 'Loại đơn', dataIndex: 'orderType', key: 'orderType', width: 80, align: 'center',
    filters: [
      { text: 'Đặt bàn', value: 'Đặt bàn' },
      { text: 'Mang về', value: 'Mang về' },
    ],
    onFilter: (value, record) => record.orderType.includes(value),
  },
  {
    title: 'Trạng thái TT', dataIndex: 'paymentStatus', key: 'paymentStatus', width: 130, align: 'center',
    filters: [
      { text: 'Chưa thanh toán', value: 'Chưa thanh toán' },
      { text: 'Đã thanh toán', value: 'Đã thanh toán' },
      { text: 'Thanh toán thất bại', value: 'Thanh toán thất bại' },
    ],
    onFilter: (value, record) => record.paymentStatus.includes(value),
  },
  {
    title: 'Phương thức TT', dataIndex: 'paymentMethod', key: 'paymentMethod', width: 130, align: 'center',
    filters: [
      { text: 'COD', value: 'COD' },
      { text: 'VNPAY', value: 'VNPAY' },
      { text: 'MOMO', value: 'MOMO' },
    ],
    onFilter: (value, record) => record.paymentMethod.includes(value),
  },
  {
    title: 'Tổng tiền', dataIndex: 'totalAmount', key: 'totalAmount', width: 100,
    sorter: (a, b) => a.totalAmount - b.totalAmount,
    align: 'center',
  },
  {
    title: 'Trạng thái', dataIndex: 'status', key: 'status', width: 120, fixed: 'right', align: 'center',
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
  pageSize: 5,
  total: orders.value.length,
  showSizeChanger: false, // Đã có selector riêng
  showTotal: (total, range) => `Hiển thị ${range[0]} đến ${range[1]} trên tổng ${total} đơn hàng`,

});

// Dữ liệu được lọc dựa trên các điều kiện
const filteredData = computed(() => {
  let tempData = [...ordersRaw.value];

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
    const lowerSearchText = searchText.value.trim().toLowerCase();
    tempData = tempData.filter(order =>
      order.customerInfo.name?.toLowerCase().includes(lowerSearchText) ||
      order.customerInfo.phone?.includes(searchText.value.trim())
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


const handlePageSizeChange = (size) => {
  pagination.pageSize = size;
  pagination.current = 1;
};

const handleTableChange = (paginationInfo) => {
  pagination.current = paginationInfo.current;
  pagination.pageSize = paginationInfo.pageSize;
};


const getStatusColor = (status) => {
  switch (status) {
    case 'Chờ xác nhận': return 'orange';
    case 'Đã xác nhận': return 'blue';
    case 'Đang xử lý': return 'processing'; // có animation
    case 'Bắt đầu giao': return 'cyan';
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
