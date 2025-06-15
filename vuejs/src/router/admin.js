const admin = [
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../components/layouts/LayoutAdmin.vue'),
    redirect: '/admin/dashboard',
    meta: { requiresAdmin: true },
    children: [
      {
        path: "dashboard",
        name: "admin-dashboard",
        component: () => import("../pages/admin/Dashboard/index.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "categories",
        name: "admin-categories",
        component: () => import("../pages/admin/Categories/index.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "products",
        name: "admin-products",
        component: () => import("../pages/admin/Products/list.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "products/combo",
        name: "admin-products-combo",
        component: () => import("../pages/admin/Combo/combo.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "options",
        name: "admin-options",
        component: () => import("../pages/admin/Options/index.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "options/category-options",
        name: "admin-category-options",
        component: () => import("../pages/admin/Category-Options/index.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "tables",
        name: "admin-tables",
        component: () => import("../pages/admin/Tables/list.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: 'tables/:orderId',
        name: 'admin-tables-list',
        component: () => import('../pages/admin/Tables/list.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: "tables/booking-schedule",
        name: "admin-tables-booking-schedule",
        component: () => import("../pages/admin/Tables/booking-schedule.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "tables/current-order",
        name: "admin-tables-current-order",
        component: () => import("../pages/admin/Tables/current-order.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "orders/orders-detail/:id",
        name: "admin-orders-detail",
        component: () => import("../pages/admin/Orders/orders-detail.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "demo",
        name: "demo",
        component: () => import("../pages/admin/demo/index.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "orders/history",
        name: "orders-history",
        component: () => import("../pages/admin/Orders/history.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "users/list-customer",
        name: "users-list-customer",
        component: () => import("../pages/admin/Users/list.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "users/list-employee",
        name: "users-list-employee",
        component: () => import("../pages/admin/Users/list.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "users/list-role",
        name: "users-list-role",
        component: () => import("../pages/admin/Users/role.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "users/list-role-detail/:id",
        name: "users-list-role-detail",
        component: () => import("../pages/admin/Users/detail.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "users/list-role-edit/:id",
        name: "users-list-role-edit",
        component: () => import("../pages/admin/Users/detail.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "users/list-role-insert",
        name: "users-list-role-insert",
        component: () => import("../pages/admin/Users/detail.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: 'choose-list-food/:orderId',
        name: 'admin-list-food1',
        component: () => import('../pages/admin/Tables/listsp.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'choose-list-food',
        name: 'admin-list-food',
        component: () => import('../pages/admin/Tables/listsp.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: "insert-food",
        name: "insert-food",
        component: () => import("../pages/admin/Products/insert.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "update-food/:id",
        name: "update-food",
        component: () => import("../pages/admin/Products/update.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "insert-food-category",
        name: "insert-food-category",
        component: () => import("../pages/admin/Categories/insert.vue"),
        meta: { requiresAdmin: true },
      },
      {
        path: "update-food-category/:id",
        name: "update-food-category",
        component: () => import("../pages/admin/Categories/update.vue")
      },
      {
        path: 'insert-reservation',
        name: 'insert-reservation-admin',
        component: () => import('../pages/admin/Tables/insert.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'insert-combo',
        name: 'insert-combo',
        component: () => import('../pages/admin/Combo/insert.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'update-combo/:id',
        name: 'update-combo',
        component: () => import('../pages/admin/Combo/update.vue'),
        meta: { requiresAdmin: true },
      },
      {
        path: 'order-create',
        name: 'order-create',
        component: () => import('../pages/admin/Orders/order-create.vue'),
        meta: { requiresAdmin: true },
      },
    ],
  }
]

export default admin
