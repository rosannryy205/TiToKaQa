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
        ]
    }
];

export default client;
