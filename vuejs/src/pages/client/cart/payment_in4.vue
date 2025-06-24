<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>

  <div class="container-sm py-4">
    <div class="row gx-5">
      <!-- Customer Info -->
      <div class="col-md-7">
        <div class="p-4 border rounded shadow-sm bg-white">
          <h4 class="mb-4">Thông tin đặt hàng</h4>
          <form @submit.prevent="submitOrder">
            <div class="mb-3">
              <input v-model="form.fullname" type="text" class="form-control-customer" placeholder="Tên của bạn" />
            </div>
            <div class="mb-3">
              <input v-model="form.email" type="email" class="form-control-customer" placeholder="Email của bạn" />
            </div>
            <div class="mb-3">
              <input v-model="form.phone" type="text" class="form-control-customer" placeholder="Số điện thoại" />
            </div>
            <div class="mb-3">
              <select v-model="selectedProvince" @change="fetchDistricts" class="form-control-customer">
                <option value="">Chọn tỉnh / thành</option>
                <option v-for="province in provinces" :key="province.ProvinceID" :value="province.ProvinceID">
                  {{ province.ProvinceName }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <select v-model="selectedDistrict" @change="fetchWards" :disabled="!selectedProvince"
                class="form-control-customer">
                <option value="">Chọn quận / huyện</option>
                <option v-for="district in districts" :key="district.DistrictID" :value="district.DistrictID">
                  {{ district.DistrictName }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <select v-model="selectedWard" :disabled="!selectedDistrict" class="form-control-customer">
                <option value="">Chọn phường / xã</option>
                <option v-for="ward in wards" :key="ward.WardCode" :value="ward.WardCode">
                  {{ ward.WardName }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <input v-model="form.address" type="text" class="form-control-customer" placeholder="Địa chỉ" />
            </div>
            <div class="mb-3">
              <textarea v-model="note" class="form-control-customer" rows="3" placeholder="Ghi chú"></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <router-link to="/cart" class="btn btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Quay về giỏ hàng
              </router-link>
              <button type="submit" class="btn btn-check-out">Đặt hàng</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-md-5">
        <div class="p-4 border rounded shadow-sm bg-white">
          <h4 class="mb-3">Đơn hàng ({{ totalQuantity }} sản phẩm)</h4>
          <hr />

          <!-- Cart Items -->
          <div class="list-product-scroll mb-3">
            <div v-for="(item, index) in cartItems" :key="index" class="d-flex mb-3">
              <img :src="getImageUrl(item.image)" alt="" class="me-3 rounded" width="80" height="80" />
              <div class="flex-grow-1">
                <strong>{{ item.name }}</strong>
                <div>Loại: {{ item.type }}</div>
                <div>{{ item.spicyLevel }}</div>
                <div v-if="item.toppings.length" class="text-muted small">
                  <div v-for="(topping, i) in item.toppings" :key="i">
                    {{ topping.name }} - {{ formatNumber(topping.price) }} VNĐ
                  </div>
                </div>
                <div v-else class="text-muted small">Không có topping</div>
                <div>Số lượng: {{ item.quantity }}</div>
                <div>Giá: {{ formatNumber(item.price) }} VNĐ</div>
              </div>
              <div class="text-end ms-2">
                <strong>{{ formatNumber(totalPriceItem(item)) }} VNĐ</strong>
              </div>
            </div>
          </div>

          <hr />

          <div class="d-flex justify-content-between mb-2">
            <span>Tạm tính</span>{{ formatNumber(totalPrice) }} VNĐ
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Phí ship</span>{{ formatNumber(shippingFee) }} VNĐ
          </div>
          <div v-if="discountFoodAmount > 0" class="d-flex justify-content-between mb-2 text-success">
            <span>Giảm giá sản phẩm</span> -{{ formatNumber(discountFoodAmount) }} VNĐ
          </div>
          <div v-if="discountShipAmount > 0" class="d-flex justify-content-between mb-2 text-success">
            <span>Giảm giá phí ship</span> -{{ formatNumber(discountShipAmount) }} VNĐ
          </div>
          <div style="color: #c92c3c" class="d-flex justify-content-between mb-2 fw-bold">
            <span>Tổng thanh toán:</span>{{ formatNumber(finalTotal) }} VNĐ
          </div>
          <!--thong bao chua login-->
          <div v-if="!isLoggedIn" class="alert alert-warning">
            🔒 Vui lòng <a href="/login" class="text-primary fw-bold">đăng nhập</a> để sử dụng và
            xem các mã giảm giá!
          </div>

          <!--nhap-->
          <div class="mb-3" v-if="isLoggedIn">
            <div v-if="selectedDiscount" class="text-green-600 mb-2">
              Mã <strong style="color: #c92c3c">{{ selectedDiscount }}</strong> đã được áp dụng ✅.
            </div>
            <label for="discount" class="form-label">Mã giảm giá</label>
            <div class="input-group">
              <input v-model="discountInput" type="text" id="discount" class="form-control"
                placeholder="Nhập mã giảm giá..." />
              <button class="btn btn-outline-primary" @click="handleDiscountInput">Áp dụng</button>
            </div>
          </div>

          <!--chon-->
          <div class="discount-scroll-wrapper" v-if="isLoggedIn">
            <div v-for="discount in discountsFiltered" :key="discount.id">
              <div class="shopee-voucher d-flex align-items-center justify-content-between mb-2" :class="{
                'disabled-voucher':
                  totalPrice < discount.min_order_value ||
                  discount.used >= discount.usage_limit ||
                  (discount.discount_type === 'freeship' && !hasShippingFee),
                'freeship-voucher': discount.discount_type === 'freeship',
              }" @click="
                totalPrice >= discount.min_order_value &&
                discount.used < discount.usage_limit &&
                !(discount.discount_type === 'freeship' && !hasShippingFee) &&
                (selectedDiscount === discount.code
                  ? removeDiscountCode()
                  : applyDiscountCode(discount.code))
                ">
                <!-- Bên trái -->
                <div class="voucher-left d-flex align-items-center">
                  <div class="voucher-logo d-flex align-items-center justify-content-center">
                    <!-- Logo theo loại mã -->
                    <img v-if="discount.discount_type === 'freeship'" src="/img/freeship-icon.png" alt="Freeship"
                      class="voucher-icon" />
                    <img v-else src="/img/discount-icon.png" alt="Discount" class="voucher-icon" />
                  </div>
                  <div class="voucher-info ps-3">
                    <div class="voucher-title" :class="{ 'freeship-text': discount.discount_type === 'freeship' }">
                      {{ discount.name }}
                    </div>
                    <div class="voucher-title" :class="{ 'freeship-text': discount.discount_type === 'freeship' }">
                      Mã {{ discount.code }}
                    </div>
                    <div class="voucher-time">
                      <i class="fa-regular fa-clock me-1"></i>Ngày hết hạn: {{ discount.end_date }}
                    </div>
                    <div v-if="discount.discount_type === 'freeship' && !hasShippingFee"
                      class="remind text-danger fw-md">
                      Vui lòng chọn địa chỉ để dùng mã !
                    </div>

                    <div v-if="totalPrice < discount.min_order_value" class="text-danger small">
                      Đơn tối thiểu: {{ discount.min_order_value.toLocaleString() }}đ
                    </div>
                  </div>
                </div>

                <!-- Bên phải -->
                <div class="voucher-right text-end">
                  <div class="voucher-status" :class="{
                    'text-success': selectedDiscount === discount.code,
                    'freeship-text': discount.discount_type === 'freeship',
                    'freeship-border': discount.discount_type === 'freeship',
                  }">
                    <span v-if="selectedDiscount === discount.code" class="text-danger">Bỏ dùng❌
                    </span>
                    <span v-else>Dùng ngay</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div>
            <h6 class="mb-2">Phương thức thanh toán</h6>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="vnpay" value="VNPAY"
                v-model="paymentMethod" />
              <label class="form-check-label d-flex align-items-center" for="vnpay">
                <span class="me-2">Thanh toán qua VNPAY</span>
                <img src="/img/Logo-VNPAY-QR-1 (1).png" height="20" width="60" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="momo" value="MOMO"
                v-model="paymentMethod" />
              <label class="form-check-label d-flex align-items-center" for="momo">
                <span class="me-2">Thanh toán qua Momo</span>
                <img src="/img/momo.png" height="20" width="20" alt="" />
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="payment" id="cod" value="COD"
                v-model="paymentMethod" />
              <label class="form-check-label d-flex align-items-center" for="cod">
                <span class="me-2">Thanh toán khi nhận hàng (COD)</span>
                <img src="/img/cod.png" height="30" width="30" alt="" />
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { FoodList } from '@/stores/food'
// import { Payment } from '@/stores/payment'
import { Discounts } from '@/stores/discount'
import { Cart } from '@/stores/cart'
import { onMounted, computed } from 'vue'
import numeral from 'numeral'
import { ref, watch } from 'vue'
import axios from 'axios'
import { User } from '@/stores/user'
import dayjs from 'dayjs'
import { useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'
import { useShippingStore } from '@/stores/shippingStore'
import { toast } from 'vue3-toastify'
const shippingStore = useShippingStore()
const { shippingFee } = storeToRefs(shippingStore)

export default {
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `/img/food/${image}`
    },
  },
  setup() {
    const router = useRouter()

    const note = ref('')
    const { user, form } = User.setup()

    const {
      discounts,
      discountInput,
      selectedDiscount,
      discountId,
      totalPrice,
      getAllDiscount,
      discountInputId,
      showMoreDiscounts,
      applyDiscountCode,
      handleDiscountInput,
      finalTotal,
      discountFoodAmount,
      totalQuantity,
      totalPriceItem,
      loadCart,
      discountShipAmount,
    } = Discounts()
    onMounted(async () => {
      await getAllDiscount({ source: 'system' })
      // console.log('Danh sách voucher hệ thống:', discounts.value)
    })

    const {
      cartItems,
      cartKey,

    } = Cart()

    const { isLoading } = FoodList.setup()
    const isLoggedIn = computed(() => !!localStorage.getItem('token'))

    const paymentMethod = ref('')

    const check_out = async (orderId) => {
      try {
        if (!paymentMethod.value) {
          toast.error('Vui lòng chọn phương thức thanh toán.')
          return
        }

        if (paymentMethod.value === 'VNPAY') {
          if (!orderId || finalTotal.value <= 0) {
            toast.error('Thông tin đơn hàng hoặc số tiền không hợp lệ để thanh toán VNPAY.');
            return;
          }
          const paymentRes = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
            order_id: orderId,
            amount: finalTotal.value,
          })
          if (paymentRes.data && paymentRes.data.payment_url) {
            localStorage.setItem('payment_method', paymentMethod.value)
            localStorage.removeItem(cartKey.value)
            window.location.href = paymentRes.data.payment_url
          } else {
            toast.error('Không tạo được link thanh toán VNPAY.')
          }
          return
        }
        if (paymentMethod.value === 'MOMO') {
          toast.info('Chức năng thanh toán MoMo đang được phát triển!');
          localStorage.setItem('payment_method', paymentMethod.value);
          localStorage.removeItem(cartKey.value);
          return
        }
        if (paymentMethod.value === 'COD') {
          if (user.value?.status === 'Block') {
            toast.error('Tài khoản của bạn đã bị hạn chế. Không thể thanh toán bằng tiền mặt.');
            return;
          }
          await new Promise((resolve) => setTimeout(resolve, 300))
          await axios.post('http://127.0.0.1:8000/api/payments/cod-payment', {
            order_id: orderId,
            amount_paid: finalTotal.value,
            payment_type: 'Thanh toán toàn bộ',
          })
          localStorage.setItem('payment_method', paymentMethod.value)
          localStorage.removeItem(cartKey.value)
          toast.success('Đặt hàng và thanh toán bằng tiền mặt thành công!')
          router.push('/payment-result');
        }

      } catch (error) {
        console.error('Lỗi khi xử lý thanh toán:', error)
        if (error.response && error.response.data && error.response.data.message) {
          toast.error('Thanh toán thất bại: ' + error.response.data.message)
        } else {
          toast.error('Thanh toán thất bại, vui lòng thử lại! Lỗi mạng hoặc server không phản hồi.');
        }
      }
    }

    const submitOrder = async () => {
      isLoading.value = true;
      try {
        if (!selectedProvince.value || !selectedDistrict.value || !selectedWard.value) {
          alert('Vui lòng chọn đầy đủ Tỉnh/Thành, Quận/Huyện và Phường/Xã.');
          isLoading.value = false;
          return;
        }

        const province = provinces.value.find(p => p.ProvinceID === selectedProvince.value);
        const district = districts.value.find(d => d.DistrictID === selectedDistrict.value);
        const ward = wards.value.find(w => w.WardCode === selectedWard.value);

        const fullAddress = `${form.value.address}, ${ward?.WardName || ''}, ${district?.DistrictName || ''}, ${province?.ProvinceName || ''}`;

        if (!fullAddress || cartItems.value.length === 0) {
          toast.error('Vui lòng nhập đầy đủ thông tin địa chỉ và thêm món ăn vào giỏ hàng.');
          isLoading.value = false;
          return;
        }

        const orderData = {
          user_id: user.value ? user.value.id : null,
          guest_name: form.value.fullname,
          guest_email: form.value.email,
          guest_phone: form.value.phone,
          guest_address: fullAddress,
          note: note.value || '',
          total_price: finalTotal.value || 0,
          money_reduce: discountFoodAmount.value > 0 ? discountFoodAmount.value : discountShipAmount.value,
          discount_id: discountId.value || null,
          order_detail: cartItems.value.map((item) => ({
            food_id: item.type == 'Food' ? item.id : null,
            combo_id: item.type == 'Combo' ? item.id : null,
            quantity: item.quantity,
            price: item.price,
            type: item.type,
            toppings: item.toppings.map((t) => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price,
            })),
          })),
        }

        const response = await axios.post('http://127.0.0.1:8000/api/order', orderData);

        if (response?.data?.status && response.data.order_id) {
          const order_id = response.data.order_id;
          toast.success('Đơn hàng đã được tạo thành công!');

          if (orderData.discount_id) {
            await axios.post('http://127.0.0.1:8000/api/discounts/use', {
              discount_id: orderData.discount_id,
              order_id: order_id,
            });
          }

          await check_out(order_id);
        } else {
          toast.error('Không nhận được ID đơn hàng từ server hoặc tạo đơn hàng thất bại.');
        }

      } catch (error) {
        console.error('Lỗi khi gửi đơn hàng hoặc xử lý thanh toán:', error);
        if (error.response?.data?.message) {
          toast.error('Đặt hàng thất bại: ' + error.response.data.message);
        } else {
          toast.error('Đặt hàng thất bại, vui lòng thử lại! Lỗi mạng hoặc server không phản hồi.');
        }
      } finally {
        isLoading.value = false;
      }
    };


    const today = dayjs().format('YYYY-MM-DD')
    const removeDiscountCode = () => {
      selectedDiscount.value = null
      // reset logic giảm giá (nếu có)
    }

    const discountsFiltered = computed(() => {
      return discounts.value.filter((discount) => {
        const endDate = dayjs(discount.end_date).format('YYYY-MM-DD')
        return discount.used < discount.usage_limit && endDate >= today
      })
    })
    //giao hang nhanh
    const provinces = ref([])
    const districts = ref([])
    const wards = ref([])

    const selectedProvince = ref('')
    const selectedDistrict = ref('')
    const selectedWard = ref('')

    const ghnToken = 'ce7a164e-3e1c-11f0-a700-860cdd37d888'

    const fetchProvinces = async () => {
      try {
        const res = await axios.get(
          'https://online-gateway.ghn.vn/shiip/public-api/master-data/province',
          {
            headers: {
              Token: ghnToken,
              'Content-Type': 'application/json',
            },
          },
        )
        provinces.value = res.data.data
        const hcm = provinces.value.find((p) =>
          p.ProvinceName.toLowerCase().includes('hồ chí minh'),
        )
        if (hcm) {
          selectedProvince.value = hcm.ProvinceID
          fetchDistricts()
        }
      } catch (error) {
        console.error('Lỗi khi fetch provinces:', error)
      }
    }

    const fetchDistricts = async () => {
      shippingServices.value = []
      selectedService.value = null
      selectedDistrict.value = ''
      selectedWard.value = ''
      districts.value = []
      wards.value = []

      try {
        const res = await axios.post(
          'https://online-gateway.ghn.vn/shiip/public-api/master-data/district',
          { province_id: selectedProvince.value },
          {
            headers: {
              Token: ghnToken,
              'Content-Type': 'application/json',
            },
          },
        )
        districts.value = res.data.data
      } catch (error) {
        console.error('Lỗi khi fetch districts:', error)
      }
      watch(selectedDistrict, (newVal) => {
        if (newVal) {
          fetchShippingServices()
        }
      })
    }

    const fetchWards = async () => {
      selectedWard.value = ''
      wards.value = []

      try {
        const res = await axios.post(
          'https://online-gateway.ghn.vn/shiip/public-api/master-data/ward',
          { district_id: selectedDistrict.value },
          {
            headers: {
              Token: ghnToken,
              'Content-Type': 'application/json',
            },
          },
        )
        wards.value = res.data.data
      } catch (error) {
        console.error('Lỗi khi fetch wards:', error)
      }
    }
    const shippingServices = ref([])
    const selectedService = ref(null)

    const fetchShippingServices = async () => {
      if (!selectedDistrict.value) return
      try {
        const res = await axios.post('http://localhost:8000/api/ghn/service', {
          to_district_id: selectedDistrict.value,
        })
        shippingServices.value = res.data || []
        selectedService.value = shippingServices.value.length ? shippingServices.value[0] : null
      } catch (err) {
        console.error('Lỗi khi lấy dịch vụ GHN:', err)
      }
    }
    const hasShippingFee = computed(() => shippingFee.value > 0)
    onMounted(() => {
      fetchProvinces()
      loadCart()

    })
    watch([selectedDistrict, selectedWard, selectedService], () => {
      if (selectedDistrict.value && selectedWard.value && selectedService.value) {
        shippingStore.calculateShippingFee({
          toDistrictId: selectedDistrict.value,
          toWardCode: selectedWard.value,
          serviceId: selectedService.value.service_id,
          insuranceValue: finalTotal.value || 0,
        })
      }
    })

    return {
      user,
      totalPriceItem,
      totalQuantity,
      check_out,
      form,
      submitOrder,
      isLoading,
      paymentMethod,
      note,
      cartItems,
      totalPrice,
      finalTotal,

      discounts,
      discountInput,
      selectedDiscount,
      removeDiscountCode,
      showMoreDiscounts,
      handleDiscountInput,
      applyDiscountCode,
      discountFoodAmount,
      discountInputId,
      isLoggedIn,
      discountsFiltered,
      today,

      provinces,
      districts,
      wards,
      selectedProvince,
      selectedDistrict,
      selectedWard,
      ghnToken,
      fetchDistricts,
      fetchWards,
      shippingServices,
      selectedService,
      shippingFee,
      discountShipAmount,
      hasShippingFee,
    }
  },
}
</script>

<style>
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

.text-success {
  color: #28a745;
  font-weight: bold;
  border: solid #28a745 !important;
}

.disabled-voucher {
  pointer-events: none;
  background-color: #f0f0f0;
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
