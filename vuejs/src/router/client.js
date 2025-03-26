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
<<<<<<< Updated upstream
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
                path: "discount",
                name: "discount",
                component: () => import("../pages/client/discount/discount.vue")
            },
            {
                path: "discount-detail",
                name: "discount-detail",
                component: () => import("../pages/client/discount/discountdetail.vue")
=======
                path: "food",
                name: "client-food",
                component: () => import("../pages/client/food/index.vue")
>>>>>>> Stashed changes
            },
        ]
    }
];

export default client;
