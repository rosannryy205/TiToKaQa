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
                    <div :class="['icon mb-2', item.iconClass]">
                      <component :is="item.icon" style="font-size: 24px" />
                    </div>
                    <h3 class="info-title">
                      <animated-number :value="item.value" />
                    </h3>
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
            <h5 class="card-category">Active Users</h5>
            <h2 class="card-title">
              <animated-number :value="34252"> </animated-number>
            </h2>
          </div>
          <div class="chart-area">
            <line-chart :labels="charts.activeUsers.labels" :data="charts.activeUsers.data"
              :color="charts.activeUsers.color" :height="200" />

          </div>
          <div class="table-responsive">
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
          </div>
          <div slot="footer" class="stats">
            <i class="now-ui-icons arrows-1_refresh-69"></i> Just Updated
          </div>
        </card>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Card from '@/components/Admin/Cards/Card.vue';
import AnimatedNumber from '@/components/Admin/AnimatedNumber.vue'
import LineChart from '@/components/Admin/Charts/LineChart';

import { h } from 'vue'
import {
  MessageOutlined,
  DollarCircleOutlined,
  UsergroupAddOutlined,
  CustomerServiceOutlined,
} from '@ant-design/icons-vue';

import { Table } from 'ant-design-vue'
import { Permission } from '@/stores/permission'

const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) {
    userId.value = user.id
  }
}
const { hasPermission, permissions } = Permission(userId)
const stats = [
  {
    title: 'Messages',
    value: 853,
    icon: MessageOutlined,
    iconClass: 'text-primary',
  },
  {
    title: 'Today Revenue',
    value: 3521,
    icon: DollarCircleOutlined,
    iconClass: 'text-success',
  },
  {
    title: 'Customers',
    value: 562,
    icon: UsergroupAddOutlined,
    iconClass: 'text-info',
  },
  {
    title: 'Support Requests',
    value: 353,
    icon: CustomerServiceOutlined,
    iconClass: 'text-danger',
  },
]
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

</script>

<style scoped>
.icon i {
  font-size: 30px;
}
</style>
