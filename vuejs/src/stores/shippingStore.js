// stores/shipping.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'

export const useShippingStore = defineStore('shipping', () => {
  const ghnToken = 'ce7a164e-3e1c-11f0-a700-860cdd37d888'
  const GHN_DISTRICT_ID = '1454'
  const shippingFee = ref(0)

  const calculateShippingFee = async ({ toDistrictId, toWardCode, serviceId, insuranceValue }) => {
    try {
      const res = await axios.post(
        'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee',
        {
          service_id: serviceId,
          from_district_id: parseInt(GHN_DISTRICT_ID),
          to_district_id: toDistrictId,
          to_ward_code: toWardCode,
          weight: 500,
          length: 20,
          width: 20,
          height: 10,
          insurance_value: insuranceValue || 0,
        },
        {
          headers: {
            Token: ghnToken,
            'Content-Type': 'application/json',
          },
        },
      )

      shippingFee.value = res.data?.data?.total || 0
      console.log('💰 Phí vận chuyển (store):', shippingFee.value)
    } catch (error) {
      console.error('Lỗi khi tính phí vận chuyển (store):', error)
      shippingFee.value = 0
    }
  }

  return {
    shippingFee,
    calculateShippingFee,
  }
})
