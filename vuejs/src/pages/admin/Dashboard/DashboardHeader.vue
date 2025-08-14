<template>
  <!-- <div class="panel-header "> -->
  <canvas ref="canvasRef" :height="255"></canvas>
  <!-- </div> -->
</template>

<script setup>
import { ref, onMounted } from 'vue'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LineController,
  LinearScale,
  CategoryScale,
  PointElement,
  Filler,
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  LineController,
  LineElement,
  PointElement,
  LinearScale,
  CategoryScale,
  Tooltip,
  Legend,
  Title,
  Filler,
)

// Chart data
const chartData = [
  50, 150, 100, 290, 130, 90,
  150, 300, 120, 90, 270, 450
]

const chartLabels = [
  'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN',
  'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'
]

const canvasRef = ref(null)

onMounted(() => {
  const ctx = canvasRef.value.getContext('2d')
  const chartColor = '#FFFFFF'

  // const gradientStroke = ctx.createLinearGradient(500, 0, 100, 0)
  // gradientStroke.addColorStop(0, '#80b6f4')
  // gradientStroke.addColorStop(1, chartColor)

  const gradientFill = ctx.createLinearGradient(0, 200, 0, 50)
  gradientFill.addColorStop(0, 'rgba(128, 182, 244, 0)')
  gradientFill.addColorStop(1, 'rgba(255, 255, 255, 0.3)')

  new ChartJS(ctx, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [
        {
          label: 'Doanh thu',
          data: chartData,
          borderColor: chartColor,
          backgroundColor: gradientFill,
          pointBorderColor: chartColor,
          pointBackgroundColor: '#872341',
          pointHoverBackgroundColor: '#872341',
          pointHoverBorderColor: chartColor,
          pointBorderWidth: 1,
          pointHoverRadius: 7,
          pointHoverBorderWidth: 2,
          pointRadius: 5,
          fill: true,
          borderWidth: 2,
          tension: 0.4
        }
      ]
    },
    options: {
      layout: {
        padding: {
          left: 20,
          right: 20,
          top: 0,
          bottom: 0
        }
      },
      maintainAspectRatio: false,
      plugins: {
        tooltip: {
          backgroundColor: 'rgba(255, 255, 255, 0.85)',
          titleColor: '#000',
          bodyColor: '#333',
          bodySpacing: 4,
          padding: 12,
        },
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          ticks: {
            color: 'rgba(255, 255, 255, 0.75)',
            font: {
              weight: 'bold'
            },
            beginAtZero: true,
            maxTicksLimit: 6,
            padding: 10
          },
          grid: {
            drawTicks: true,
            drawBorder: false,
            color: 'rgba( 255, 255, 255, 0.25)',
            zeroLineColor: 'transparent'
          }
        },
        x: {
          ticks: {
            padding: 10,
            color: 'rgba( 255, 255, 255, 0.6)',
            font: {
              weight: 'bold'
            }
          },
          grid: {
            display: false,          // tắt grid lines
            drawTicks: false,        // tắt tick nhỏ
            drawOnChartArea: false,  // không vẽ trên vùng chart
            drawBorder: false,
            color: 'transparent'
          }
        }
      }
    }
  })

})

</script>
