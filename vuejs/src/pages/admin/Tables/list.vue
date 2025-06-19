<template>
  <div>
    <div v-if="isLoading || isLoadingPermissions" class="isLoading-overlay">
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>

    <div class="main-layout">
      <div class="main-content">
        <h2>Danh sách bàn</h2>
        <template v-if="hasPermission('view_table')">
          <div class="table-filter-box">
            <div>
              <input type="date" class="form-control rounded" v-model="date" :min="today" @change="getTable" />
            </div>

            <div class="filter-status-select">
              <select class="form-control rounded" v-model="filterStatus" @change="getTable">
                <option value="">Tất cả bàn</option>
                <option value="Bàn trống">Bàn trống</option>
                <option value="Đã đặt trước">Đã đặt trước</option>
                <option value="Đang phục vụ">Đang phục vụ</option>
              </select>
            </div>

            <div class="table-status-box">
              <strong>Trạng thái:</strong>
              <div class="status-item"><span class="status-dot billed"></span>Đã đặt trước</div>
              <div class="status-item"><span class="status-dot reservation"></span>Đang phục vụ</div>
              <div class="status-item"><span class="status-dot vacant"></span>Bàn trống</div>
            </div>
            <button v-if="hasPermission('create_table')" class="btn btn-outline-danger" @click="toggleSidebar">Thêm Bàn
              Mới</button>
          </div>

          <hr />
          <div class="col-md-12 form-section mt-2" style="background-color: #ffff; min-height: 500px">
            <draggable :list="displayTables" item-key="id" tag="div" class="table-container" :animation="150"
              @add="onTableAddedFromSidebar" group="tables">
              <template #item="{ element: ban }">
                <div class="table-block" :class="{ 'rotated-layout': ban.isRotated }">
                  <div class="table-content-wrapper" :class="{ 'rotated-visual': ban.isRotated }">
                    <div class="chairs top-chairs" :class="'ghe-' + getChairCount(ban.capacity)">
                      <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="n" :class="[
                        {
                          chair_billed: ban.current_day_status === 'Đã đặt trước',
                          'billed-text': ban.current_day_status === 'Đã đặt trước',
                          chair_reservation: ban.current_day_status === 'Đang phục vụ',
                          'reservation-text': ban.current_day_status === 'Đang phục vụ',
                          chair1: selectedTableId == ban.id

                        },
                      ]"></div>
                    </div>
                    <div @click="hasPermission('edit_table') ? loadTable(ban.id) : null" :class="[
                      selectedTableId == ban.id ? 'table-rect1' : 'table-rect',
                      {
                        medium: getChairCount(ban.capacity) === 2,
                        large: getChairCount(ban.capacity) === 3,
                        billed: ban.current_day_status === 'Đã đặt trước',
                        'billed-text': ban.current_day_status === 'Đã đặt trước',
                        reservation: ban.current_day_status === 'Đang phục vụ',
                        'reservation-text': ban.current_day_status === 'Đang phục vụ',

                      },
                    ]">
                      B{{ ban.table_number }}
                    </div>
                    <div class="chairs bottom-chairs" :class="'ghe-' + getChairCount(ban.capacity)">
                      <div class="chair" v-for="n in getChairCount(ban.capacity)" :key="'b' + n" :class="[
                        {
                          chair_billed: ban.current_day_status === 'Đã đặt trước',
                          'billed-text': ban.current_day_status === 'Đã đặt trước',
                          chair_reservation: ban.current_day_status === 'Đang phục vụ',
                          'reservation-text': ban.current_day_status === 'Đang phục vụ',
                          chair1: selectedTableId == ban.id
                        },
                      ]"></div>
                    </div>
                  </div>
                  <button v-if="ban.has_booking_history == false && hasPermission('delete_table')" class="rotate-btn"
                    @click="deleteTable(ban.id)">
                    <i class="bi bi-trash3-fill"></i>
                  </button>
                </div>
              </template>
            </draggable>
            <div class="d-flex justify-content-center mt-3 w-100">
              <nav>
                <ul class="pagination">
                  <li class="page-item" :class="{ disabled: currentPage.tables === 1 }">
                    <button type="button" class="page-link" @click="goToPage(currentPage.tables - 1)">
                      «
                    </button>
                  </li>

                  <li v-for="page in totalPagesTables" :key="page" class="page-item"
                    :class="{ active: currentPage.tables === page }">
                    <button type="button" class="page-link" @click="goToPage(page)">
                      {{ page }}
                    </button>
                  </li>

                  <li class="page-item" :class="{ disabled: currentPage.tables === totalPagesTables }">
                    <button type="button" class="page-link" @click="goToPage(currentPage.tables + 1)">
                      »
                    </button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </template>
      </div>

      <transition name="slide-fade">
        <!-- Chỉ hiển thị sidebar nếu người dùng có quyền tạo hoặc sửa bàn -->
        <div v-if="isSidebarOpen && (hasPermission('create_table') || hasPermission('edit_table'))"
          class="add-table-sidebar">
          <h5 v-if="!hasBookingHistory">{{ selectedTableId ? 'Sửa Bàn' : 'Thêm Bàn Mới' }}</h5>
          <h5 v-else>Chi tiết</h5>

          <button class="close-sidebar-btn" @click="selectedTableId = null; toggleSidebar()">X</button>
          <form @submit.prevent="addNewTable(selectedTableId)">
            <div class="mb-3">
              <label for="newTableNumber" class="form-label">Số bàn:</label>

              <input type="number" class="form-control rounded" id="newTableNumber" v-model.number="table_number"
                required @input="updateNewTablePreview" :disabled="hasBookingHistory" />

            </div>
            <div class="mb-3">
              <label for="newTableCapacity" class="form-label">Số ghế:</label>
              <select class="form-select rounded" id="newTableCapacity" v-model="capacity"
                @change="updateNewTablePreview" :disabled="hasBookingHistory">
                <option selected value="2">2</option>
                <option value="4">4</option>
                <option value="6">6</option>
              </select>
            </div>
            <button type="submit" class="btn btn-outline-danger w-100 mb-3"
              v-if="selectedTableId && hasPermission('edit_table') && !hasBookingHistory">
              Sửa Bàn
            </button>
            <button type="submit" class="btn btn-outline-danger w-100 mb-3"
              v-else-if="!selectedTableId && hasPermission('create_table')">Thêm Bàn</button>
          </form>

          <hr />
          <div class="new-table-preview-area">
            <draggable v-model="newTablesQueue" item-key="id" tag="div" class="new-table-draggable-container"
              :animation="150" group="tables" @end="onNewTableDragEnd">
              <template #item="{ element: newBan }">
                <div class="table-block draggable-new-table" :class="{ 'rotated-layout': newBan.isRotated }">
                  <div class="table-content-wrapper" :class="{ 'rotated-visual': newBan.isRotated }">
                    <div class="chairs top-chairs" :class="'ghe-' + getChairCount(newBan.capacity)">
                      <div class="chair" v-for="n in getChairCount(newBan.capacity)" :key="n"></div>
                    </div>
                    <div class="table-rect table-new" :class="{
                      medium: getChairCount(newBan.capacity) === 2,
                      large: getChairCount(newBan.capacity) === 3,
                    }">
                      Bàn {{ newBan.table_number }}
                    </div>
                    <div class="chairs bottom-chairs" :class="'ghe-' + getChairCount(newBan.capacity)">
                      <div class="chair" v-for="n in getChairCount(newBan.capacity)" :key="'b' + n"></div>
                    </div>
                  </div>
                </div>
              </template>
            </draggable>
          </div>
          <hr>
          <div style="min-height: 80px; overflow-y: auto;">
            <div class="border shadow-sm p-2 mb-2 rounded" style="cursor: pointer;" v-for="item in tableOrder"
              :key="item.order_id">
              <div class="" @click="getInfoDetail(item.order_id)">
                <div class="fw-bold text-danger">#{{ item.order_id }}</div>
                <div class="d-flex justify-content-between">
                  <div class="text-mute"><i class="bi bi-calendar2-week"></i> {{ formatTime(item.reserved_from) }}</div>
                  <div class="text-mute"><i class="bi bi-card-checklist"></i> {{ item.order_status }}</div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>


  <div name="popup-fade">
    <div v-show="showDetailPopup" class="event-detail-popup-overlay" @click="closeDetailPopup">
      <div class="event-detail-popup" @click.stop>
        <div class="popup-header">
          <h3 class="popup-title">Thông tin chi tiết đơn hàng {{ info.id }}</h3>
          <button class="close-button" @click="closeDetailPopup">&times;</button>
        </div>
        <div class="popup-content" v-if="info">
          <div class="info-item">
            <div class="info-block">
              <i class="bi bi-table"></i>Ngày đặt:
              <span v-for="(t, index) in info.tables" :key="index">
                {{ formatDate(t.reserved_from)
                }}<span v-if="index < info.tables.length - 1">, </span>
              </span>
            </div>
            <div class="info-block">
              <i class="bi bi-clock"></i>Giờ đặt:
              <span v-for="(t, index) in info.tables" :key="index">
                {{ formatTime(t.reserved_form) }} - {{ formatTime(t.reserved_to)
                }}<span v-if="index < info.tables.length - 1">, </span>
              </span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-block">
              <i class="fa-solid fa-calendar"></i>
              <span>Bàn số:
                <span v-for="(t, index) in info.tables" :key="index">
                  {{ t.table_number }}<span v-if="index < info.tables.length - 1">, </span>
                </span>
              </span>
            </div>
            <div class="info-block">
              <i class="bi bi-people"></i>
              <span>Lượng khách:
                {{ info.guest_count }}
              </span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-block">
              <i class="fa-solid fa-user"></i>
              <span>Khách hàng: {{ info.guest_name }}</span>
            </div>
            <div class="info-block">
              <i class="bi bi-telephone"></i>
              <span>SĐT: {{ info.guest_phone }}</span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-block">
              <i class="bi bi-envelope"></i>
              <span>Email: {{ info.guest_email }}</span>
            </div>
            <div class="info-block">
              <i class="bi bi-card-heading"></i>
              <span>Ghi chú: {{ info.note || 'Không có' }}</span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-block">
              <span> <i class="bi bi-card-list"></i>Trạng thái thanh toán: Làm sau </span>
            </div>
            <div class="info-block">
              <span> <i class="bi bi-cash"></i>Phương thức thanh toán: Làm sau </span>
            </div>
          </div>
          <div class="info-item">
            <div class="info-block">
              <span>
                <i class="bi bi-cash"></i>Tổng tiền: {{ formatNumber(info.total_price) }} VNĐ
              </span>
            </div>
            <div class="info-block">
              <i class="bi bi-card-list"></i>
              <span>Trạng thái đơn:
                <select v-model="info.order_status" class="p-1 border rounded-0"
                  @change="updateStatus(info.id, info.order_status)" :disabled="!hasPermission('edit_booking')">
                  <option value="Chờ xác nhận" :disabled="!canSelectStatus(info.order_status, 'Chờ xác nhận')">
                    Chờ xác nhận
                  </option>
                  <option value="Đã xác nhận" :disabled="!canSelectStatus(info.order_status, 'Đã xác nhận')">
                    Đã xác nhận
                  </option>
                  <option value="Đang xử lý" :disabled="!canSelectStatus(info.order_status, 'Đang xử lý')">
                    Đang xử lý
                  </option>
                  <option value="Khách đã đến" :disabled="!canSelectStatus(info.order_status, 'Khách đã đến')">
                    Khách đã đến
                  </option>
                  <option value="Hoàn thành" :disabled="!canSelectStatus(info.order_status, 'Hoàn thành')">
                    Hoàn thành
                  </option>
                  <option value="Đã hủy" :disabled="!canSelectStatus(info.order_status, 'Đã hủy')">
                    Đã hủy
                  </option>
                </select>
              </span>
            </div>
          </div>
          <div class="info-item1">
            <i class="bi bi-journals" style="padding-right: 15px"></i>
            <span>Các món đã đặt</span>
            <div class="card-custom" style="max-height: 200px; overflow-y: auto">
              <div class="row align-items-center border-bottom" v-for="item in info.details" :key="item.id">
                <div class="col-6 p-2">
                  <div class="item-name">
                    {{ item.food_name }}
                  </div>
                  <div class="item-details">
                    <ul v-if="item.toppings && item.toppings.length" class="mb-0 ps-3">
                      <li v-for="topping in item.toppings" :key="topping.food_toppings_id">
                        + {{ topping.topping_name }} ({{ Number(topping.price).toLocaleString() }}
                        đ)
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="col-2">{{ item.quantity }}</div>
                <div class="col-3">
                  <div class="total-price">
                    {{ formatNumber(calculateTotalItemPrice(item)) }} VNĐ
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="popup-actions" v-if="hasPermission('edit_booking')">
          <router-link :to="`/admin/choose-list-food/${info.id}`" class="btn edit-button">Chọn món</router-link>
          <router-link :to="`/admin/tables/${info.id}`" class="btn edit-button">Chuyển bàn</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'
import draggable from 'vuedraggable'

import { Permission } from '@/stores/permission'
import { Info } from '@/stores/info-order-reservation'

export default {
  components: {
    draggable
  },
  setup() {
    const userId = ref(null)
    const userString = localStorage.getItem('user')
    if (userString) {
      const user = JSON.parse(userString)
      if (user && user.id !== undefined) {
        userId.value = user.id
      }
    }
    const { hasPermission, permissions, isLoadingPermissions } = Permission(userId)
    const allTables = ref([])
    const route = useRoute()
    const orderId = route.params.orderId
    const selectedTableId = ref(null)
    const table_number = ref('')
    const capacity = ref(2)
    const hasBookingHistory = ref(false)
    const isSidebarOpen = ref(false)
    const newTablesQueue = ref([])
    const today = new Date().toISOString().split('T')[0]
    const date = ref(today)
    const filterStatus = ref('')
    const isLoading = ref(false)
    const tableOrder = ref([])
    const currentPage = ref(1)
    const isMobileView = ref(window.innerWidth <= 768)
    const dynamicItemsPerPageTable = computed(() => {
      return isMobileView.value ? 9 : 14
    })
    const { formatTime, formatDate, formatNumber, info, getInfo } = Info.setup()

    const updateView = () => {
      isMobileView.value = window.innerWidth <= 768
    }


    const totalPagesTables = computed(() => {
      return Math.ceil(allTables.value.length / dynamicItemsPerPageTable.value)
    })


    const paginatedTables = computed(() => {
      const start = (currentPage.value - 1) * dynamicItemsPerPageTable.value
      const end = start + dynamicItemsPerPageTable.value
      return allTables.value.slice(start, end)
    })


    const displayTables = computed(() => paginatedTables.value)

    const resetNewTableForm = () => {
      table_number.value = ''
      capacity.value = 2
      newTablesQueue.value = []
      hasBookingHistory.value = false
      tableOrder.value = []
    }


    const getTable = async () => {
      if (!hasPermission('view_table')) {
        allTables.value = []
        return
      }

      isLoading.value = true
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/tables', {
          params: {
            date: date.value,
            status: filterStatus.value
          }
        })
        allTables.value = res.data.tables.map((table) => ({
          ...table,
        }))
        currentPage.value = 1
      } catch (error) {
        console.error('Lỗi khi tải danh sách bàn:', error)
        toast.error('Lỗi khi tải danh sách bàn.')
      } finally {
        isLoading.value = false
      }
    }


    const goToPage = (page) => {
      if (page >= 1 && page <= totalPagesTables.value) {
        currentPage.value = page
      }
    }


    const getChairCount = (seats) => {
      if (seats <= 2) return 1
      if (seats <= 4) return 2
      return 3
    }

    const changeTable = async (table_id) => {
      if (!table_id) {
        toast.error('Vui lòng chọn một bàn để chuyển.')
        return
      }
      try {
        selectedTableId.value = table_id
        await axios.put('http://127.0.0.1:8000/api/change-table', {
          id: orderId,
          table_id: table_id
        })
        toast.success('Thay đổi bàn thành công!')
        getTable()
      } catch (error) {
        console.error('Lỗi khi chuyển bàn:', error)
        toast.error('Có lỗi xảy ra khi chuyển bàn.')
      }
    }


    const toggleSidebar = () => {
      if (!isSidebarOpen.value && !(hasPermission('create_table') || hasPermission('edit_table'))) {
        toast.warn('Bạn không có quyền thêm hoặc sửa bàn.')
        return
      }
      isSidebarOpen.value = !isSidebarOpen.value
      if (!isSidebarOpen.value) {
        resetNewTableForm()
        selectedTableId.value = null
      }
    }


    const addNewTable = async (table_id) => {
      if (!table_id && !hasPermission('create_table')) {
        toast.error('Bạn không có quyền thêm bàn mới.')
        return
      }
      if (table_id && !hasPermission('edit_table')) {
        toast.error('Bạn không có quyền sửa bàn này.')
        return
      }

      if (!table_number.value || !capacity.value || capacity.value <= 0) {
        toast.error('Vui lòng nhập đủ Số bàn và Số ghế hợp lệ.')
        return
      }

      try {
        isLoading.value = true
        if (!table_id) {
          await axios.post(`http://127.0.0.1:8000/api/insert-table`, {
            table_number: table_number.value,
            capacity: capacity.value
          })
          toast.success(`Thêm bàn thành công!`)
        } else {
          await axios.put(`http://127.0.0.1:8000/api/tables/${table_id}`, {
            table_number: table_number.value,
            capacity: capacity.value
          })
          toast.success(`Sửa bàn thành công!`)
        }
        await getTable()
        resetNewTableForm()
        toggleSidebar()
        selectedTableId.value = null
      } catch (error) {
        console.error('Lỗi API khi thêm/sửa bàn:', error)
        if (error.response && error.response.status === 422 && error.response.data.errors) {
          let validationErrors = ''
          for (const field in error.response.data.errors) {
            validationErrors += error.response.data.errors[field].join(' ') + ' '
          }
          toast.error(`${validationErrors.trim()}`)
        } else {
          toast.error('Có lỗi xảy ra trong quá trình thêm/sửa bàn.')
        }
      } finally {
        isLoading.value = false
      }
    }


    const onNewTableDragEnd = async (event) => {
      if (!hasPermission('create_table')) {
        toast.error('Bạn không có quyền thêm bàn mới bằng cách kéo thả.')
        return
      }

      if (event.from === newTablesQueue.value && event.to === newTablesQueue.value) {
        if (newTablesQueue.value.length === 0 && table_number.value && capacity.value) {
          updateNewTablePreview()
        }
      }
    }


    const onTableAddedFromSidebar = async (event) => {
      if (!hasPermission('create_table')) {
        toast.error('Bạn không có quyền thêm bàn mới.')
        allTables.value.splice(event.newIndex, 1)
        return
      }

      const addedTableData = allTables.value[event.newIndex]
      if (!addedTableData || !addedTableData.table_number || !addedTableData.capacity) {
        toast.error('Vui lòng nhập số bàn và số ghế trước khi thêm bàn.')
        allTables.value.splice(event.newIndex, 1)
        return
      }

      try {
        isLoading.value = true
        const response = await axios.post(`http://127.0.0.1:8000/api/insert-table`, {
          table_number: addedTableData.table_number,
          capacity: addedTableData.capacity
        })

        toast.success(`Đã thêm bàn thành công!`)
        if (response.data && response.data.id) {
          addedTableData.id = response.data.id
        }

        await getTable()
        resetNewTableForm()
      } catch (error) {
        console.error('Lỗi API khi thêm bàn:', error)
        if (error.response && error.response.status === 422 && error.response.data.errors) {
          let validationErrors = ''
          for (const field in error.response.data.errors) {
            validationErrors += error.response.data.errors[field].join(' ') + ' '
          }
          toast.error(`Lỗi dữ liệu: ${validationErrors.trim()}`)
        } else {
          toast.error('Có lỗi xảy ra khi thêm bàn.')
        }
      } finally {
        isLoading.value = false
      }
    }


    const deleteTable = async (table_id) => {
      if (!hasPermission('delete_table')) {
        toast.error('Bạn không có quyền xóa bàn.')
        return
      }

      try {
        isLoading.value = true
        await axios.delete(`http://127.0.0.1:8000/api/tables/${table_id}`)
        await getTable()
        toast.success('Xoá bàn thành công')
      } catch (error) {
        console.log('Lỗi ' + error)
        toast.error('Xoá bàn không thành công')
      } finally {
        isLoading.value = false
      }
    }


    const loadTable = async (id) => {
      if (!hasPermission('edit_table')) {
        toast.error('Bạn không có quyền sửa bàn.')
        return
      }

      if (selectedTableId.value === id && isSidebarOpen.value) {
        selectedTableId.value = null
        toggleSidebar()
        resetNewTableForm()
      } else {
        const selected = allTables.value.find((t) => t.id === id)
        if (selected) {
          selectedTableId.value = selected.id
          table_number.value = selected.table_number
          capacity.value = selected.capacity
          hasBookingHistory.value = selected.has_booking_history
          updateNewTablePreview()

          const res = await axios.get(`http://127.0.0.1:8000/api/get-orders-tables/${selected.id}`)
          tableOrder.value = res.data.data.reservations
          if (!isSidebarOpen.value) {
            toggleSidebar();
          }

        }
      }
    }


    const updateNewTablePreview = () => {
      if (table_number.value && parseInt(capacity.value) > 0) {
        const tempTable = {
          id: `temp-${Date.now()}`,
          table_number: table_number.value,
          capacity: parseInt(capacity.value),
          status: 'Bàn trống'
        }
        newTablesQueue.value = [tempTable]
      } else {
        newTablesQueue.value = []
      }
    }


    const showDetailPopup = ref(false)

    const getInfoDetail = async (id) => {
      await getInfo('order', id)
      showDetailPopup.value = true
      toggleSidebar()
    }


    const closeDetailPopup = () => {
      showDetailPopup.value = false
    }


    const calculateTotalItemPrice = (item) => {
      const basePrice = item.price * item.quantity
      const toppingTotal =
        (item.toppings?.reduce((sum, topping) => sum + Number(topping.price), 0) || 0) * item.quantity
      return basePrice + toppingTotal
    }


    const updateStatus = async (id, status) => {
      try {
        if (confirm(`Bạn có chắc chắn muốn cập nhật sang trạng thái ${status}`)) {
          await axios.post('http://127.0.0.1:8000/api/reservation-update-status', {
            id: id,
            order_status: status,
          })
          closeDetailPopup()
          await getTable()
          toast.success('Cập nhật thành công')
        }
      } catch (error) {
        toast.error('Có lỗi xảy ra')
        console.log(error)
      }
    }


    const canSelectStatus = (currentStatus, optionStatus) => {
      const statusOrder = [
        'Chờ xác nhận',
        'Đã xác nhận',
        'Đang xử lý',
        'Khách đã đến',
        'Hoàn thành',
        'Đã hủy',
      ]

      const currentIndex = statusOrder.indexOf(currentStatus)
      const optionIndex = statusOrder.indexOf(optionStatus)

      if (currentIndex === -1 || optionIndex === -1) return false

      if (optionStatus === currentStatus) return true

      if (currentStatus === 'Hoàn thành' || currentStatus === 'Đã hủy') return false

      if (optionIndex === currentIndex + 1) return true

      if (optionStatus === 'Đã hủy' && currentStatus !== 'Đã hủy') return true

      return false
    }

    // const statusMessage = ref('')
    // const statusType = ref('')
    // let timeoutId = null;


    // function showStatusMessage(message, type) {
    //   statusMessage.value = message;
    //   statusType.value = type;
    //   if (timeoutId) clearTimeout(timeoutId);
    //   timeoutId = setTimeout(() => {
    //     statusMessage.value = '';
    //     statusType.value = '';
    //   }, 3000);
    // }

    watch(permissions, (newPermissions) => {
      if (newPermissions.length > 0 && !isLoadingPermissions.value) {
        getTable();
      } else if (newPermissions.length === 0 && !isLoadingPermissions.value) {
        getTable();
      }
    }, { immediate: true });

    return {
      userId,
      allTables,
      route,
      orderId,
      selectedTableId,
      table_number,
      capacity,
      isSidebarOpen,
      newTablesQueue,
      date,
      filterStatus,
      isLoading,
      currentPage,
      isMobileView,
      dynamicItemsPerPageTable,
      totalPagesTables,
      paginatedTables,
      displayTables,
      getTable,
      goToPage,
      getChairCount,
      changeTable,
      toggleSidebar,
      addNewTable,
      onNewTableDragEnd,
      onTableAddedFromSidebar,
      deleteTable,
      loadTable,
      updateView,
      resetNewTableForm,
      updateNewTablePreview,
      hasPermission,
      isLoadingPermissions,
      tableOrder,
      today,
      formatTime,
      getInfoDetail,
      showDetailPopup,
      closeDetailPopup,
      info,
      formatNumber,
      canSelectStatus,
      updateStatus,
      formatDate,
      calculateTotalItemPrice,
      hasBookingHistory,
      // showStatusMessage, timeoutId, statusType, statusMessage
    }
  }
}
</script>

<style scoped>
.card-custom {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
  padding: 0 20px;
  margin-top: 5px;
}

.item-name {
  font-weight: bold;
  font-size: 14px;
}

.total-price {
  font-size: 14px;
  text-align: right;
  color: #c92c3c;
}

.item-details {
  font-size: 11px;
  color: #6c757d;
}

.ban {
  background-color: #ffffff;
  /* border: 1px solid #ffffff; */
  font-weight: bold;
  /* border-radius: 5px; */
}

.title {
  font-size: 12px;
  text-align: left;
  margin-bottom: 2px;
}

.time-badge {
  color: #8d8a8a;
  font-size: small;
}

/* .status-message {
  margin-top: 15px;
  padding: 10px 15px;
  border-radius: 5px;
  font-weight: bold;
  text-align: center;
}

.status-message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
} */
.event-detail-popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.event-detail-popup {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  width: 90%;
  max-width: 600px;
  overflow: hidden;
  font-family: Arial, sans-serif;
  color: #333;
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0px 20px;
  background-color: #c92c3c;
  border-bottom: 1px solid #eee;
}

.popup-title {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
  color: #ffffff;
}

.event-detail-popup.show {
  opacity: 1;
  transform: translateY(0);
}

a {
  text-decoration: none;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.8em;
  cursor: pointer;
  color: #ffffff;
  padding: 0 5px;
}

.popup-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.info-item {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  /* khoảng cách giữa 2 cột */
  font-size: 0.95em;
  color: #1d1d1d;
}

.info-block {
  align-items: center;
  gap: 8px;
}

.info-item i {
  margin-right: 12px;
  font-size: 1.1em;
}

.info-item span {
  flex-grow: 1;
}

.popup-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 15px 20px;
  border-top: 1px solid #eee;
  background-color: #f5f5f5;
}

.popup-actions button {
  padding: 8px 18px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
  transition:
    background-color 0.2s,
    color 0.2s;
}

.edit-button {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}

.edit-button:hover {
  background-color: #c92c3c;
  color: white;
}

.delete-button {
  color: #c92c3c;
  border: 1px solid #c92c3c;
}

.delete-button:hover {
  background-color: #c92c3c;
  color: white;
}

.fc-theme-standard .fc-scrollgrid {
  border: none;
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

.popup-fade-enter-active,
.popup-fade-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
}

.popup-fade-enter-from,
.popup-fade-leave-to {
  transform: translateY(-20px);
}

.popup-fade-enter-to,
.popup-fade-leave-from {
  transform: translateY(0px);
}

.main-layout {
  display: flex;
  height: 100vh;
  overflow: hidden;
}

.main-content {
  flex-grow: 1;
  padding: 20px;
  overflow-y: auto;
}

h2 {
  margin-bottom: 1.5rem;
  font-size: 2rem;
}

/* --- Filter & Status Box --- */
.table-filter-box {
  display: flex;
  flex-wrap: wrap;
  /* Cho phép các mục tự xuống hàng */
  gap: 12px;
  align-items: center;
  margin-bottom: 1rem;
}

.table-filter-box>div {
  flex: 1 1 auto;
  /* Các mục sẽ co giãn linh hoạt */
  min-width: 150px;
  /* Đảm bảo đủ rộng trên desktop */
}

.filter-status-select {
  width: 180px;
  /* Giữ nguyên width cho select nếu không muốn nó co giãn hoàn toàn */
  flex-shrink: 0;
  /* Ngăn không cho nó bị co lại quá nhiều nếu không gian chật */
}

.table-status-box {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 16px;
  border-radius: 12px;
  font-size: 14px;
  flex-wrap: wrap;
  /* Cho phép các mục trạng thái xuống hàng */
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

.table-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(min(140px, 100%), 1fr));
  gap: 20px;
  padding: 10px;
  justify-content: center;
  /* Căn giữa các cột */
  border: 1px solid #eee;
  border-radius: 8px;
  align-items: start;
  min-height: 300px;
}

/* --- Individual Table Block Styles --- */
.table-block {
  display: flex;
  flex-direction: column;
  /* Đảm bảo nội dung bàn xếp dọc */
  justify-content: center;
  align-items: center;
  position: relative;
  transition: all 0.3s ease;
  box-sizing: border-box;
  padding: 5px;

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

.table-content-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.3s ease;
  transform-origin: center center;
}

.table-content-wrapper.rotated-visual {
  transform: rotate(90deg);
}

.chairs {
  display: flex;
  justify-content: center;
  margin: 4px 0;
  width: 100%;
}

.chair {
  width: 40px;
  height: 6px;
  background-color: #ddd;
  border-radius: 3px;
  margin: 0 4px;
}

.chair1 {
  width: 40px;
  height: 6px;
  background-color: #007bff;
  border-radius: 3px;
  margin: 0 4px;
}

.table-content-wrapper.rotated-visual .top-chairs,
.table-content-wrapper.rotated-visual .bottom-chairs {
  flex-direction: column;
  margin: 0 8px;
}

.table-content-wrapper.rotated-visual .chair {
  margin: 4px 0;
  height: 40px;
  width: 6px;
}

.table-rect,
.table-rect1 {
  color: rgb(81, 73, 73);
  padding: 10px 20px;
  border-radius: 10px;
  text-align: center;
  border: 5px solid #ddd;
  min-width: 80px;
  /* Kích thước cơ bản cho bàn nhỏ nhất */
  font-weight: bold;
  font-size: 16px;
  background-color: #f4f4f4;
  flex-shrink: 0;
  box-sizing: border-box;
  cursor: pointer;
  /* Thêm con trỏ để người dùng biết có thể click */
}

.table-rect1 {
  border-color: #007bff;
  /* Màu border khi được chọn */
}

.table-rect.medium,
.table-rect1.medium {
  min-width: 120px;
  /* Kích thước bàn trung bình */
}

.table-rect.large,
.table-rect1.large {
  min-width: 160px;
  /* Kích thước bàn lớn */
}

/* --- Status Colors --- */
.billed {
  border: 5px solid #fcdc7c;
  /* Đã đặt trước */
}

.chair_billed {
  background-color: #fcdc7c;
  /* Đã đặt trước */
}

.vacant {
  background-color: #f4f4f4;
  border: 5px solid #ddd;
}

.reservation {
  border: 5px solid #ec988e;
  /* Đang phục vụ */
}

/* Các lớp text color, dùng để đảm bảo màu chữ phù hợp với màu nền bàn/ghế */
.billed-text {
  color: #d1a64b;
  /* Màu chữ cho trạng thái Đã đặt trước */
}

.reservation-text {
  color: #e06c75;
  /* Màu chữ cho trạng thái Đang phục vụ */
}

.chair_reservation {
  background-color: #ec988e;
  /* Đang phục vụ */
}


/* --- Sidebar & Animations --- */
.add-table-sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 350px;
  height: 100%;
  background-color: #ffff;
  box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2);
  padding: 20px;
  overflow-y: auto;
  z-index: 1000;
}

.close-sidebar-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: #666;
}

.close-sidebar-btn:hover {
  color: #333;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: transform 0.3s ease-in-out;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(100%);
}

.new-table-preview-area {
  min-height: 120px;
  padding: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f9f9f9;
  border: 1px dashed #ccc;
  border-radius: 8px;
}

.new-table-draggable-container {
  display: flex;
  justify-content: center;
  width: 100%;
}

.table-rect.table-new {
  background-color: #f4f4f4;
  color: rgb(81, 73, 73);
  border: 5px solid #ddd;
}

/* --- Drag and Drop Effects --- */
.sortable-ghost,
.sortable-drag {
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  opacity: 0.8;
  transition: box-shadow 0.15s ease-in-out;
}

.sortable-chosen {
  opacity: 0.5;
  background-color: #f0f0f0;
}

/* --- Rotate/Delete Button --- */
.rotate-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  border-radius: 50%;
  width: 25px;
  height: 25px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  font-size: 0.8em;
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
  z-index: 10;
}

.table-block:hover .rotate-btn {
  opacity: 1;
}

/* --- RESPONSIVE STYLES --- */

@media (max-width: 991.98px) {
  .main-layout {
    flex-direction: column;
    height: auto;
    overflow-y: auto;
  }

  .main-content {
    padding: 15px;
  }

  h2 {
    font-size: 1.8rem;
  }

  .table-filter-box {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }

  .table-filter-box>div {
    width: 100%;
    min-width: unset;
  }

  .filter-status-select {
    width: 100%;
  }

  .table-status-box {
    flex-wrap: wrap;
    justify-content: center;
    font-size: 13px;
    padding: 6px 10px;
  }

  .table-container {
    grid-template-columns: repeat(auto-fill, minmax(min(140px, 100%), 1fr));
    gap: 15px;
    max-width: 720px;
  }

  .add-table-sidebar {
    width: 100%;
    left: 0;
    top: auto;
    bottom: 0;
    height: auto;
    max-height: 80vh;
    padding-bottom: 20px;
  }

  .slide-fade-enter-from,
  .slide-fade-leave-to {
    transform: translateY(100%);
  }

  .table-rect,
  .table-rect1 {
    font-size: 15px;
    padding: 9px 18px;
    min-width: 75px;
  }

  .table-rect.medium,
  .table-rect1.medium {
    min-width: 110px;
  }

  .table-rect.large,
  .table-rect1.large {
    min-width: 150px;
  }

  .chair {
    width: 35px;
    height: 6px;
    margin: 0 3px;
  }

  .table-content-wrapper.rotated-visual .chair {
    height: 35px;
    width: 6px;
    margin: 3px 0;
  }
}

@media (max-width: 767.98px) {
  .main-content {
    padding: 10px;
  }

  h2 {
    font-size: 1.3rem;
  }

  .table-filter-box {
    gap: 8px;
  }

  .table-status-box {
    flex-direction: column;
    align-items: flex-start;
    padding: 5px 10px;
    font-size: 12px;
  }

  .table-container {
    grid-template-columns: repeat(auto-fill, minmax(min(100px, 100%), 1fr));
    gap: 10px;
    padding: 5px;
    max-width: 380px;
  }

  .table-block {
    padding: 3px;
  }

  .table-rect,
  .table-rect1 {
    font-size: 13px;
    padding: 6px 12px;
    min-width: 70px;
  }

  .table-rect.medium,
  .table-rect1.medium {
    min-width: 90px;
  }

  .table-rect.large,
  .table-rect1.large {
    min-width: 120px;
    font-size: 14px;
  }

  .chair {
    width: 25px;
    height: 5px;
    margin: 0 2px;
  }

  .table-content-wrapper.rotated-visual .chair {
    height: 25px;
    width: 5px;
    margin: 2px 0;
  }

  .chairs {
    margin: 5px 0;
  }

  .table-content-wrapper.rotated-visual .top-chairs,
  .table-content-wrapper.rotated-visual .bottom-chairs {
    margin: 0 5px;
  }

  .rotate-btn {
    width: 20px;
    height: 20px;
    font-size: 0.7em;
  }
}
</style>
