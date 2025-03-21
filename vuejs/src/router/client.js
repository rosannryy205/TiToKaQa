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
                path: "payment",
                name: "client-payment",
                component: () => import("../pages/client/cart/payment.vue")
            },
        ]
    }
];

export default client;
