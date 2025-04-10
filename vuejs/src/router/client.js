const client = [
    {
        path: "/",
        component: () => import("../components/layouts/LayoutUser.vue"),
        redirect: "/home",
        children: [
            {
                path: "home",
                name: "client-home",
                component: () => import("../pages/client/home/index.vue")
            },
            {
                path: "cart",
                name: "client-cart",
                component: () => import("../pages/client/cart/index.vue")
            },
            {
                path: "payment_if",
                name: "client-payment-information",
                component: () => import("../pages/client/cart/payment_in4.vue")
            },
            {
                path: "reservation",
                name: "reservation",
                component: () => import("../pages/client/reservation/reservation.vue")
            },
            {
              path: "reservation-form/:orderId",
              name: "reservation-form",
              component: () => import("../pages/client/reservation/reservation-form.vue")
            },
            {
                path: "discount",
                name: "discount",
                component: () => import("../pages/client/discount/discount.vue")
            },
            {
                path: "discount-detail",
                name: "discount-detail",
                component: () => import("../pages/client/discount/discountdetail.vue")
            },
            {
                path: "food",
                name: "client-food",
                component: () => import("../pages/client/food/index.vue")
            },
            {
              path: "update-user",
              name: "update-user",
              component: () => import("../pages/client/user/update-user.vue")
            },
            {
              path: "history-order",
              name: "history-order",
              component: () => import("../pages/client/user/history-order.vue")
            },
            {
              path: "history-order-detail/:id",
              name: "history-order-detail",
              component: () => import("../pages/client/user/history-order-detail.vue")
            },
        ]
    }
];

export default client;
