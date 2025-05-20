const admin = [
    {
        path: "/admin",
        name: "admin",
        component: () => import("../components/layouts/LayoutAdmin.vue"),
        redirect: "/admin/dashboard",
        children: [
            {
                path: "dashboard",
                name: "admin-dashboard",
                component: () => import("../pages/admin/Dashboard/index.vue"),
                meta: { title: 'Dashboard' }
            },
            {
                path: "categories",
                name: "admin-categories",
                component: () => import("../pages/admin/Categories/index.vue"),
                meta: { title: 'Categories' }
            },
            {
                path: "products",
                name: "admin-products",
                component: () => import("../pages/admin/Products/list.vue"),
                meta: { title: 'Products' }
            },
            {
                path: "products/combo",
                name: "admin-products-combo",
                component: () => import("../pages/admin/Combo/combo.vue"),
                meta: { title: 'Combo Products' }
            },
            {
                path: "options",
                name: "admin-options",
                component: () => import("../pages/admin/Options/index.vue"),
                meta: { title: 'Options' }
            },
            {
                path: "options/category-options",
                name: "admin-category-options",
                component: () => import("../pages/admin/Category-Options/index.vue"),
                meta: { title: 'Category Options' }
            },
            {
                path: "tables",
                name: "admin-tables",
                component: () => import("../pages/admin/Tables/list.vue"),
                meta: { title: 'Tables' }
            },
            {
                path: "tables/booking-schedule",
                name: "admin-tables-booking-schedule",
                component: () => import("../pages/admin/Tables/booking-schedule.vue"),
                meta: { title: 'Booking Schedule' }
            },
            {
                path: "tables/current-order",
                name: "admin-tables-current-order",
                component: () => import("../pages/admin/Tables/current-order.vue"),
                meta: { title: 'Current Orders' }
            },
            {
                path: "orders/orders-detail/:id",
                name: "admin-orders-detail",
                component: () => import("../pages/admin/Orders/orders-detail.vue"),
                meta: { title: 'Order Detail' }
            },
            {
                path: "demo",
                name: "demo",
                component: () => import("../pages/admin/demo/index.vue"),
                meta: { title: 'Demo' }
            },
            {
                path: "orders/history",
                name: "orders-history",
                component: () => import("../pages/admin/Orders/history.vue"),
                meta: { title: 'Order History' }
            },
            {
                path: "users/list",
                name: "users-list",
                component: () => import("../pages/admin/Users/list.vue"),
                meta: { title: 'Users List' }
            },
            {
                path: "users/list-role",
                name: "users-list-role",
                component: () => import("../pages/admin/Users/role.vue"),
                meta: { title: 'User Roles' }
            },
            {
                path: "choose-list-food/:id",
                name: "list-food",
                component: () => import("../pages/admin/Tables/listsp.vue"),
                meta: { title: 'Food List' }
            },
            {
              path: "insert-food",
              name: "insert-food",
              component: () => import("../pages/admin/Products/insert.vue")
            },
            {
              path: "insert-category",
              name: "insert-category",
              component: () => import("../pages/admin/Categories/insert.vue")
            },
            {
              path: "insert-combo",
              name: "insert-combo",
              component: () => import("../pages/admin/Combo/insert.vue")
            }
        ]
    }
]

export default admin;
