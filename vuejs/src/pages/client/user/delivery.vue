<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>
  <div class="container-sm fade-in container-delivery pt-20">
    <div class="p-4">
      <h2 class="text-2xl font-bold mb-4 text-gray-800">🛵 Theo dõi đơn hàng</h2>
      <div id="deliveryMap" class="relative rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div id="distanceBox" v-show="showDistanceBox"
          class="absolute top-4 left-4 w-[100px] h-[100px] bg-white rounded-lg shadow-md flex items-center justify-center text-gray-800 text-base font-semibold z-[500]">
          0 km
        </div>
      </div>
      <button class="btn btn-secondary mt-2 ml-2 p-2" @click="goBack">Quay lại</button>
    </div>

  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { toast } from 'vue3-toastify'
import { API_URL } from '@/config'
import { STORAGE_URL } from '@/config'
// Firebase
import { ref as dbRef, onValue } from 'firebase/database'
import { database } from '@/stores/firebase'

const goBack = () => window.history.back()

const isLoading = ref(false)
const route = useRoute()
const order_id = route.params.id

const showDistanceBox = ref(false)

const restaurant = ref({ lat: 10.854113664188024, lng: 106.6262030926953 })
const customer = ref({})
const shipper = ref({ lat: 10.854113664188024, lng: 106.6262030926953 })

let shipperMarker = null
let animationFrameId = null
let previousLatLng = null

function animateMarker(marker, fromLatLng, toLatLng, duration = 1000) {
  if (fromLatLng.lat === toLatLng.lat && fromLatLng.lng === toLatLng.lng) return

  const startTime = performance.now()
  function easeInOutQuad(t) {
    return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t
  }

  function animate(currentTime) {
    const elapsed = currentTime - startTime
    const t = Math.min(elapsed / duration, 1)
    const easedT = easeInOutQuad(t)

    const lat = fromLatLng.lat + (toLatLng.lat - fromLatLng.lat) * easedT
    const lng = fromLatLng.lng + (toLatLng.lng - fromLatLng.lng) * easedT

    marker.setLatLng([lat, lng])

    if (t < 1) {
      animationFrameId = requestAnimationFrame(animate)
    }
  }

  if (animationFrameId) cancelAnimationFrame(animationFrameId)
  animationFrameId = requestAnimationFrame(animate)
}



let dynamicRouteLine = null
let hasArrived = false // dùng để hiển thị toast 1 lần duy nhất

const listenToShipperLocation = (shipperId, map, icon) => {
  const locationRef = dbRef(database, `locations/shipper_${shipperId}`)

  onValue(locationRef, (snapshot) => {
    const data = snapshot.val()
    if (!data) return

    const { lat, lng } = data
    const newLatLng = L.latLng(lat, lng)

    // Nếu chưa có marker → khởi tạo marker mới
    if (!shipperMarker) {
      shipperMarker = L.marker(newLatLng, { icon }).addTo(map)
      previousLatLng = newLatLng
    } else {
      // So sánh nếu toạ độ mới cách toạ độ cũ > 1m → animate
      if (
        previousLatLng &&
        newLatLng.distanceTo(previousLatLng) > 1 &&
        (newLatLng.lat !== previousLatLng.lat || newLatLng.lng !== previousLatLng.lng)
      ) {
        animateMarker(shipperMarker, previousLatLng, newLatLng, 1000) // ⏱ khớp Firebase
        previousLatLng = newLatLng
      }
    }

    // 🟢 Khi shipper đến nơi (cách < 10m)
    const distToCustomer = newLatLng.distanceTo(L.latLng(customer.value.lat, customer.value.lng))
    if (distToCustomer < 10 && !hasArrived) {
      toast.success('Shipper đã đến nơi!')
      hasArrived = true
      localStorage.setItem('hasArrived', 'true')
    }
  }, {
    onlyOnce: false
  })
}



//Heigit
async function getRoutePolyline(start, end) {
  const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/geojson', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImY2N2UzNGY1YmFmZWVhODlmZmQyZTI4M2M0YjVjNTZjNGQxYTcyZjI4Yzg3YjRiYzIwNDk0ZmZlIiwiaCI6Im11cm11cjY0In0='
    },
    body: JSON.stringify({
      coordinates: [
        [start.lng, start.lat],
        [end.lng, end.lat]
      ]
    })
  })

  const data = await response.json()
  if (!data.features || data.features.length === 0) return { coords: [], distance: 0 }

  const coords = data.features[0].geometry.coordinates.map(coord => [coord[1], coord[0]])
  const distance = data.features[0].properties.summary.distance
  return { coords, distance }
}
//LocationIQ
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

onMounted(async () => {
  if (localStorage.getItem('hasArrived') === 'true') {
    localStorage.removeItem('hasArrived')  // reset để lần sau không bị kẹt
    window.history.back()
    return
  }
  isLoading.value = true
  try {
    const res = await axios.get(`${API_URL}/delivery/${order_id}`)
    const order = res.data
    const shipperId = order.data.shipper_id

    const res2 = await axios.get(`${API_URL}/shipper/${shipperId}/last-location`)
    shipper.value = {
      lat: res2.data.lat,
      lng: res2.data.lng
    }



    const address = order.data.guest_address
    const coords = await getCoordinatesFromAddress(address)
    if (coords) {
      customer.value = coords
    } else {
      console.warn('Không tìm thấy tọa độ từ địa chỉ.')
    }

    const map = L.map('deliveryMap', { zoomControl: false }).setView([restaurant.value.lat, restaurant.value.lng], 13)

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://carto.com/">CARTO</a> contributors'
    }).addTo(map)

    L.control.zoom({ position: 'bottomright' }).addTo(map)

    L.marker([restaurant.value.lat, restaurant.value.lng])
      .addTo(map)
      .bindPopup('<b>🏠 Nhà hàng</b>')

    L.marker([customer.value.lat, customer.value.lng])
      .addTo(map)
      .bindPopup('<b>👤 Khách hàng</b>')

    const { coords: polylineCoords, distance } = await getRoutePolyline(shipper.value, customer.value)
    if (polylineCoords.length) {
      const routeLine = L.polyline(polylineCoords, {
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
    }

    const shipperIcon = L.icon({
      iconUrl: '/shipper.png',
      iconSize: [50, 50],
      iconAnchor: [25, 25],
      popupAnchor: [0, -20]
    })

    listenToShipperLocation(shipperId, map, shipperIcon)

    setTimeout(() => {
      map.invalidateSize()
    }, 300)
  } catch (error) {
    console.error('Lỗi khi khởi tạo:', error)
  } finally {
    isLoading.value = false
  }
})
</script>


<style scoped>
#deliveryMap {
  height: 450px;
  width: 100%;
  background-color: #f3f4f6;
  transition: all 0.3s ease-in-out;
  border-radius: 16px;
}

#deliveryMap,
.leaflet-container,
.leaflet-pane,
.leaflet-map-pane {
  z-index: 0 !important;
}

.distance-label {

  font-weight: bold;
  pointer-events: none;
}

#distanceBox {
  width: 80px;
  height: 50px;
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  color: #1f2937;
  /* text-gray-800 */
  font-size: 15px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 500;
  pointer-events: none;
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
</style>
