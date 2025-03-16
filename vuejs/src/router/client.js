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
        ]
    }
];

export default client;
