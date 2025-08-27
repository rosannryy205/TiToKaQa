<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-stats card-raised">
        <div class="card-body">
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
              <p class="mb-1">
                <strong>📝 Ghi chú:</strong>
                {{ order?.data?.note ? order.data.note : 'Không có' }}
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
            <div class="action-buttons mt-4" v-if="hasPermission('edit_shipper')">

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
        </div>
      </div>
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
import Swal from 'sweetalert2'
import { set, ref as dbRef } from 'firebase/database'
import { database } from '@/stores/firebase'
import { remove } from 'firebase/database'
import { Permission } from '@/stores/permission'
import { API_URL } from '@/config'
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
const userId = ref(null)
const userString = localStorage.getItem('user')
if (userString) {
  const user = JSON.parse(userString)
  if (user && user.id !== undefined) userId.value = user.id
}
const { hasPermission } = Permission(userId)
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

const updateMap = async () => {
  const address = order.value.data.guest_address
  const coords = await getCoordinatesFromAddress(address)
  if (!coords) return console.warn('Không tìm thấy tọa độ từ địa chỉ.')

  customer.value = coords

  const lastPos = JSON.parse(localStorage.getItem('lastShipperPosition'))
  const startPoint = lastPos || restaurant.value

  const { coords: polylineCoords, distance } = await getRoutePolyline(startPoint, customer.value)
  if (!polylineCoords.length) return

  if (routeLine) {
    map.removeLayer(routeLine)
    routeLine = null
  }
  if (shipperMarker) {
    map.removeLayer(shipperMarker)
    shipperMarker = null
  }

  L.marker([customer.value.lat, customer.value.lng])
    .addTo(map)
    .bindPopup('<b>👤 Khách hàng</b>')

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
    const latlngs = routeLine.getLatLngs()
    let savedPos = null

    if (order.value.data.order_status === 'Đang giao hàng') {
      savedPos = JSON.parse(localStorage.getItem('currentShipperPosition'))
    }

    let startIndex = 0
    if (savedPos) {
      let minDistance = Infinity
      latlngs.forEach((point, idx) => {
        const dist = Math.sqrt(
          Math.pow(point.lat - savedPos.lat, 2) + Math.pow(point.lng - savedPos.lng, 2)
        )
        if (dist < minDistance) {
          minDistance = dist
          startIndex = idx
        }
      })
    }

    const remainingRoute = latlngs.slice(startIndex)

    shipperMarker = new L.AnimatedMarker(remainingRoute, {
      icon: shipperIcon,
      autoStart: true,
      distance: 80,
      interval: 150,
      onEnd: () => {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Đã đến điểm giao',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        })
        localStorage.removeItem('currentShipperPosition')
      }
    })

    shipperMarker.on('move', (e) => {
      const latlng = e.latlng
      localStorage.setItem('currentShipperPosition', JSON.stringify(latlng))

      const shipperId = JSON.parse(localStorage.getItem('user'))?.id
      const locationRef = dbRef(database, `locations/shipper_${shipperId}`)
      set(locationRef, {
        lat: latlng.lat,
        lng: latlng.lng,
        timestamp: Date.now()
      })
    })
  } else {
    shipperMarker = L.marker([startPoint.lat, startPoint.lng], { icon: shipperIcon })
  }

  map.addLayer(shipperMarker)
}

const changeStatus = async (newStatus) => {
  try {
    const response = await axios.put(`${API_URL}/update/${order_id}/status`, {
      order_status: newStatus
    })

    if (response.data.success) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Cập nhật thành công',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      })

      if (newStatus === 'Giao thành công' || newStatus === 'Giao thất bại') {
        const shipperId = JSON.parse(localStorage.getItem('user'))?.id
        const res = await axios.get(`${API_URL}/shipper/${shipperId}/active-orders`)
        const remainingOrders = res.data.orders || []

        const newPos = remainingOrders.length < 1 ? restaurant.value : customer.value

        await axios.post(`${API_URL}/shipper/update-location`, {
          shipper_id: shipperId,
          lat: newPos.lat,
          lng: newPos.lng,
        })

        if (remainingOrders.length < 1) {
          localStorage.removeItem('lastShipperPosition')
        } else {
          localStorage.setItem('lastShipperPosition', JSON.stringify(newPos))
        }

        localStorage.removeItem('currentShipperPosition')

        const locationRef = dbRef(database, `locations/shipper_${shipperId}`)
        await set(locationRef, null)

        setTimeout(() => {
          goBack()
        }, 800)
      } else {
        await fetchOrder()
        await updateMap()
      }
    } else {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: 'Cập nhật thất bại',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      })
    }
  } catch (error) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'error',
      title: 'Lỗi hệ thống',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true
    })
    console.error('Lỗi cập nhật trạng thái:', error)
  }
}

const fetchOrder = async () => {
  const res = await axios.get(`${API_URL}/delivery/${order_id}`)
  order.value = res.data
}

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

const getRoutePolyline = async (start, end) => {
  const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImY2N2UzNGY1YmFmZWVhODlmZmQyZTI4M2M0YjVjNTZjNGQxYTcyZjI4Yzg3YjRiYzIwNDk0ZmZlIiwiaCI6Im11cm11cjY0In0='
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

onMounted(async () => {
  isLoading.value = true
  try {
    initMap()
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
  width: 100%;
  max-width: 480px;
  margin: 0 auto;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 600;
  font-size: 16px;
  padding: 12px;
  border-radius: 40px;
  border: none;
  color: white;
  transition: all 0.2s ease;
  cursor: pointer;
  height: 56px;
  /* Khớp SwipeToConfirm */
  width: 100%;
  max-width: 100%;
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

.action-btn.back:hover {
  background-color: #5a6268;
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
