<script setup>
import {
    Bars3Icon,
    SunIcon,
    MoonIcon,
} from "@heroicons/vue/24/outline";

import { useDark, useToggle } from "@vueuse/core";
import { useAppStore } from "../stores/app";

const app = useAppStore();

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <header
        class="
            sticky top-0 z-50
            flex h-16
            items-center justify-between

            border-b
            border-slate-200
            bg-white

            px-4
            shadow-sm

            dark:border-slate-800
            dark:bg-slate-900

            sm:px-6
            lg:px-8
        "
    >

        <!-- KIRI -->
        <div class="flex items-center gap-3">

            <!-- Toggle Sidebar -->
            <button
                @click="app.sidebarOpen = !app.sidebarOpen"
                class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-xl

                    text-slate-600
                    transition

                    hover:bg-slate-100
                    hover:text-[#6C9BD2]

                    dark:text-slate-300
                    dark:hover:bg-slate-800
                "
            >
                <Bars3Icon class="h-6 w-6" />
            </button>

            <!-- Page Title -->
            <div>
                <h1
                    class="
                        text-lg font-bold
                        text-slate-800
                        dark:text-white

                        sm:text-xl
                    "
                >
                    Dashboard
                </h1>

                <p
                    class="
                        hidden
                        text-xs
                        text-slate-500
                        dark:text-slate-400

                        sm:block
                    "
                >
                    BlueAttend Administration
                </p>
            </div>

        </div>


        <!-- KANAN -->
        <div class="flex items-center gap-2 sm:gap-3">

            <!-- Dark Mode -->
            <button
                @click="toggleDark()"
                class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-xl

                    bg-slate-100
                    text-slate-600

                    transition

                    hover:bg-slate-200

                    dark:bg-slate-800
                    dark:text-slate-200
                    dark:hover:bg-slate-700
                "
            >
                <SunIcon
                    v-if="isDark"
                    class="h-5 w-5 text-yellow-400"
                />

                <MoonIcon
                    v-else
                    class="h-5 w-5"
                />
            </button>


            <!-- User -->
            <div
                class="
                    flex
                    items-center
                    gap-3
                    rounded-xl
                    px-2 py-1.5
                    transition
                    hover:bg-slate-100

                    dark:hover:bg-slate-800
                "
            >
                <!-- Avatar -->
                <div
                    class="
                        flex h-9 w-9
                        items-center justify-center
                        rounded-full
                        bg-[#6C9BD2]
                        text-sm font-bold
                        text-white
                        shadow-sm
                    "
                >
                    A
                </div>

                <!-- User Info -->
                <div class="hidden sm:block">
                    <p
                        class="
                            text-sm font-semibold
                            text-slate-800
                            dark:text-white
                        "
                    >
                        Admin
                    </p>

                    <div class="flex items-center gap-1.5">
                        <span
                            class="
                                h-1.5 w-1.5
                                rounded-full
                                bg-green-500
                            "
                        ></span>

                        <p
                            class="
                                text-xs
                                text-slate-500
                                dark:text-slate-400
                            "
                        >
                            Online
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </header>
</template>
