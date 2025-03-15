
const admin = [
    {
        path: "/admin",
        component: () => import("../components/layouts/LayoutAdmin.vue"),
        redirect: "/admin/dashboard",
        children: [
            {
                path: "dashboard",
                name: "admin-dashboard",
                component: () => import("../pages/admin/Dashboard/index.vue")
            },
            {
                path: "categories",
                name: "admin-categories",
                component: () => import("../pages/admin/Categories/index.vue")
            },
            {
                path: "products",
                name: "admin-products",
                component: () => import("../pages/admin/Products/list.vue")
            },
            
        ]
    }
]

export default admin;