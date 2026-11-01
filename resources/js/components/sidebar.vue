<script setup>
import { RouterLink, useRoute, useRouter } from "vue-router";
import { useAppStore } from "../stores/app";

import {
    HomeIcon,
    SignalIcon,
    CalendarDaysIcon,
    UsersIcon,
    ClipboardDocumentListIcon,
     ArrowLeftStartOnRectangleIcon,
} from "@heroicons/vue/24/outline";

const route = useRoute();
const router = useRouter();
const app = useAppStore();

function logout() {
    localStorage.removeItem("token");
    router.push("/login");
}

const menus = [
    { title: "Dashboard", icon: HomeIcon, route: "/" },
    { title: "BLE", icon: SignalIcon, route: "/ble" },
    { title: "Event", icon: CalendarDaysIcon, route: "/event" },
    { title: "Peserta", icon: UsersIcon, route: "/peserta" },
    { title: "Presensi", icon: ClipboardDocumentListIcon, route: "/presensi" },
];
</script>

<template>
    <aside
        :class="[

            // dekstop
            app.sidebarOpen
                ? 'md:w-44'
                : 'md:w-20',

            // hp
            app.sidebarOpen
                ? 'max-h-[500px]'
                : 'max-h-0',

            'fixed left-0 top-16 z-40 w-full',
            'overflow-hidden',

            // reset dekstop
            'md:top-0',
            'md:h-screen',
            'md:max-h-none',
            'md:overflow-visible',

            'bg-[#6C9BD2]',
            'dark:bg-slate-900',
            'shadow-xl',

            'transition-all duration-300',
        ]"
    >
        <!-- LOGO -->
        <div
            class="
                hidden
                h-16
                items-center
                px-4
                md:flex
                md:h-16
                md:justify-center
            "
        >
            <!-- Expanded -->
            <div
                v-if="app.sidebarOpen"
                class="flex items-center gap-3"
            >
                <div
                    class="
                        flex h-11 w-11
                        items-center justify-center
                        overflow-hidden
                        rounded-xl
                        bg-white
                        shadow-md
                    "
                >
                    <img
                        :src="'/logo.png'"
                        alt="Logo BlueAttend"
                        class="h-full w-full object-contain p-1"
                    />
                </div>

                <div>
                    <h1 class="text-lg font-bold text-white">
                        BlueAttend
                    </h1>

                    <p
                        class="
                            text-[10px]
                            text-blue-100
                        "
                    >
                        Attendance System
                    </p>
                </div>
            </div>

            <!-- ketutup -->
            <div
                v-else
                class="
                    flex h-11 w-11
                    items-center justify-center
                    rounded-xl
                    bg-white
                    shadow-md
                "
            >
                 <img
                        :src="'/logo.png'"
                        alt="Logo BlueAttend"
                        class="h-full w-full object-contain p-1"
                    />
            </div>
        </div>

        <!-- hp header -->
        <div
            class="
                flex
                items-center
                justify-between
                border-b
                border-white/20
                px-4
                py-3

                md:hidden
            "
        >
            <div class="flex items-center gap-3">
                <div
                    class="
                        flex h-9 w-9
                        items-center justify-center
                        rounded-lg
                        bg-white
                        shadow
                    "
                >
                    <span
                        class="
                            text-lg font-bold
                            text-[#6C9BD2]
                        "
                    >
                        B
                    </span>
                </div>

                <div>
                    <h1 class="text-base font-bold text-white">
                        BlueAttend
                    </h1>

                    <p class="text-[9px] text-blue-100">
                        Attendance System
                    </p>
                </div>
            </div>
        </div>

        <!-- MENU -->
        <nav
            class="
                px-3
                py-3

                md:px-4
                md:py-5
            "
        >
            <div
                class="
                    flex
                    flex-col
                    gap-2
                "
            >
                <RouterLink
                    v-for="menu in menus"
                    :key="menu.route"
                    :to="menu.route"
                    @click="app.sidebarOpen = false"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-xl
                        px-4
                        py-3
                        transition-all
                        duration-200
                    "
                    :class="[
                        (
                            menu.route === '/'
                                ? route.path === '/'
                                : route.path.startsWith(menu.route)
                        )
                            ? 'bg-white text-[#6C9BD2] shadow-md'
                            : 'text-white hover:bg-white/15',

                        !app.sidebarOpen
                            ? 'md:justify-center'
                            : '',
                    ]"
                >
                    <component
                        :is="menu.icon"
                        class="h-5 w-5 shrink-0"
                    />

                    <span
                        class="text-sm font-medium md:text-base"
                        :class="
                            app.sidebarOpen
                                ? 'md:block'
                                : 'md:hidden'
                        "
                    >
                        {{ menu.title }}
                    </span>
                </RouterLink>
            </div>
        </nav>


        <!-- logout -->
        <div
            class="
                border-t
                border-white/20
                p-4

                md:absolute
                md:bottom-0
                md:left-0
                md:right-0
            "
        >
            <button
                @click="logout"
                class="
                    flex w-full
                    items-center
                    gap-3
                    rounded-xl
                    px-4 py-3
                    text-white
                    transition

                    hover:bg-red-500
                "
                :class="
                    !app.sidebarOpen
                        ? 'md:justify-center'
                        : ''
                "
            >
                <ArrowLeftStartOnRectangleIcon
                    class="h-5 w-5 shrink-0"
                />

                <span
                    class="md:block"
                    :class="
                        app.sidebarOpen
                            ? 'md:block'
                            : 'md:hidden'
                    "
                >
                    Logout
                </span>
            </button>
        </div>
    </aside>
</template>
