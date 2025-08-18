<template>
  <canvas :id="props.chartId" :height="props.height" ref="chartRef"></canvas>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Tooltip } from 'chart.js';
import { hexToRGB } from './utils';

// Đăng ký Chart.js components
Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Tooltip);

const props = defineProps({
  labels: { type: Array, default: () => [] },
  data: { type: Array, default: () => [] },
  datasets: { type: Array, default: null },
  color: { type: String, default: '#f96332' },
  extraOptions: { type: Object, default: () => ({}) },
  title: { type: String, default: '' },
  height: { type: Number, default: 200 },
  chartId: { type: String, default: 'line-chart' }
});

const chartRef = ref(null);
let chartInstance = null;

const chartColor = '#FFFFFF';
const gradientChartOptionsConfiguration = {
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      bodySpacing: 4,
      mode: 'nearest',
      intersect: false,
      position: 'nearest',
      padding: 10
    }
  },
  responsive: true,
  scales: {
    y: { display: false, grid: { drawTicks: false, display: false, drawBorder: false } },
    x: { display: false, grid: { drawTicks: false, display: false, drawBorder: false } }
  },
  layout: { padding: { left: 0, right: 0, top: 15, bottom: 15 } }
};

const renderChart = () => {
  if (!chartRef.value) return;
  const ctx = chartRef.value.getContext('2d');

  const gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
  gradientStroke.addColorStop(0, props.color);
  gradientStroke.addColorStop(1, chartColor);

  const gradientFill = ctx.createLinearGradient(0, 170, 0, 50);
  gradientFill.addColorStop(0, 'rgba(128, 182, 244, 0)');
  gradientFill.addColorStop(1, hexToRGB(props.color, 0.4));

  const chartDatasets = props.datasets || [
    {
      label: props.title,
      borderColor: props.color,
      pointBorderColor: '#FFF',
      pointBackgroundColor: props.color,
      pointBorderWidth: 2,
      pointHoverRadius: 4,
      pointHoverBorderWidth: 1,
      pointRadius: 4,
      fill: true,
      backgroundColor: gradientFill,
      borderWidth: 2,
      tension: 0.4,
      data: props.data
    }
  ];

  const chartOptions = { ...gradientChartOptionsConfiguration, ...props.extraOptions };

  // Hủy chart cũ nếu tồn tại
  if (chartInstance) chartInstance.destroy();

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: props.labels,
      datasets: chartDatasets
    },
    options: chartOptions
  });
};

onMounted(renderChart);

watch(
  () => [props.labels, props.data, props.datasets],
  () => renderChart(),
  { deep: true }
);
</script>
