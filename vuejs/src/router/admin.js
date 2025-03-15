const admin = [
    {
        path: "/admin",
        component: () => import("../components/layouts/LayoutAdmin.vue"),
        children: [
            {
                path: "users",
                name: "admin-users",
                component: () => import ("../pages/admin/users/UserHome.vue")
            },

            {
                path: "roles",
                name: "admin-roles",
                component: () => import ("../pages/admin/roles/RoleHome.vue")
            },
            
            {
                path: "settings",
                name: "admin-settings",
                component: () => import ("../pages/admin/settings/SettingHome.vue")
            }
        ]
    }
]

export default admin;