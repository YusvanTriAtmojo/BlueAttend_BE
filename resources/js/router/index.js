import { createRouter, createWebHistory } from "vue-router";

import Login from "../pages/login.vue";
import Dashboard from "../pages/dashboard.vue";
import BleIndex from "../pages/ble/index.vue";
import BleCreate from "../pages/ble/bleCreate.vue";
import BleEdit from "../pages/ble/bleUpdate.vue";
import EventIndex from "../pages/event/index.vue";
import EventCreate from "../pages/event/eventCreate.vue";
import EventEdit from "../pages/event/eventUpdate.vue";
import UserIndex from "../pages/user/index.vue";
import UserCreate from "../pages/user/userCreate.vue";
import UserEdit from "../pages/user/userUpdate.vue";
import PresensiIndex from "../pages/presensi/index.vue";

const routes = [
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: {
            guestOnly: true,
        },
    },
    {
        path: "/",
        name: "dashboard",
        component: Dashboard,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/ble",
        name: "ble",
        component: BleIndex,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/ble/create",
        name: "ble.create",
        component: BleCreate,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/ble/:id/edit",
        name: "ble.edit",
        component: BleEdit,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/event",
        name: "event",
        component: EventIndex,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/event/create",
        name: "event.create",
        component: EventCreate,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/event/:id/edit",
        name: "event.edit",
        component: EventEdit,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/peserta",
        name: "user",
        component: UserIndex,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/peserta/create",
        name: "user.create",
        component: UserCreate,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/peserta/:id",
        name: "user.edit",
        component: UserEdit,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/presensi",
        name: "presensi",
        component: PresensiIndex,
        meta: {
            requiresAuth: true,
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const token = localStorage.getItem("token");

    // saat halaman membutuhkan login tetapi token tidak ada
    if (to.matched.some((record) => record.meta.requiresAuth) && !token) {
        return { name: "login" };
    }

    // saat sudah login dan membuka halaman login
    if (to.matched.some((record) => record.meta.guestOnly) && token) {
        return { name: "dashboard" };
    }
    return true;
});

export default router;