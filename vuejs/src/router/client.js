const client = [
  {
    path: '/',
    component: () => import('../components/layouts/LayoutUser.vue'),
    redirect: '/home',
    children: [
      {
        path: 'home',
        name: 'client-home',
        component: () => import('../pages/client/home/index.vue'),
        meta: { title: 'Trang chủ' }
      },
      {
        path: '/search',
        name: 'client-search',
        component: () => import('../components/ThePageFood.vue'),
        meta: { title: 'Tìm kiếm món ăn' }
      },
      {
        path: 'cart',
        name: 'client-cart',
        component: () => import('../pages/client/cart/index.vue'),
        meta: { title: 'Giỏ hàng' }
      },
      {
        path: 'payment_if',
        name: 'client-payment-information',
        component: () => import('../pages/client/cart/payment_in4.vue'),
        meta: { title: 'Thông tin thanh toán' }
      },
      {
        path: 'reservation',
        name: 'reservation',
        component: () => import('../pages/client/reservation/reservation.vue'),
        meta: { title: 'Đặt bàn' }
      },
      {
        path: 'reservation-form/:orderId',
        name: 'reservation-form',
        component: () => import('../pages/client/reservation/reservation-form.vue'),
        meta: { title: 'Chi tiết đặt bàn' }
      },
      {
        path: 'discount',
        name: 'discount',
        component: () => import('../pages/client/discount/discount.vue'),
        meta: { title: 'Khuyến mãi' }
      },
      {
        path: 'discount-detail',
        name: 'discount-detail',
        component: () => import('../pages/client/discount/discountdetail.vue'),
        meta: { title: 'Chi tiết khuyến mãi' }
      },
      {
        path: 'food',
        name: 'client-food',
        component: () => import('../pages/client/food/index.vue'),
        meta: { title: 'Thực đơn' }
      },
      {
        path: 'update-user',
        name: 'update-user',
        component: () => import('../pages/client/user/update-user.vue'),
        meta: { title: 'Cập nhật thông tin' }
      },
      {
        path: 'history-order',
        name: 'history-order',
        component: () => import('../pages/client/user/history-order.vue'),
        meta: { title: 'Lịch sử đơn hàng' }
      },
      {
        path: 'history-order-detail/:id',
        name: 'history-order-detail',
        component: () => import('../pages/client/user/history-order-detail.vue'),
        meta: { title: 'Chi tiết đơn hàng' }
      },
      {
        path: 'payment-result',
        name: 'payment-result',
        component: () => import('@/pages/client/cart/payment-result.vue'),
        meta: { title: 'Kết quả thanh toán' }
      },
      {
        path: '/google/callback',
        component: () => import('../pages/client/user/GoogleCallback.vue'),
        meta: { title: 'Google Callback' }
      },
      {
        path: '/test',
        component: () => import('../pages/client/user/test.vue'),
        meta: { title: 'Trang test' }
      }
    ],
  },
]

export default client;
