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
          <a-select
            v-model:value="pagination.pageSize"
            style="width: 80px"
            @change="handlePageSizeChange"
          >
            <a-select-option :value="5">5</a-select-option>
            <a-select-option :value="10">10</a-select-option>
            <a-select-option :value="20">20</a-select-option>
            <a-select-option :value="50">50</a-select-option>
          </a-select>
        </a-col>
        <a-col :xs="24" :sm="12" :md="8" :lg="6">
          <a-select
            v-model:value="selectedOrderType"
            placeholder="Lọc theo loại đơn"
            style="width: 100%"
            allow-clear
            @change="handleOrderTypeChange"
          >
            <a-select-option value="Tất cả">Tất cả loại đơn</a-select-option>
            <a-select-option value="Mang về">Mang về</a-select-option>
            <a-select-option value="Đặt bàn">Đặt bàn</a-select-option>
            </a-select>
        </a-col>
        <a-col :xs="24" :sm="24" :md="8" :lg="12">
           <a-input-search
              v-model:value="searchText"
              placeholder="Tìm theo Mã ĐH, Tên KH, SĐT..."
              allow-clear
              @search="handleSearch"
              @change="e => { if (e.target.value === '') handleSearch('') }"
            />
        </a-col>
      </a-row>

      <a-table
        :columns="columns"
        :data-source="paginatedData"
        :row-key="record => record.id"
        :pagination="pagination"
        @change="handleTableChange"
        bordered
        :scroll="{ x: 1200 }"
      >
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

      <a-modal v-model:open="isDetailModalVisible" title="Chi tiết đơn hàng" @ok="isDetailModalVisible = false" :footer="null" width="700px">
          <div v-if="selectedOrder">
              <p><strong>Mã đơn hàng:</strong> {{ selectedOrder.id }}</p>
              <p><strong>Ngày đặt:</strong> {{ selectedOrder.orderDate }}</p>
              <p><strong>Khách hàng:</strong> {{ selectedOrder.customerInfo.name }} - {{ selectedOrder.customerInfo.phone }}</p>
              <p><strong>Khu vực - Bàn:</strong> {{ selectedOrder.areaTable }}</p>
              <p><strong>Loại đơn:</strong> {{ selectedOrder.orderType }}</p>
              <p><strong>Trạng thái:</strong> <a-tag :color="getStatusColor(selectedOrder.status)">{{ selectedOrder.status }}</a-tag></p>
              <p><strong>Tổng tiền:</strong> {{ formatCurrency(selectedOrder.totalAmount) }}</p>
              <h4>Các món đã đặt:</h4>
              <a-list bordered :data-source="selectedOrder.items">
                  <template #renderItem="{ item }">
                  <a-list-item>
                      <a-list-item-meta
                      :description="`Số lượng: ${item.quantity} - Đơn giá: ${formatCurrency(item.price)}`"
                      >
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
  // Import các icon nếu cần cho nút hoặc các phần khác
  // import { EyeOutlined, PrinterOutlined } from '@ant-design/icons-vue';

  // Sample data (Dữ liệu mẫu)
  const initialOrderData = [
    { id: 'DH001', orderDate: '2025-06-01 10:30:00', areaTable: 'Khu A - Bàn 5', customerInfo: { name: 'Nguyễn Văn An', phone: '0901234567' }, orderType: 'Đặt bàn', totalAmount: 550000, status: 'Chờ xác nhận', items: [{id: 'M01', name: 'Cơm sườn đặc biệt', quantity: 2, price: 150000}, {id: 'M02', name: 'Canh chua cá lóc', quantity: 1, price: 250000}] },
    { id: 'DH002', orderDate: '2025-06-01 11:45:00', areaTable: 'Mang về', customerInfo: { name: 'Trần Thị Bích', phone: '0912345678' }, orderType: 'Mang về', totalAmount: 320000, status: 'Đã xác nhận', items: [{id: 'M03', name: 'Bún bò Huế', quantity: 2, price: 80000}, {id: 'M04', name: 'Nước ngọt Coca', quantity: 4, price: 40000}] },
    { id: 'DH003', orderDate: '2025-06-01 14:00:00', areaTable: 'Khu B - Bàn 2', customerInfo: { name: 'Lê Văn Cường', phone: '0987654321' }, orderType: 'Đặt bàn', totalAmount: 780000, status: 'Đang xử lý', items: [] },
    { id: 'DH004', orderDate: '2025-06-02 09:15:00', areaTable: 'Khu VIP - Phòng 1', customerInfo: { name: 'Phạm Thị Dung', phone: '0905558888' }, orderType: 'Đặt bàn', totalAmount: 1250000, status: 'Đang giao hàng', items: [] },
    { id: 'DH005', orderDate: '2025-06-02 12:30:00', areaTable: 'Mang về', customerInfo: { name: 'Hoàng Văn Em', phone: '0933112233' }, orderType: 'Mang về', totalAmount: 210000, status: 'Giao thành công', items: [] },
    { id: 'DH006', orderDate: '2025-06-02 15:00:00', areaTable: 'Khu A - Bàn 10', customerInfo: { name: 'Vũ Thị Giang', phone: '0977001122' }, orderType: 'Đặt bàn', totalAmount: 670000, status: 'Giao thất bại', items: [] },
    { id: 'DH007', orderDate: '2025-06-02 16:30:00', areaTable: 'Mang về', customerInfo: { name: 'Đặng Văn Hiếu', phone: '0944998877' }, orderType: 'Mang về', totalAmount: 150000, status: 'Đã hủy', items: [] },
    { id: 'DH008', orderDate: '2025-06-03 08:00:00', areaTable: 'Khu C - Bàn 1', customerInfo: { name: 'Ngô Thị Yến', phone: '0908789789' }, orderType: 'Đặt bàn', totalAmount: 920000, status: 'Chờ xác nhận', items: [] },
    { id: 'DH009', orderDate: '2025-06-03 09:30:00', areaTable: 'Khu A - Bàn 3', customerInfo: { name: 'Trịnh Văn Khải', phone: '0911223344' }, orderType: 'Đặt bàn', totalAmount: 480000, status: 'Đã xác nhận', items: [] },
    { id: 'DH010', orderDate: '2025-06-03 11:00:00', areaTable: 'Mang về', customerInfo: { name: 'Đỗ Thị Lan', phone: '0988776655' }, orderType: 'Mang về', totalAmount: 330000, status: 'Giao thành công', items: [] },
  ];

  const orders = ref([...initialOrderData]);
  const activeStatusTab = ref('Tất cả'); // Trạng thái đang được chọn trên tab
  const selectedOrderType = ref('Tất cả'); // Loại đơn hàng đang được chọn
  const searchText = ref('');

  // Modal state
  const isDetailModalVisible = ref(false);
  const selectedOrder = ref(null);

  const columns = [
    { title: 'STT', key: 'stt', width: 60, fixed: 'left' },
    { title: 'Mã ĐH', dataIndex: 'id', key: 'orderId', width: 100, fixed: 'left', sorter: (a,b) => a.id.localeCompare(b.id) },
    { title: 'Khu vực-Bàn', dataIndex: 'areaTable', key: 'areaTable', width: 150 },
    { title: 'Thông tin KH', key: 'customerInfo', width: 150 },
    { title: 'Loại đơn', dataIndex: 'orderType', key: 'orderType', width: 80,
      filters: [
          { text: 'Đặt bàn', value: 'Đặt bàn' },
          { text: 'Mang về', value: 'Mang về' },
      ],
      onFilter: (value, record) => record.orderType.includes(value),
    },
    { title: 'Tổng tiền', dataIndex: 'totalAmount', key: 'totalAmount', width: 100, sorter: (a, b) => a.totalAmount - b.totalAmount, align: 'right' },
    { title: 'Trạng thái', dataIndex: 'status', key: 'status', width: 120, fixed: 'right',
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

  </script>

  <style scoped>
  </style>
