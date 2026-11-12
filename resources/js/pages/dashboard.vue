<script setup>
import { onMounted, ref } from "vue";
import AdminLayout from "../layouts/adminLayout.vue";
import StatCard from "../components/statCard.vue";
import AppCard from "../components/appCard.vue";
import { getDashboard } from "../services/dashboardService";

const dashboard = ref({
    total_user: 0,
    total_ble: 0,
    total_event: 0,
    total_presensi: 0,
    presensi_terbaru: [],
});

const loading = ref(false);

async function loadDashboard() {
    loading.value = true;

    try {
        const response = await getDashboard();
        console.log("Dashboard response:", response.data);
        dashboard.value = response.data;
    } catch (error) {
        console.error("Gagal mengambil dashboard:", error);
        console.error("Response:", error.response?.data);
    } finally {
        loading.value = false;
    }
}

function formatTime(datetime) {
    if (!datetime) return "-";
    return new Date(datetime).toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });
}

onMounted(() => {
    loadDashboard();
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-8">
            <div class="rounded-3xl bg-gradient-to-r from-primary to-primary-gradient p-8 text-white shadow-lg">
                <h1 class="text-3xl font-bold">Dashboard</h1>
                <p class="mt-2 text-primary-light">ADMIN BlueAttend</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                <StatCard
                    title="Total User"
                    :value="dashboard.total_user"
                    color="blue"
                />
                <StatCard
                    title="Total BLE"
                    :value="dashboard.total_ble"
                    color="blue"
                />
                <StatCard
                    title="Total Event"
                    :value="dashboard.total_event"
                    color="blue"
                />
            </div>

            <AppCard>
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-text-primary dark:text-dark-text-primary">
                        Presensi Terbaru
                    </h2>

                    <span class="rounded-full bg-primary/20 px-3 py-1 text-sm font-medium text-primary">
                        Hari Ini
                    </span>
                </div>

                <div v-if="dashboard.presensi_terbaru.length" class="space-y-3">
                    <div
                        v-for="presensi in dashboard.presensi_terbaru"
                        :key="presensi.id"
                        class="flex items-center gap-4 rounded-xl p-3 transition hover:bg-surface-hover dark:hover:bg-dark-surface"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/20 text-lg">
                            👤
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-text-primary dark:text-dark-text-primary">
                                {{ presensi.user?.nama }}
                            </p>

                            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">
                                {{ presensi.event?.nama }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <p class="text-sm font-medium text-text-secondary dark:text-dark-text-secondary">
                                {{ formatTime(presensi.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="py-8 text-center text-text-secondary dark:text-dark-text-secondary"
                >
                    Belum ada presensi hari ini.
                </div>
            </AppCard>
        </div>
    </AdminLayout>
</template>
