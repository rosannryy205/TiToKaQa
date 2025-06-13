<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Đang tải...</span>
    </div>
  </div>
  <div class="main-layout">
    <div class="main-content">
      <h2>Danh sách bàn</h2>
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
        <button class="btn btn-outline-danger" @click="toggleSidebar">Thêm Bàn Mới</button>
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
                    },
                  ]"></div>
                </div>
                <div @click="loadTable(ban.id)" :class="[
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
                    },
                  ]"></div>
                </div>
              </div>
              <button v-if="ban.has_booking_history == false" class="rotate-btn" @click="deleteTable(ban.id)">
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
    </div>

    <transition name="slide-fade">
      <div v-if="isSidebarOpen" class="add-table-sidebar">
        <h5>Thêm Bàn Mới</h5>
        <button class="close-sidebar-btn"  @click="selectedTableId = null; toggleSidebar()" >X</button>
        <form @submit.prevent="addNewTable(selectedTableId)">
          <div class="mb-3">
            <label for="newTableNumber" class="form-label">Số bàn:</label>
            <input type="text" class="form-control rounded" id="newTableNumber" v-model="table_number" required
              @input="updateNewTablePreview" />
          </div>
          <div class="mb-3">
            <label for="newTableCapacity" class="form-label">Số ghế:</label>
            <select class="form-select rounded" id="newTableCapacity" v-model="capacity"
              @change="updateNewTablePreview">
              <option selected value="2">2</option>
              <option value="4">4</option>
              <option value="6">6</option>
            </select>
          </div>
          <button type="submit" class="btn btn-outline-danger w-100 mb-3" v-if="selectedTableId">
            Sửa Bàn
          </button>
          <button type="submit" class="btn btn-outline-danger w-100 mb-3" v-else>Thêm Bàn</button>
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
      </div>
    </transition>
  </div>
</template>

<script>
import axios from 'axios'
import { onBeforeUnmount } from 'vue'
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'
import draggable from 'vuedraggable'

export default {
  components: {
    draggable,
  },
  setup() {
    const allTables = ref([])
    const route = useRoute()
    const orderId = route.params.orderId
    const selectedTableId = ref(null)
    const table_number = ref('')
    const capacity = ref(2)
    const isSidebarOpen = ref(false)
    const newTablesQueue = ref([])
    const today = new Date().toISOString().split('T')[0]
    const date = ref(today)
    const filterStatus = ref('')
    const isLoading = ref(false)

    const currentPage = ref(1)
    const isMobileView = ref(window.innerWidth <= 768);
    const dynamicItemsPerPageTable = computed(() => {
      return isMobileView.value ? 9 : 14; // 9 item cho mobile, 14 cho desktop
    })

    const updateView = () => {
      isMobileView.value = window.innerWidth <= 768;
    };


    const totalPagesTables = computed(() => {
      return Math.ceil(allTables.value.length / dynamicItemsPerPageTable.value);
    });

    const paginatedTables = computed(() => {
      const start = (currentPage.value - 1) * dynamicItemsPerPageTable.value;
      const end = start + dynamicItemsPerPageTable.value;
      return allTables.value.slice(start, end);
    });


    const displayTables = computed(() => paginatedTables.value)


    const resetNewTableForm = () => {
      table_number.value = ''
      capacity.value = 2
      newTablesQueue.value = []
    }

    const getTable = async () => {
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/tables', {
          params: {
            date: date.value,
            status: filterStatus.value,
          },
        })
        allTables.value = res.data.tables.map((table) => ({ ...table, isRotated: false }))
        currentPage.value = 1; // Reset về trang 1 khi filter hoặc tải lại bàn
      } catch (error) {
        console.error('Lỗi khi tải danh sách bàn:', error)
        toast.error('Lỗi khi tải danh sách bàn.')
      }
    }

    // hàm chuyển trang
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
          table_id: table_id,
        })
        toast.success('Thay đổi bàn thành công!')
        getTable()
      } catch (error) {
        console.error('Lỗi khi chuyển bàn:', error)
        toast.error('Có lỗi xảy ra khi chuyển bàn.')
      }
    }

    const toggleSidebar = () => {
      isSidebarOpen.value = !isSidebarOpen.value
      if (!isSidebarOpen.value) {
        resetNewTableForm()
      }
    }

    const addNewTable = async (table_id) => {
      if (!table_number.value || !capacity.value || capacity.value <= 0) {
        toast.error('Vui lòng nhập đủ Số bàn và Số ghế hợp lệ.')
        return
      }
      try {
        if (!table_id) {
          isLoading.value = true
          await axios.post(`http://127.0.0.1:8000/api/insert-table`, {
            table_number: table_number.value,
            capacity: capacity.value,
          })
          await getTable()
          resetNewTableForm()
          toggleSidebar()
          selectedTableId.value = null
          isLoading.value = false
          toast.success(`Thêm bàn thành công!`)
        } else {
          isLoading.value = true
          await axios.put(`http://127.0.0.1:8000/api/tables/${table_id}`, {
            table_number: table_number.value,
            capacity: capacity.value,
          })
          await getTable()
          resetNewTableForm()
          selectedTableId.value = null
          toggleSidebar()
          isLoading.value = false
          toast.success(`Sửa bàn thành công!`)
        }
      } catch (error) {
        console.error('Lỗi API khi thêm bàn:', error)
        if (error.response.status === 422 && error.response.data && error.response.data.errors) {
          let validationErrors = ''
          for (const field in error.response.data.errors) {
            validationErrors += error.response.data.errors[field].join(' ') + ' '
          }
          toast.error(`Lỗi dữ liệu: ${validationErrors.trim()}`)
        }
      }finally{
          isLoading.value = false
      }
    }

    const onNewTableDragEnd = (event) => {
      if (event.from === newTablesQueue.value && event.to === newTablesQueue.value) {
        if (newTablesQueue.value.length === 0 && table_number.value && capacity.value) {
          updateNewTablePreview()
        }
      }
    }

    const onTableAddedFromSidebar = async (event) => {
      const addedTableData = allTables.value[event.newIndex] // Sử dụng allTables ở đây
      if (!addedTableData || !addedTableData.table_number || !addedTableData.capacity) {
        toast.error(' Vui lòng nhập số bàn và số ghế trước khi thêm bàn.')
        allTables.value.splice(event.newIndex, 1) // Sử dụng allTables ở đây
        return
      }

      try {
        const response = await axios.post(`http://127.0.0.1:8000/api/insert-table`, {
          table_number: addedTableData.table_number,
          capacity: addedTableData.capacity,
        })

        toast.success(`Đã thêm bàn thành công!`)

        if (response.data && response.data.id) {
          addedTableData.id = response.data.id
        }

        await getTable()
        resetNewTableForm()
      } catch (error) {
        console.error('Lỗi API khi thêm bàn:', error)
        if (error.response.status === 422 && error.response.data && error.response.data.errors) {
          let validationErrors = ''
          for (const field in error.response.data.errors) {
            validationErrors += error.response.data.errors[field].join(' ') + ' '
          }
          toast.error(`Lỗi dữ liệu: ${validationErrors.trim()}`)
        }
      }
    }

    const deleteTable = async (table_id) => {
      try {
        await axios.delete(`http://127.0.0.1:8000/api/tables/${table_id}`)
        await getTable()
        toast.success('Xoá bàn thành công')
      } catch (error) {
        console.log('Lỗi ' + error)
        toast.error('Xoá bàn không thành công')
      }
    }

    const loadTable = (id) => {
      if (selectedTableId.value === id) {
        selectedTableId.value = null
        toggleSidebar()
        resetNewTableForm()
      } else {
        const selected = allTables.value.find((t) => t.id === id)
        if (selected) {
          selectedTableId.value = selected.id
          table_number.value = selected.table_number
          capacity.value = selected.capacity
          updateNewTablePreview()
          if (!isSidebarOpen.value) {
            toggleSidebar()
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
          status: 'Bàn trống',
        }
        newTablesQueue.value = [tempTable]
      } else {
        newTablesQueue.value = []
      }
    }

    onMounted(() => {
      getTable()
      window.addEventListener('resize', updateView)
    })
    onBeforeUnmount(() => {
      window.removeEventListener('resize', updateView); // Dọn dẹp listener khi component bị hủy
    });
    return {
      tables: displayTables,
      getChairCount,
      getTable,
      changeTable,
      selectedTableId,
      orderId,
      isSidebarOpen,
      toggleSidebar,
      newTablesQueue,
      onNewTableDragEnd,
      onTableAddedFromSidebar,
      addNewTable,
      table_number,
      capacity,
      updateNewTablePreview,
      loadTable,
      deleteTable,
      date,
      today,
      filterStatus,
      currentPage,
      totalPagesTables,
      goToPage,
      displayTables,
      isMobileView,
      isLoading
    }
  },
}
</script>

<style scoped>
/* --- Base Layout & Main Content --- */
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
  margin: 8px 0;
  width: 100%;
}

.chair {
  width: 40px;
  height: 6px;
  background-color: #ddd;
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

.vacant {
  background-color: #f4f4f4;
  /* Bàn trống */
  border: 5px solid #ddd;
  /* Đồng bộ border với table-rect */
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
