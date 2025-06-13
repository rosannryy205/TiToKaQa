const admin = [
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../components/layouts/LayoutAdmin.vue'),
    redirect: '/admin/dashboard',

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
      {
        path: "products/combo",
        name: "admin-products-combo",
        component: () => import("../pages/admin/Combo/combo.vue")
      },
      {
        path: "options",
        name: "admin-options",
        component: () => import("../pages/admin/Options/index.vue")
      },
      {
        path: "options/category-options",
        name: "admin-category-options",
        component: () => import("../pages/admin/Category-Options/index.vue")
      },
      {
        path: "tables",
        name: "admin-tables",
        component: () => import("../pages/admin/Tables/list.vue")
      }, {
        path: 'tables/:orderId',
        name: 'admin-tables-list',
        component: () => import('../pages/admin/Tables/list.vue'),
      },
      {
        path: "tables/booking-schedule",
        name: "admin-tables-booking-schedule",
        component: () => import("../pages/admin/Tables/booking-schedule.vue")
      },
      {
        path: "tables/current-order",
        name: "admin-tables-current-order",
        component: () => import("../pages/admin/Tables/current-order.vue")
      },
      {
        path: "orders/orders-detail/:id",
        name: "admin-orders-detail",
        component: () => import("../pages/admin/Orders/orders-detail.vue")
      },
      {
        path: "demo",
        name: "demo",
        component: () => import("../pages/admin/demo/index.vue")
      },
      {
        path: "orders/history",
        name: "orders-history",
        component: () => import("../pages/admin/Orders/history.vue")
      },
      {
        path: "users/list",
        name: "users-list",
        component: () => import("../pages/admin/Users/list.vue")
      },
      {
        path: "users/list-role",
        name: "users-list-role",
        component: () => import("../pages/admin/Users/role.vue")
      },
      {
        path: 'choose-list-food/:orderId',
        name: 'admin-list-food1',
        component: () => import('../pages/admin/Tables/listsp.vue'),
      },
      {
        path: 'choose-list-food',
        name: 'admin-list-food',
        component: () => import('../pages/admin/Tables/listsp.vue'),
      },
      {
        path: "insert-food",
        name: "insert-food",
        component: () => import("../pages/admin/Products/insert.vue")
      },
      {
        path: "update-food/:id",
        name: "update-food",
        component: () => import("../pages/admin/Products/update.vue")
      },
      {
        path: "insert-category",
        name: "insert-category",
        component: () => import("../pages/admin/Categories/insert.vue")
      },
      {
        path: 'insert-reservation',
        name: 'insert-reservation-admin',
        component: () => import('../pages/admin/Tables/insert.vue'),
      },
      {
        path: 'insert-combo',
        name: 'insert-combo',
        component: () => import('../pages/admin/Combo/insert.vue'),
      },
      {
        path: 'update-combo/:id',
        name: 'update-combo',
        component: () => import('../pages/admin/Combo/update.vue'),
      },
    ],
  }
]

export default admin
