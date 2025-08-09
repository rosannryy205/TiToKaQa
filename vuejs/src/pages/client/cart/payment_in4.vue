<template>
  <div v-if="isLoading" class="isLoading-overlay">
    <div class="spinner-border text-danger" role="status">
      <span class="visually-hidden">isLoading...</span>
    </div>
  </div>

  <div class="container-sm py-4">
    <div class="row gx-5">
      <!---->
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
          <div class="d-flex justify-content-between mb-2">
            <span>{{ formatNumber(form.usable_points) }} Tpoints</span><label class="toggle-switch">
              <input type="checkbox" v-model="form.use_points" :disabled="form.usable_points <= 0" />
              <div class="toggle-switch-background">
                <div class="toggle-switch-handle"></div>
              </div>
            </label>
          </div>
          <div v-if="form.use_points" class="d-flex justify-content-between mb-2 text-success mb-2">
            Giảm {{ formatNumber(pointsDiscountAmount) }} VNĐ từ Tpoints
            <br />
            <span class="text-muted">
              Còn lại {{ formatNumber(form.usable_points - (pointsDiscountAmount)) }} Tpoints
            </span>
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
            <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4 mt-3">
              <div v-for="tab in tabs" :key="tab" :class="['tab-item', { active: activeTab === tab }]"
                @click="setActive(tab)">
                {{ tab }}
              </div>
            </div>
            <div class="discount-scroll-wrapper" v-if="isLoggedIn">
              <div v-for="discount in displayedDiscounts" :key="discount.id">
                <div class="voucher-card mb-3" :class="{
                  'disabled-voucher':
                    totalPrice < discount.min_order_value ||
                    discount.used >= discount.usage_limit ||
                    (discount.discount_type === 'freeship' && !hasShippingFee) ||
                    (discount.category_id &&
                      !cartItems.some((item) =>
                        getAllChildCategoryIds(discount.category_id).includes(Number(item.category_id))
                      )) ||
                    finalTotal <= 0
                }" @click="
                  totalPrice >= discount.min_order_value &&
                  discount.used < discount.usage_limit &&
                  !(discount.discount_type === 'freeship' && !hasShippingFee) &&
                  finalTotal > 0 &&
                  (selectedDiscount === discount.code
                    ? removeDiscountCode()
                    : applyDiscountCode(discount.code))
                  ">
                  <!-- Cột trái -->
                  <div class="voucher-card-left"
                    :class="discount.discount_type === 'freeship' ? 'freeship' : 'salefood'">
                    <img :src="discount.discount_type === 'freeship'
                      ? '/img/freeship-icon.png'
                      : '/img/discount-icon.png'
                      " alt="icon" />
                    <div class="voucher-card-label">
                      {{ discount.discount_type === 'freeship' ? 'FREESHIP' : 'GIẢM GIÁ' }}
                    </div>
                  </div>

                  <!-- Cột phải -->
                  <div class="voucher-card-right">
                    <div>
                      <div class="voucher-code">Mã: {{ discount.code }}</div>
                      <div class="voucher-condition">
                        <i class="fa-regular fa-clock me-1"></i>Hết hạn: {{ discount.end_date }}
                      </div>
                      <div class="voucher-condition">
                        {{ discount.name }}
                      </div>

                      <div v-if="finalTotal <= 0" class="text-danger small">
                        Đơn hàng đã giảm hết, không thể áp thêm mã!
                      </div>
                    </div>

                    <div class="voucher-footer">
                      <div class="voucher-coins">
                        Đã dùng: {{ discount.used }}/{{ discount.usage_limit }}
                      </div>
                      <button class="voucher-button" :class="{ 'has-voucher': selectedDiscount === discount.code }"
                        :disabled="totalPrice < discount.min_order_value ||
                          discount.used >= discount.usage_limit ||
                          (discount.discount_type === 'freeship' && !hasShippingFee) ||
                          finalTotal <= 0
                          ">
                        {{ selectedDiscount === discount.code ? 'Bỏ dùng ❌' : 'Dùng ngay' }}
                      </button>
                    </div>
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
              <input class="form-check-input" type="radio" name="payment" id="momo" value="MOMO" v-model="paymentMethod"
                disabled />
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
import { onMounted, computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import numeral from 'numeral'
import axios from 'axios'
import dayjs from 'dayjs'
import { toast } from 'vue3-toastify'

import { FoodList } from '@/stores/food'
import { Discounts } from '@/stores/discount'
import { Cart } from '@/stores/cart'
import { User } from '@/stores/user'
import { useShippingStore } from '@/stores/shippingStore'
import Swal from 'sweetalert2';
import { storeToRefs } from 'pinia'

const shippingStore = useShippingStore()
const { shippingFee } = storeToRefs(shippingStore)

export default {
  //==============
  // METHODS FORMAT
  //==============
  methods: {
    formatNumber(value) {
      return numeral(value).format('0,0')
    },
    getImageUrl(image) {
      return `http://127.0.0.1:8000/storage/img/food/${image}`
    },
  },
  setup() {
    const router = useRouter()

    //==============
    // STATE & REF
    //==============
    const note = ref('')
    const paymentMethod = ref('')
    const activeTab = ref('Tất cả mã')
    const showAllVoucher = ref(false)
    const today = dayjs().format('YYYY-MM-DD')

    //==============
    // STORE
    //==============
    const { user, form } = User.setup()
    const { cartItems, cartKey } = Cart()
    const { isLoading } = FoodList.setup()

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
      getAllChildCategoryIds,
      fetchUserDiscounts,
      userDiscounts,
      pointsDiscountAmount
    } = Discounts()

    //==============
    // TAB DISCOUNT
    //==============
    const tabs = ['Tất cả mã', 'Mã của tôi']
    const setActive = (tab) => {
      activeTab.value = tab
    }
    const displayedDiscounts = computed(() => {
      if (activeTab.value === 'Tất cả mã') return discountsFiltered.value
      if (activeTab.value === 'Mã của tôi') return userDiscounts.value
      return []
    })

    const removeDiscountCode = () => {
      selectedDiscount.value = null
    }

    const discountsFiltered = computed(() => {
      return discounts.value.filter((discount) => {
        const endDate = dayjs(discount.end_date).format('YYYY-MM-DD')
        return discount.used < discount.usage_limit && endDate >= today
      })
    })

    const isLoggedIn = computed(() => !!localStorage.getItem('token'))

    //==============
    // ORDER & PAYMENT
    //==============

    const check_out = async (orderId) => {
      try {

        if (paymentMethod.value === 'VNPAY') {
          if (!orderId || finalTotal.value <= 0) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Thông tin đơn hàng hoặc số tiền không hợp lệ để thanh toán VNPAY.',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
            return
          }

          const res = await axios.post('http://127.0.0.1:8000/api/payments/vnpay-init', {
            order_id: orderId,
            amount: finalTotal.value,
          })

          if (res.data?.payment_url) {
            localStorage.setItem('order_id', orderId)
            localStorage.setItem('payment_method', paymentMethod.value)
            localStorage.removeItem(cartKey.value)
            window.location.href = res.data.payment_url
          } else {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Không tạo được link thanh toán VNPAY.',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
          }
          return
        }

        if (paymentMethod.value === 'MOMO') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Chức năng thanh toán MoMo đang được phát triển!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          // localStorage.setItem('order_id', orderId)
          localStorage.setItem('payment_method', paymentMethod.value)
          localStorage.removeItem(cartKey.value)
          return
        }

        if (paymentMethod.value === 'COD') {
          if (user.value?.status === 'Block') {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'info',
              title: 'Tài khoản của bạn đã bị hạn chế. Không thể thanh toán bằng tiền mặt.',
              showConfirmButton: false,
              timer: 2000,
              timerProgressBar: true
            });
            return
          }
          await axios.post('http://127.0.0.1:8000/api/payments/cod-payment', {
            order_id: orderId,
            amount_paid: finalTotal.value,
          })
          localStorage.setItem('order_id', orderId)
          localStorage.setItem('payment_method', paymentMethod.value)
          localStorage.removeItem(cartKey.value)
          toast.success('Đặt hàng và thanh toán bằng tiền mặt thành công!')
          router.push({
            name: 'payment-result',
            query: {
              type: 'order_id',
              value: orderId
            }
          })
        }
      } catch (error) {
        console.error(error)
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: 'Thanh toán thất bại: ' + error.response.data.message,
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      }
    }

    const submitOrder = async () => {
      isLoading.value = true
      try {
        if (!selectedProvince.value || !selectedDistrict.value || !selectedWard.value) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Vui lòng chọn đầy đủ Tỉnh/Thành, Quận/Huyện và Phường/Xã.',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          isLoading.value = false
          return
        }
        if (!paymentMethod.value) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Vui lòng chọn phương thức thanh toán trước khi đặt hàng!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          isLoading.value = false
          return
        }
        const province = provinces.value.find((p) => p.ProvinceID === selectedProvince.value)
        const district = districts.value.find((d) => d.DistrictID === selectedDistrict.value)
        const ward = wards.value.find((w) => w.WardCode === selectedWard.value)

        const fullAddress = `${form.address}, ${ward?.WardName}, ${district?.DistrictName}, ${province?.ProvinceName}`

        if (!fullAddress || cartItems.value.length === 0) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Vui lòng nhập địa chỉ và thêm món ăn vào giỏ hàng.',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          isLoading.value = false
          return
        }

        const orderData = {
          user_id: user.value?.id || null,
          guest_name: form.fullname,
          guest_email: form.email,
          guest_phone: form.phone,
          guest_address: fullAddress,
          note: note.value || '',
          total_price: finalTotal.value || 0,
          tpoint_used: pointsDiscountAmount.value,
          ship_cost: parseInt(shippingFee.value),
          money_reduce: discountFoodAmount.value > 0 ? discountFoodAmount.value : discountShipAmount.value,
          discount_id: discountId.value || null,
          order_detail: cartItems.value.map((item) => ({
            food_id: item.type === 'food' ? item.id : null,
            combo_id: item.type === 'combo' ? item.id : null,
            quantity: item.quantity,
            price: item.price,
            type: item.type,
            is_deal: item.is_deal,
            is_flash_sale: item.is_flash_sale,
            reward_id: item.reward_id,
            toppings: item.toppings.map((t) => ({
              food_toppings_id: t.food_toppings_id,
              price: t.price,
            })),
          })),
        }

        const res = await axios.post('http://127.0.0.1:8000/api/order', orderData)

        if (res.data?.status && res.data.order_id) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Đơn hàng đã được tạo thành công!',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
          if (orderData.discount_id) {
            await axios.post('http://127.0.0.1:8000/api/discounts/use', {
              discount_id: orderData.discount_id,
              order_id: res.data.order_id,
            })
          }
          await check_out(res.data.order_id)
        } else {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Không nhận được ID đơn hàng từ server.',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        }
      } catch (err) {
        console.error(err)
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'error',
          title: err?.response?.data?.message || 'Đặt hàng thất bại.',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });
      } finally {
        isLoading.value = false
      }
    }

    //==============
    // GHN - ĐỊA CHỈ & VẬN CHUYỂN
    //==============
    const provinces = ref([])
    const districts = ref([])
    const wards = ref([])

    const selectedProvince = ref('')
    const selectedDistrict = ref('')
    const selectedWard = ref('')
    const shippingServices = ref([])
    const selectedService = ref(null)
    const ghnToken = 'ce7a164e-3e1c-11f0-a700-860cdd37d888'
    const hasShippingFee = computed(() => shippingFee.value > 0)

    const fetchProvinces = async () => {
      try {
        const res = await axios.get(
          'https://online-gateway.ghn.vn/shiip/public-api/master-data/province',
          {
            headers: { Token: ghnToken },
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
      } catch (err) {
        console.error('fetchProvinces error:', err)
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
          { headers: { Token: ghnToken } },
        )
        districts.value = res.data.data
      } catch (err) {
        console.error('fetchDistricts error:', err)
      }

      watch(selectedDistrict, (newVal) => {
        if (newVal) fetchShippingServices()
      })
    }

    const fetchWards = async () => {
      selectedWard.value = ''
      wards.value = []

      try {
        const res = await axios.post(
          'https://online-gateway.ghn.vn/shiip/public-api/master-data/ward',
          { district_id: selectedDistrict.value },
          { headers: { Token: ghnToken } },
        )
        wards.value = res.data.data
      } catch (err) {
        console.error('fetchWards error:', err)
      }
    }

    const fetchShippingServices = async () => {
      if (!selectedDistrict.value) return
      try {
        const res = await axios.post('http://localhost:8000/api/ghn/service', {
          to_district_id: selectedDistrict.value,
        })
        shippingServices.value = res.data || []
        selectedService.value = shippingServices.value[0] || null
      } catch (err) {
        console.error('fetchShippingServices error:', err)
      }
    }

    //==============
    // LIFECYCLE
    //==============
    onMounted(async () => {
      await getAllDiscount({ source: 'system' })
      await fetchUserDiscounts()
      loadCart()
      fetchProvinces()
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

    //==============
    // RETURN
    //==============
    return {
      user,
      form,
      note,
      tabs,
      setActive,
      activeTab,
      displayedDiscounts,
      showAllVoucher,
      isLoggedIn,
      paymentMethod,
      check_out,
      submitOrder,
      isLoading,
      cartItems,
      cartKey,
      totalPrice,
      totalQuantity,
      totalPriceItem,
      finalTotal,

      discounts,
      discountInput,
      selectedDiscount,
      discountId,
      discountInputId,
      showMoreDiscounts,
      applyDiscountCode,
      handleDiscountInput,
      discountFoodAmount,
      discountShipAmount,
      removeDiscountCode,
      discountsFiltered,
      today,
      getAllChildCategoryIds,
      userDiscounts,

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
      hasShippingFee,

      pointsDiscountAmount
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

.modal-backdrop {
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

.order-tabs {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
  display: none;
}

.tab-item {
  padding: 8px 16px;
  cursor: pointer;
  white-space: nowrap;
  border-bottom: 2px solid transparent;
}

.tab-item.active {
  border-color: #c92c3c;
  color: #c92c3c;
  font-weight: 600;
}

.voucher-card {
  display: flex;
  min-height: 100px;
  /* Tăng nhẹ từ 90px */
  border-radius: 8px;
  overflow: hidden;
  background-color: #fff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  cursor: pointer;
  transition: box-shadow 0.2s;
}

.voucher-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.voucher-card.disabled-voucher {
  opacity: 0.6;
  cursor: not-allowed;
}

.voucher-card-left {
  width: 26%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  padding: 10px;
}

.voucher-card-left.freeship {
  background-color: #00bfa5;
}

.voucher-card-left.salefood {
  background-color: #f44336;
}

.voucher-card-left img {
  width: 32px;
  height: 32px;
  object-fit: contain;
}

.voucher-card-label {
  font-size: 12px;
  font-weight: bold;
  text-align: center;
  margin-top: 5px;
}

.voucher-card-right {
  width: 74%;
  padding: 10px 14px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.voucher-code {
  font-weight: 600;
  font-size: 14px;
  margin-bottom: 5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.voucher-condition {
  font-size: 12px;
  color: #6c757d;
  margin-bottom: 4px;
  line-height: 1.3;
}

.voucher-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 6px;
}

.voucher-coins {
  font-size: 13px;
  color: #777;
  display: flex;
  align-items: center;
}

.voucher-coins img {
  width: 16px;
  height: 16px;
  margin-left: 5px;
}

.voucher-button {
  font-size: 13px;
  padding: 5px 12px;
  border-radius: 5px;
  border: 1px solid #c92c3c;
  color: #c92c3c;
  background-color: transparent;
  transition: all 0.2s ease;
}

.voucher-button:hover {
  background-color: #c92c3c;
  color: white;
}

.voucher-button.has-voucher {
  border-color: #007d00;
  color: #007d00;
  cursor: default;
}

.voucher-button.has-voucher:hover {
  background-color: #007d00;
  color: white;
}

/**togle btn tpoint */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 36px;
  height: 18px;
  cursor: pointer;
}

.toggle-switch input[type='checkbox'] {
  display: none;
}

.toggle-switch-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #ddd;
  border-radius: 9px;
  box-shadow: inset 0 0 0 1px #ccc;
  transition: background-color 0.3s ease-in-out;
}

.toggle-switch-handle {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 14px;
  height: 14px;
  background-color: #fff;
  border-radius: 50%;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease-in-out;
}

.toggle-switch::before {
  content: '';
  position: absolute;
  top: -18px;
  right: -20px;
  font-size: 10px;
  font-weight: bold;
  color: #aaa;
  text-shadow: 1px 1px #fff;
  transition: color 0.3s ease-in-out;
}

.toggle-switch input[type='checkbox']:checked+.toggle-switch-background {
  background-color: #05c46b;
  box-shadow: inset 0 0 0 1px #04b360;
}

.toggle-switch input[type='checkbox']:checked+.toggle-switch-background .toggle-switch-handle {
  transform: translateX(18px);
}
</style>
