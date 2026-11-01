
<script setup>
import { useAppStore } from "../stores/app";

import Sidebar from "../components/sidebar.vue";
import Navbar from "../components/navbar.vue";

const app = useAppStore();
</script>

<template>
    <div
        class="
            min-h-screen
            bg-slate-50
            dark:bg-slate-950
        "
    >
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main Content -->
        <div
            :class="[
                app.sidebarOpen
                    ? 'md:ml-44'
                    : 'md:ml-20',

                'min-h-screen',
                'transition-all duration-300',
            ]"
        >
            <!-- Navbar -->
            <Navbar />

            <!-- Content -->
            <main
                class="
                    p-4
                    sm:p-6
                    lg:p-8
                "
            >
                <slot />
            </main>
        </div>
    </div>
</template>
