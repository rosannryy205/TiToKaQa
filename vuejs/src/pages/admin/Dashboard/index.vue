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
            <h2 class="card-title">
              <animated-number :value="34252"> </animated-number>
            </h2>
            <Dropdown :hide-arrow="true" position="right">
              <template #title>
                <n-button class="dropdown-toggle no-caret" round simple icon>
                  <SettingOutlined />
                </n-button>
              </template>

              <a class="dropdown-items" href="#">Ngày hiện tại</a>
              <a class="dropdown-items" href="#">Bộ lọc</a>
            </Dropdown>
          </div>
          <div class="chart-area">
            <LineChart :labels="charts.activeUsers.labels" :data="charts.activeUsers.data"
              :color="charts.activeUsers.color" :height="200" />
          </div>
          <!-- <div class="table-responsive">
            <Table :data-source="data" :pagination="false" row-key="id" bordered>
              <template #bodyCell="{ column, record }">
                <td>
                  <div class="flag">
                    <img :src="record.flag" />
                  </div>
                </td>
                <td>{{ record.country }}</td>
                <td class="text-right">
                  {{ record.value }}
                </td>
                <td class="text-right">
                  {{ record.percentage }}
                </td>
              </template>
            </Table>
          </div> -->
          <div slot="footer" class="stats">
            <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
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
import {
  ShoppingCartOutlined,
  DollarCircleOutlined,
  UserOutlined,
  ShoppingOutlined,
  SettingOutlined
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
    const res = await axios.get('http://127.0.0.1:8000/api/admin/get-dashboard-stats')
    OrdersToday.value = res.data.orders_today
    RevenueToday.value = res.data.revenue_today
    ReservationsToday.value = res.data.reservations_today
    BestSellingDish.value = res.data.best_selling_dish

    stats.value = [
      {
        title: 'Đơn hàng hôm nay',
        value: OrdersToday.value,
        icon: ShoppingCartOutlined,
        iconClass: 'text-primary',
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
        iconClass: 'text-info',
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
  activeUsers: {
    labels: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "May",
      "Jun",
      "Jul",
      "Aug",
      "Sep",
      "Oct",
      "Nov",
      "Dec",
    ],
    data: [542, 480, 430, 550, 530, 453, 380, 434, 568, 610, 700, 630],
    color: '#f96332'
  }
})
const columns = [
  { title: 'Quốc kỳ', dataIndex: 'flag', key: 'flag' },
  { title: 'Quốc gia', dataIndex: 'country', key: 'country' },
  { title: 'Giá trị', dataIndex: 'value', key: 'value' },
  { title: 'Phần trăm', dataIndex: 'percentage', key: 'percentage' },
]


const data = ref([
  {
    flag: 'https://flagcdn.com/vn.svg',
    country: 'Việt Nam',
    value: 1000,
    percentage: 60
  },
  {
    flag: 'https://flagcdn.com/kr.svg',
    country: 'Hàn Quốc',
    value: 800,
    percentage: 40
  },
  {
    flag: "https://flagcdn.com/us.svg",
    country: "Mỹ",
    value: "2.920",
    percentage: "53.23%",
  },
  {
    flag: "https://flagcdn.com/de.svg",
    country: "Đức",
    value: "1.300",
    percentage: "20.43%",
  },
  {
    flag: "https://flagcdn.com/au.svg",
    country: "Úc",
    value: "760",
    percentage: "10.35%",
  },
])
onMounted(fetchStats)
</script>

<style lang="scss" scoped>
@import "../../../assets/sass/now-ui-dashboard/_variables.scss";

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
