<script setup>
import { Bars3Icon, SunIcon, MoonIcon } from "@heroicons/vue/24/outline";

import { useDark, useToggle } from "@vueuse/core";
import { useAppStore } from "../stores/app";

const app = useAppStore();
const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <header
        class="sticky top-0 z-50 flex h-16 items-center justify-between border-b border-border bg-surface px-4 shadow-sm dark:border-dark-border dark:bg-dark-surface sm:px-6 lg:px-8"
    >
        <div class="flex items-center gap-3">
            <button
                @click="app.sidebarOpen = !app.sidebarOpen"
                class="flex h-10 w-10 items-center justify-center rounded-xl text-text-secondary transition hover:bg-surface-hover hover:text-primary dark:text-dark-text-secondary dark:hover:bg-dark-surface-hover dark:hover:text-primary"
            >
                <Bars3Icon class="h-6 w-6" />
            </button>

            <div>
                <h1
                    class="text-lg font-bold text-text-primary dark:text-dark-text-primary sm:text-xl"
                >
                    Dashboard
                </h1>

                <p
                    class="hidden text-xs text-text-secondary dark:text-dark-text-muted sm:block"
                >
                    BlueAttend Administration
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <button
                @click="toggleDark()"
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-surface-hover text-text-secondary transition hover:bg-secondary dark:bg-dark-surface-hover dark:text-dark-text-secondary dark:hover:bg-secondary"
            >
                <SunIcon v-if="isDark" class="h-5 w-5 text-icon-sun" />
                <MoonIcon v-else class="h-5 w-5" />
            </button>
        </div>
    </header>
</template>
