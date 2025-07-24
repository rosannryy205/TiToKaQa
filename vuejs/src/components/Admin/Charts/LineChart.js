import { h, defineComponent, computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale
} from 'chart.js'
import { hexToRGB } from './utils'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale
)

export default defineComponent({
  name: 'LineChart',
  props: {
    labels: Array,
    datasets: Array,
    data: Array,
    color: String,
    extraOptions: Object,
    title: String,
    height: {
      type: [String, Number],
      default: 200
    }
  },
  setup(props) {
    const fallbackColor = '#f96332'
    const chartColor = props.color || fallbackColor

    const chartData = computed(() => {
      const dataset = {
        label: props.title || '',
        borderColor: chartColor,
        pointBorderColor: '#FFF',
        pointBackgroundColor: chartColor,
        pointBorderWidth: 2,
        pointHoverRadius: 4,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        fill: true,
        backgroundColor: hexToRGB(chartColor, 0.4),
        borderWidth: 2,
        data: props.data || [],
        tension: 0.4
      }

      return {
        labels: props.labels || [],
        datasets: props.datasets || [dataset]
      }
    })

    const chartOptions = computed(() => ({
      maintainAspectRatio: false,
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          mode: 'nearest',
          intersect: false,
          padding: 10
        }
      },
      scales: {
        y: {
          display: false,
          grid: {
            display: false,
            drawBorder: false
          }
        },
        x: {
          display: false,
          grid: {
            display: false,
            drawBorder: false
          }
        }
      },
      layout: {
        padding: { left: 0, right: 0, top: 15, bottom: 15 }
      },
      ...props.extraOptions
    }))

    return () =>
      h(Line, {
        data: chartData.value,
        options: chartOptions.value,
        height: props.height
      })
  }
})
