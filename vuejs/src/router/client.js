

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
        meta: { title: 'Trang chủ' },
      },
      {
        path: '/search',
        name: 'client-search',
        component: () => import('../pages/client/search/index.vue'),
        meta: { title: 'Tìm kiếm món ăn' },
      },
      {
        path: 'cart',
        name: 'client-cart',
        component: () => import('../pages/client/cart/index.vue'),
        meta: { title: 'Giỏ hàng' },
      },
      {
        path: 'payment_if',
        name: 'client-payment-information',
        component: () => import('../pages/client/cart/payment_in4.vue'),
        meta: { title: 'Thông tin thanh toán' },
      },
      {
        path: 'reservation',
        name: 'reservation',
        component: () => import('../pages/client/reservation/reservation.vue'),
        meta: { title: 'Đặt bàn' },
      },
      {
        path: 'luckywheel',
        name: 'luckywheel',
        component: () => import('../pages/client/luckywheel/luckywheel.vue'),
        meta: { title: 'Vòng quay may mắn' },
      },
      {
        path: 'flashsale',
        name: 'flashsale',
        component: () => import('../pages/client/flashsale/index.vue'),
        meta: { title: 'Vòng quay may mắn' },
      },
      {
        path: 'reservation-form/:orderId',
        name: 'reservation-form',
        component: () => import('../pages/client/reservation/reservation-form.vue'),
        meta: { title: 'Chi tiết đặt bàn' },
      },
      {
        path: 'discount',
        name: 'discount',
        component: () => import('../pages/client/discount/discount.vue'),
        meta: { title: 'Khuyến mãi' },
      },
      {
        path: 'discount-detail',
        name: 'discount-detail',
        component: () => import('../pages/client/discount/discountdetail.vue'),
        meta: { title: 'Chi tiết khuyến mãi' },
      },
      {
        path: 'food',
        name: 'client-food',
        component: () => import('../pages/client/food/index.vue'),
      },
      {
        path: 'food/:orderId',
        name: 'client-food-reservation',
        component: () => import('../pages/client/food/index.vue'),
      },
      {
        path: 'food/:orderId',
        name: 'client-food-reservation',
        component: () => import('../pages/client/food/index.vue'),
      },
      {
        path: 'payment-result',
        name: 'payment-result',
        component: () => import('@/pages/client/cart/payment-result.vue'),
        meta: { title: 'Kết quả thanh toán' },
      },
      {
        path: '/login/google/callback',
        component: () => import('../pages/client/user/GoogleCallback.vue'),
        meta: { title: 'Google Callback' },
      },
      {
        path: '/login',
        component: () => import('../pages/client/login/login.vue'),
      },
      {
        path: '/register',
        component: () => import('../pages/client/login/register.vue'),
      },
      {
        path: '/verify',
        component: () => import('../pages/client/login/verify.vue'),
      },
      {
        path: 'account',
        component: () => import('../components/AccountLayout/SidebarAccount.vue'),
        redirect: { name: 'update-user' },
        children: [
          {
            path: 'update-user',
            name: 'update-user',
            component: () => import('../pages/client/user/update-user.vue'),
            meta: { title: 'Cập nhật thông tin' },
          },
          {
            path: 'coins-reward',
            name: 'coins-reward',
            component: () => import('../pages/client/user/coins-reward.vue'),
            meta: { title: 'Trang chủ' },
          },
          {
            path: 'discount-management',
            name: 'discount-management',
            component: () => import('../pages/client/user/discount-management.vue'),
            meta: { title: 'Kho mã giảm giá' },
          },
          {
            path: 'order-management',
            name: 'order-management',
            component: () => import('../pages/client/user/order-management.vue'),
            meta: { title: 'Quản lý đơn hàng' },
          },
          {
            path: 'reservation-management',
            name: 'reservation-management',
            component: () => import('../pages/client/user/reservation-management.vue'),
            meta: { title: 'Quản lý đơn đặt bàn' },
          },
          {
            path: 'deal-food',
            name: 'deal-food',
            component: () => import('../pages/client/user/deal-food.vue'),
            meta: { title: 'Kho Deal Đã Nhận' },
          },
        ]
      },
      {
        path: 'history-order-detail/:id',
        name: 'history-order-detail',
        component: () => import('../pages/client/user/history-order-detail.vue'),
        meta: { title: 'Chi tiết đơn hàng' },
      },
      {
        path: 'history-reservation-detail/:id',
        name: 'history-reservation-detail',
        component: () => import('../pages/client/user/history-reservation-detail.vue'),
        meta: { title: 'Chi tiết đơn đặt bàn' },
      },
      {
        path: 'delivery/:id',
        name: 'delivery',
        component: () => import('../pages/client/user/delivery.vue'),
        meta: { title: 'Theo dõi đơn hàng' },
      },
      {
        path: 'posts',
        name: 'posts',
        component: () => import('../pages/client/post/index.vue'),
        meta: { title: 'Bài viết' },
      },
      {
        path: 'posts_detail/:id',
        name: 'posts_detail',
        component: () => import('../pages/client/post/postdetail.vue'),
        meta: { title: 'Chi tiết bài viết' },
      },
    ],
  },
]

export default client
