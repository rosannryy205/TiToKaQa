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
  50, 150, 100, 190, 130, 90,
  150, 160, 120, 140, 190, 95
]

const chartLabels = [
  'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN',
  'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'
]

const canvasRef = ref(null)

onMounted(() => {
  const ctx = canvasRef.value.getContext('2d')
  const chartColor = '#FFFFFF'

  const gradientStroke = ctx.createLinearGradient(500, 0, 100, 0)
  gradientStroke.addColorStop(0, '#80b6f4')
  gradientStroke.addColorStop(1, chartColor)

  const gradientFill = ctx.createLinearGradient(0, 200, 0, 50)
  gradientFill.addColorStop(0, 'rgba(128, 182, 244, 0)')
  gradientFill.addColorStop(1, 'rgba(255, 255, 255, 0.24)')

  new ChartJS(ctx, {
    type: 'line',
    data: {
      labels: chartLabels,
      datasets: [
        {
          label: 'Data',
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
          backgroundColor: '#fff',
          titleColor: '#333',
          bodyColor: '#666',
          bodySpacing: 4,
          padding: 12
        },
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          ticks: {
            color: 'rgba(255,255,255,0.4)',
            font: {
              weight: 'bold'
            },
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 10
          },
          grid: {
            drawTicks: true,
            drawBorder: false,
            color: 'rgba(255,255,255,0.1)',
            zeroLineColor: 'transparent'
          }
        },
        x: {
          ticks: {
            padding: 10,
            color: 'rgba(255,255,255,0.4)',
            font: {
              weight: 'bold'
            }
          },
          grid: {
            display: false
          }
        }
      }
    }
  })

})

</script>
