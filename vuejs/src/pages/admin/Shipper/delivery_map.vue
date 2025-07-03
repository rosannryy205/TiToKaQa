<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <div class="delivery-container px-3 py-4">
    <h4 class="text-primary fw-bold mb-3">
      <i class="bi bi-geo-alt-fill me-2"></i>Hành trình giao hàng
    </h4>

    <!-- Button chọn đơn hàng -->


    <!-- Thông tin khách hàng -->
    <div class="bg-white rounded-3 shadow-sm p-3 mb-3">
      <p class="mb-1"><strong>👤 Khách:</strong> {{ order?.data?.guest_name }}</p>
      <p class="mb-1"><strong>📍 Địa chỉ:</strong> {{ order?.data?.guest_address }}</p>
      <p class="mb-0">
        <strong>📞 SĐT: </strong>
        <span class="text-decoration-none text-primary">
          {{ order?.data?.guest_phone }}
        </span>
      </p>
    </div>

    <!-- Bản đồ giao hàng -->
    <div id="deliveryMap" class="map-box position-relative mb-3">
      <div id="distanceBox" v-show="showDistanceBox"
        class="position-absolute top-0 start-0 m-3 bg-white px-3 py-2 rounded shadow text-dark fw-semibold">
        0 km
      </div>
    </div>

    <!-- Nút thao tác -->
    <div class="action-buttons mt-4">

      <SwipeToConfirm v-if="order?.data?.order_status === 'Bắt đầu giao'" label="Bắt đầu giao" color="#28a745"
        @confirm="() => changeStatus('Đang giao hàng')" />

      <SwipeToConfirm v-if="order?.data?.order_status === 'Đang giao hàng'" label="Xác nhận đã giao"
        color="#007bff" @confirm="() => changeStatus('Giao thành công')" />

      <SwipeToConfirm v-if="order?.data?.order_status === 'Đang giao hàng'" label="Giao thất bại"
        color="#dc3545" @confirm="() => changeStatus('Giao thất bại')" />



      <button class="action-btn back" @click="goBack">
        <i class="bi bi-arrow-left"></i>
        Quay lại
      </button>
    </div>


  </div>
</template>


<script setup>
import SwipeToConfirm from '@/components/SwipeToConfirm.vue'
import '@/stores/animated-marker'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { toast } from 'vue3-toastify'

const goBack = () => window.history.back()
const route = useRoute()
const order_id = route.params.id

const order = ref({})
const isLoading = ref(false)
const showDistanceBox = ref(false)

const restaurant = ref({ lat: 10.854113664188024, lng: 106.6262030926953 })
const customer = ref({})
const shipper = ref({ lat: 10.854113664188024, lng: 106.6262030926953 })

let map = null
let shipperMarker = null
let routeLine = null

// Tạo map chỉ 1 lần
const initMap = () => {
  map = L.map('deliveryMap', {
    zoomControl: false
  }).setView([restaurant.value.lat, restaurant.value.lng], 13)

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://carto.com/">CARTO</a> contributors'
  }).addTo(map)

  L.control.zoom({ position: 'bottomright' }).addTo(map)

  L.marker([restaurant.value.lat, restaurant.value.lng])
    .addTo(map)
    .bindPopup('<b>🏠 Nhà hàng</b>')
}

// Cập nhật marker, route và shipper theo order mới
const updateMap = async () => {
  const address = order.value.data.guest_address
  const coords = await getCoordinatesFromAddress(address)
  if (!coords) return console.warn('Không tìm thấy tọa độ từ địa chỉ.')

  customer.value = coords

  const lastPos = JSON.parse(localStorage.getItem('lastShipperPosition'))
  const startPoint = lastPos || restaurant.value // fallback về nhà hàng

  // Xóa route cũ
  if (routeLine) {
    map.removeLayer(routeLine)
    routeLine = null
  }

  // Xóa shipper cũ
  if (shipperMarker) {
    map.removeLayer(shipperMarker)
    shipperMarker = null
  }

  // Thêm marker khách
  L.marker([customer.value.lat, customer.value.lng])
    .addTo(map)
    .bindPopup('<b>👤 Khách hàng</b>')

  const { coords: polylineCoords, distance } = await getRoutePolyline(startPoint, customer.value)

  if (!polylineCoords.length) return

  routeLine = L.polyline(polylineCoords, {
    color: '#C92C3C',
    weight: 6,
    opacity: 0.85,
    smoothFactor: 1
  }).addTo(map)

  map.fitBounds(routeLine.getBounds(), { padding: [20, 20] })

  const distanceInKm = (distance / 1000).toFixed(2)
  const distanceBox = document.getElementById('distanceBox')
  if (distanceBox) {
    distanceBox.textContent = `${distanceInKm} km`
    showDistanceBox.value = true
  }

  const shipperIcon = L.icon({
    iconUrl: '/shipper.png',
    iconSize: [50, 50],
    iconAnchor: [25, 25],
    popupAnchor: [0, -20]
  })

  if (order.value.data.order_status === 'Đang giao hàng') {
    shipperMarker = new L.AnimatedMarker(routeLine.getLatLngs(), {
      icon: shipperIcon,
      autoStart: true,
      distance: 80,
      interval: 150, //150 // 720
      onEnd: () => {
        toast.success('Đã đến điểm giao')
      }
    })
  } else {
    shipperMarker = L.marker([startPoint.lat, startPoint.lng], { icon: shipperIcon })
  }

  map.addLayer(shipperMarker)
}

// API: Đổi trạng thái
const changeStatus = async (newStatus) => {
  try {
    const response = await axios.put(`http://127.0.0.1:8000/api/update/${order_id}/status`, {
      order_status: newStatus
    })


    if (response.data.success) {
      toast.success('Cập nhật thành công')
      if (newStatus === 'Giao thành công' || newStatus === 'Giao thất bại') {
        const shipperId = JSON.parse(localStorage.getItem('user'))?.id
        const res = await axios.get(`http://127.0.0.1:8000/api/shipper/${shipperId}/active-orders`)
        const remainingOrders = res.data.orders || []

        const newPos = remainingOrders.length < 1 ? restaurant.value : customer.value

        await axios.post('http://127.0.0.1:8000/api/shipper/update-location', {
          shipper_id: shipperId,
          lat: newPos.lat,
          lng: newPos.lng,
        })

        if (remainingOrders.length < 1) {
          localStorage.removeItem('lastShipperPosition')
        } else {
          localStorage.setItem('lastShipperPosition', JSON.stringify(newPos))
        }


        setTimeout(() => {
          goBack()
        }, 800)
      } else {
        await fetchOrder()
        await updateMap()
      }
    } else {
      toast.error('Cập nhật thất bại')
    }
  } catch (error) {
    toast.error('Lỗi hệ thống')
    console.error('Lỗi cập nhật trạng thái:', error)
  }
}

// API: Lấy đơn hàng
const fetchOrder = async () => {
  const res = await axios.get(`http://127.0.0.1:8000/api/delivery/${order_id}`)
  order.value = res.data
}

// Đổi địa chỉ → toạ độ
//Api LocationIQ
const getCoordinatesFromAddress = async (address) => {
  const apiKey = 'pk.a3a8213154230324b5a5b37fd3e5f48a'
  const res = await axios.get('https://us1.locationiq.com/v1/search.php', {
    params: {
      key: apiKey,
      q: address,
      format: 'json',
      limit: 1
    }
  })
  if (res.data.length > 0) {
    const { lat, lon } = res.data[0]
    return { lat: parseFloat(lat), lng: parseFloat(lon) }
  }
  return null
}

// API: Vẽ tuyến đường
//Api Heigit
const getRoutePolyline = async (start, end) => {
  const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: '5b3ce3597851110001cf62482b60c4bf4dd35899168bdb73789d885e63b65a8ba7f4add869673f46'
    },
    body: JSON.stringify({
      coordinates: [
        [start.lng, start.lat],
        [end.lng, end.lat]
      ]
    })
  })
  const data = await response.json()
  if (!data.features?.length) return { coords: [], distance: 0 }
  const coords = data.features[0].geometry.coordinates.map(coord => [coord[1], coord[0]])
  const distance = data.features[0].properties.summary.distance
  return { coords, distance }
}

// mounted
onMounted(async () => {
  isLoading.value = true
  try {
    initMap() // map tạo ngay
    await fetchOrder()
    await updateMap()
  } catch (error) {
    console.error('Lỗi khi khởi tạo:', error)
  } finally {
    isLoading.value = false
  }
})
</script>



<style scoped>
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 600;
  font-size: 16px;
  padding: 12px;
  border-radius: 12px;
  border: none;
  color: white;
  transition: all 0.2s ease;
  cursor: pointer;
}

.action-btn i {
  font-size: 18px;
}

/* Màu riêng cho từng nút */
.action-btn.start {
  background-color: #28a745;
}

.action-btn.delivered {
  background-color: #007bff;
}

.action-btn.problem {
  background-color: #dc3545;
}

.action-btn.back {
  background-color: #6c757d;
}

/* Hover effect */
.action-btn:hover {
  filter: brightness(1.1);
}

@media (max-width: 768px) {
  .action-btn {
    font-size: 15px;
    padding: 10px;
  }
}


#deliveryMap {
  height: 450px;
  width: 100%;
}


.map-box {
  height: 400px;
  width: 100%;
  background-color: #f1f3f5;
  border-radius: 16px;
  overflow: hidden;
}

#distanceBox {
  font-size: 14px;
  z-index: 1000;
}

.isLoading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(255, 255, 255, 0.85);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}

.delivery-container {
  max-width: 720px;
  margin: 0 auto;
}

@media (max-width: 768px) {
  .map-box {
    height: 320px;
  }

  .btn {
    font-size: 1rem;
    padding: 10px 16px;
  }
}
</style>
