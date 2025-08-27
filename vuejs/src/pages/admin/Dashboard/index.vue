<template v-if="hasPermission('view_dashboard')">
  <div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-stats card-raised">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3" v-for="item in stats" :key="item.title">
                <div class="statistics">
                  <div class="info">
                    <div :class="['icon', item.iconClass]">
                      <component :is="item.icon" style="font-size: 24px" />
                    </div>
                    <h4 class="info-title">
                      <template v-if="item.title === 'Món bán chạy nhất hôm nay'">
                        <span>{{ item.value }}</span>
                      </template>
                      <template v-else-if="item.title === 'Doanh Thu Hôm Nay'">
                        <animated-number :value="Number(item.value)" format="currency" />
                      </template>
                      <template v-else>
                        <animated-number :value="Number(item.value)" format="number" />
                      </template>
                    </h4>
                    <h6 class="stats-title">{{ item.title }}</h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <card class="card-chart" no-footer-line>
          <div slot="header">
            <h5 class="card-category">Thống kê người dùng</h5>
            <h2 class="card-title d-flex flex-wrap align-items-center gap-2">
              <TeamOutlined style="color: #f96332;" />
              <animated-number :value="totalUsers" class="ms-2 me-2 fw-bold fs-2"></animated-number>
              <small class="text-muted fs-6">(Tổng người dùng)</small>
            </h2>
            <h4 class="d-flex flex-wrap align-items-center gap-2">
              <animated-number :value="filteredUsers" class="ms-2 me-2 fs-5">
              </animated-number>
              <small class="text-muted fs-6">({{ filterLabel }})</small>
            </h4>
            <Dropdown :hide-arrow="true" position="right">
              <template #title>
                <n-button class="dropdown-toggle no-caret" round simple icon>
                  <SettingOutlined />
                </n-button>
              </template>
              <!-- <div class="dropdown-menu-custom"> -->
              <a class="dropdown-items" href="#" @click.prevent="fetchStatsUsersByTime('today')">
                <CalendarOutlined class="me-2" /> Ngày hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="fetchStatsUsersByTime('this_week')">
                <ScheduleOutlined class="me-2" /> Tuần hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="fetchStatsUsersByTime('month')">
                <FieldTimeOutlined class="me-2" /> Tháng hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="fetchStatsUsersByTime('year')">
                <LineChartOutlined class="me-2" /> Năm hiện tại
              </a>
              <!-- </div> -->
            </Dropdown>
          </div>
          <div class="chart-area">
            <LineChart :labels="charts.users.labels" :data="charts.users.data" :color="charts.users.color"
              :height="200" />
          </div>
        </card>
      </div>
      <div class="col-lg-4">
        <card class="card-chart" no-footer-line>
          <div slot="header">
            <h5 class="card-category">Thống kê đặt bàn</h5>
            <h2 class="card-title d-flex flex-wrap align-items-baseline">
              <BarChartOutlined style="color: #2CA8FF;" />
              <animated-number :value="totalRes" class="ms-2 me-2 fw-bold fs-2"></animated-number>
              <small class="text-muted fs-6">(Tổng bàn đã đặt)</small>
            </h2>
            <h4 class="d-flex flex-wrap align-items-center gap-2">
              <animated-number :value="filteredRes" class="ms-2 me-2 fs-5">
              </animated-number>
              <small class="text-muted fs-6">({{ filterLabelRes }})</small>
            </h4>
            <Dropdown :hide-arrow="true" position="right">
              <template #title>
                <n-button class="dropdown-toggle no-caret" round simple icon>
                  <SettingOutlined />
                </n-button>
              </template>
              <!-- <div class="dropdown-menu-custom"> -->
              <a class="dropdown-items" href="#" @click.prevent="getResStats('today')">
                <CalendarOutlined class="me-2" /> Ngày hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="getResStats('this_week')">
                <ScheduleOutlined class="me-2" /> Tuần hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="getResStats('month')">
                <FieldTimeOutlined class="me-2" /> Tháng hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="getResStats('year')">
                <LineChartOutlined class="me-2" /> Năm hiện tại
              </a>
              <!-- </div> -->
            </Dropdown>
          </div>
          <div class="chart-area">
            <LineChart :labels="charts.reservations.labels" :data="charts.reservations.data"
              :color="charts.reservations.color" :height="200" />
          </div>
        </card>
      </div>
      <div class="col-lg-4">
        <card class="card-chart" no-footer-line>
          <div slot="header">
            <h5 class="card-category">Thống kê đơn hàng</h5>
            <h2 class="card-title d-flex flex-wrap align-items-baseline">
              <ShoppingCartOutlined style="color: #18ce0f;" />
              <animated-number :value="totalOrder" class="ms-2 me-2 fw-bold fs-2"></animated-number>
              <small class="text-muted fs-6">(Tổng đơn hàng đã đặt)</small>
            </h2>
            <h4 class="d-flex flex-wrap align-items-center gap-2">
              <animated-number :value="filteredOrder" class="ms-2 me-2 fs-5">
              </animated-number>
              <small class="text-muted fs-6">({{ filterLabelOrder }})</small>
            </h4>
            <Dropdown :hide-arrow="true" position="right">
              <template #title>
                <n-button class="dropdown-toggle no-caret" round simple icon>
                  <SettingOutlined />
                </n-button>
              </template>
              <!-- <div class="dropdown-menu-custom"> -->
              <a class="dropdown-items" href="#" @click.prevent="getOrderStats('today')">
                <CalendarOutlined class="me-2" /> Ngày hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="getOrderStats('this_week')">
                <ScheduleOutlined class="me-2" /> Tuần hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="getOrderStats('month')">
                <FieldTimeOutlined class="me-2" /> Tháng hiện tại
              </a>
              <a class="dropdown-items" href="#" @click.prevent="getOrderStats('year')">
                <LineChartOutlined class="me-2" /> Năm hiện tại
              </a>
              <!-- </div> -->
            </Dropdown>
          </div>
          <div class="chart-area">
            <LineChart :labels="charts.orders.labels" :data="charts.orders.data" :color="charts.orders.color"
              :height="200" />
          </div>
        </card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios';
import Card from '@/components/Admin/Cards/Card.vue';
import AnimatedNumber from '@/components/Admin/AnimatedNumber.vue'
import Dropdown from '@/components/Admin/Dropdown.vue'
import NButton from '@/components/Admin/Button.vue'
import LineChart from '@/components/Admin/Charts/LineChart.vue';
import { Table } from 'ant-design-vue'
import { Permission } from '@/stores/permission'
import { h } from 'vue'
import { API_URL } from '@/config';
import {
  ShoppingCartOutlined,
  DollarCircleOutlined,
  UserOutlined,
  ShoppingOutlined,
  SettingOutlined,
  CalendarOutlined,
  ScheduleOutlined,
  FieldTimeOutlined,
  LineChartOutlined,
  TeamOutlined,
  BarChartOutlined,
} from '@ant-design/icons-vue';

const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)

const OrdersToday = ref(0)
const RevenueToday = ref(0)
const ReservationsToday = ref(0)
const BestSellingDish = ref('')

const stats = ref([])

const fetchStats = async () => {
  try {
    const res = await axios.get(`${API_URL}/admin/get-dashboard-stats`)
    OrdersToday.value = res.data.orders_today
    RevenueToday.value = res.data.revenue_today
    ReservationsToday.value = res.data.reservations_today
    BestSellingDish.value = res.data.best_selling_dish

    stats.value = [
      {
        title: 'Đơn hàng hôm nay',
        value: OrdersToday.value,
        icon: ShoppingCartOutlined,
        iconClass: 'text-info',
      },
      {
        title: 'Doanh Thu Hôm Nay',
        value: Number(RevenueToday.value),
        icon: DollarCircleOutlined,
        iconClass: 'text-success',
      },
      {
        title: 'Khách đặt bàn hôm nay',
        value: ReservationsToday.value,
        icon: UserOutlined,
        iconClass: 'text-primary',
      },
      {
        title: 'Món bán chạy nhất hôm nay',
        value: BestSellingDish.value,
        icon: ShoppingOutlined,
        iconClass: 'text-danger',
      },
    ]
  } catch (err) {
    console.error(err)
  }
}

const charts = ref({
  users: { labels: [], data: [], color: '#f96332' },
  reservations: { labels: [], data: [], color: '#2CA8FF' },
  orders: { labels: [], data: [], color: '#18ce0f' }
})

const totalUsers = ref(0)
const filteredUsers = ref(0)
const filterLabel = ref('')
const fetchStatsUsers = async () => {
  try {
    const res = await axios.get(`${API_URL}/admin/get-total-users`)
    totalUsers.value = res.data.total
  } catch (error) {
    console.error(error)
  }
}
const fetchStatsUsersByTime = async (filter = 'today') => {
  try {
    const res = await axios.get(`${API_URL}/admin/stats-user-by-time`,
      { params: { filter } }
    )
    charts.value.users.labels = res.data.labels
    charts.value.users.data = res.data.data
    filteredUsers.value = Array.isArray(res.data.data)
      ? res.data.data.reduce((sum, val) => sum + Number(val), 0)
      : Object.values(res.data.data || {}).reduce((sum, val) => sum + Number(val), 0)
    switch (filter) {
      case 'today':
        filterLabel.value = 'Hôm nay'
        break
      case 'this_week':
        filterLabel.value = 'Tuần này'
        break
      case 'month':
        filterLabel.value = 'Tháng này'
        break
      case 'year':
        filterLabel.value = 'Năm nay'
        break
    }
  } catch (error) {
    console.error(error)
  }
}

const totalRes = ref(0)
const filteredRes = ref(0)
const filterLabelRes = ref('')
const getTotalRes = async () => {
  try {
    const res = await axios.get(`${API_URL}/admin/get-total-res`)
    totalRes.value = res.data.total
  } catch (error) {
    console.error(error)
  }
}
const getResStats = async (filter = 'today') => {
  try {
    const res = await axios.get(`${API_URL}/admin/stats-res-by-time`,
      { params: { filter } }
    )
    charts.value.reservations.labels = res.data.labels
    charts.value.reservations.data = res.data.data
    filteredRes.value = Array.isArray(res.data.data)
      ? res.data.data.reduce((sum, val) => sum + val, 0)
      : 0
    switch (filter) {
      case 'today':
        filterLabelRes.value = 'Hôm nay'
        break
      case 'this_week':
        filterLabelRes.value = 'Tuần này'
        break
      case 'month':
        filterLabelRes.value = 'Tháng này'
        break
      case 'year':
        filterLabelRes.value = 'Năm nay'
        break
    }
  } catch (error) {
    console.error(error)
  }
}

const totalOrder = ref(0)
const filteredOrder = ref(0)
const filterLabelOrder = ref('')
const getTotalOrder = async () => {
  try {
    const res = await axios.get(`${API_URL}/admin/get-total-order`)
    totalOrder.value = res.data.total
  } catch (error) {
    console.error(error)
  }
}
const getOrderStats = async (filter = 'today') => {
  try {
    const res = await axios.get(`${API_URL}/admin/stats-order-by-time`,
      { params: { filter } }
    )
    charts.value.orders.labels = res.data.labels
    charts.value.orders.data = res.data.data
    filteredOrder.value = Array.isArray(res.data.data)
      ? res.data.data.reduce((sum, val) => sum + val, 0)
      : 0
    switch (filter) {
      case 'today':
        filterLabelOrder.value = 'Hôm nay'
        break
      case 'this_week':
        filterLabelOrder.value = 'Tuần này'
        break
      case 'month':
        filterLabelOrder.value = 'Tháng này'
        break
      case 'year':
        filterLabelOrder.value = 'Năm nay'
        break
    }
  } catch (error) {
    console.error(error)
  }
}

onMounted(() => {
  fetchStats()

  fetchStatsUsers()
  fetchStatsUsersByTime()

  getTotalRes()
  getResStats()

  getTotalOrder()
  getOrderStats()
})
</script>

<style lang="scss" scoped>
@use "../../../assets/sass/now-ui-dashboard/variables" as *;

.text-primary {
  color: #0d6efd !important;
}

.info {
  .info-title {
    margin: $margin-base-vertical 0 5px;
    padding: 0 15px;
    color: $black-color;

    span {
      font-weight: $font-weight-bold;
    }
  }
}

.icon {
  color: $default-color;
  transition: transform .4s, box-shadow .4s;

  >span {
    font-size: 2.3em !important;
  }
}

h2,
.h2 {
  font-size: $font-size-h2;
  margin-bottom: $margin-base-vertical * 2;
}

h4,
.h4 {
  font-size: $font-size-h4;
  line-height: 1.45em;
  margin-top: $margin-base-vertical * 2;
  margin-bottom: $margin-base-vertical;

  &+.category,
  &.title+.category {
    margin-top: -10px;
  }
}

h5,
.h5 {
  font-size: $font-size-h5;
  line-height: 1.4em;
  margin-bottom: 15px;
}

h6,
.h6 {
  font-size: $font-size-h6;
  font-weight: $font-weight-bold;
  text-transform: uppercase;
}

.category,
.card-category {
  text-transform: capitalize;
  font-weight: $font-weight-normal;
  color: $dark-gray;
  font-size: $font-size-mini;
}
</style>
