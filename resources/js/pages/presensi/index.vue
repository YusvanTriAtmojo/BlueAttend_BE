
<script setup>
import { ref, computed, onMounted } from "vue";

import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppTable from "../../components/appTabel.vue";

import { getPresensi } from "../../services/presensiService";

const presensi = ref([]);
const loading = ref(false);
const searchNama = ref("");
const currentPage = ref(1);
const perPage = 10;
const filteredPresensi = computed(() => {
    const keyword = searchNama.value.trim().toLowerCase();
    if (!keyword) return presensi.value;
    return presensi.value.filter((item) =>
        (item.nama_user || "").toLowerCase().includes(keyword)
    );
});


const totalPages = computed(() =>
    Math.ceil(filteredPresensi.value.length / perPage)
);

const paginatedPresensi = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredPresensi.value.slice(start, start + perPage);
});

function cariPeserta() {
    currentPage.value = 1;
}

const showNotification = ref(false);
const notification = ref({
    type: "success",
    title: "",
    message: "",
});

function tampilkanNotifikasi(type, title, message) {
    notification.value = { type, title, message };
    showNotification.value = true;
}

function tutupNotifikasi() {
    showNotification.value = false;
}


async function loadPresensi() {
    loading.value = true;

    try {
        const response = await getPresensi();
        presensi.value = response.data.data || [];
        currentPage.value = 1;
    } catch (error) {
        console.error("Gagal mengambil data presensi:", error);
        console.error(error.response?.data);
        tampilkanNotifikasi(
            "error",
            "Gagal Memuat Data",
            error.response?.data?.message ??
                "Data presensi gagal dimuat. Silakan coba lagi."
        );
    } finally {
        loading.value = false;
    }
}

function formatTanggal(tanggal) {
    if (!tanggal) return "-";

    const date = new Date(tanggal + "T00:00:00");
    if (isNaN(date.getTime())) return "-";
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
}

onMounted(() => {
    loadPresensi();
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Data Presensi
                </h1>
                <p class="text-slate-500 dark:text-slate-400">
                    Kelola dan lihat seluruh riwayat presensi peserta.
                </p>
            </div>
            <div
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Total Data Presensi
                </p>
                <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">
                    {{ filteredPresensi.length }}
                </p>
                <p
                    v-if="searchNama"
                    class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                >
                    Hasil pencarian dari {{ presensi.length }} data presensi.
                </p>
            </div>
            <div
                class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-4">
                <div class="relative w-full md:max-w-sm">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-4.35-4.35"
                        />
                    </svg>

                    <input
                        v-model="searchNama"
                        @input="cariPeserta"
                        type="text"
                        placeholder="Cari nama peserta..."
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 py-2.5 pl-10 pr-10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                    />
                    <button
                        v-if="searchNama"
                        @click="searchNama = ''; cariPeserta()"
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500"
                        title="Hapus pencarian">
                        &times;
                    </button>
                </div>
            </div>

            <AppTable>
                <table class="w-full text-sm">
                    <thead
                        class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold w-16">
                                No
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                Nama User
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                Nama Event
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                Tanggal
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                Jam
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td
                                colspan="6"
                                class="py-8 text-center text-slate-500 dark:text-slate-400">
                                Memuat data presensi...
                            </td>
                        </tr>

                        <template v-else-if="paginatedPresensi.length > 0">
                            <tr
                                v-for="(item, index) in paginatedPresensi"
                                :key="item.id"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ (currentPage - 1) * perPage + index + 1 }}
                                </td>

                                <td class="px-4 py-4 font-medium text-slate-700 dark:text-slate-200">
                                    {{ item.nama_user }}
                                </td>

                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ item.nama_event }}
                                </td>

                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                    {{ formatTanggal(item.tanggal) }}
                                </td>

                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                    {{ item.jam }}
                                </td>
                            </tr>
                        </template>

                        <tr v-else>
                            <td
                                colspan="6"
                                class="py-10 text-center text-slate-500 dark:text-slate-400">
                                {{
                                    searchNama
                                        ? "Peserta dengan nama tersebut tidak ditemukan."
                                        : "Data presensi belum tersedia."
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </AppTable>

            <div
                v-if="totalPages > 1"
                class="mt-5 flex items-center justify-between">
                <AppButton
                    color="secondary"
                    :disabled="currentPage === 1"
                    @click="currentPage--">
                    Sebelumnya
                </AppButton>

                <span class="text-sm text-slate-600 dark:text-slate-300">
                    Halaman {{ currentPage }} dari {{ totalPages }}
                </span>

                <AppButton
                    color="secondary"
                    :disabled="currentPage === totalPages"
                    @click="currentPage++">
                    Selanjutnya
                </AppButton>
            </div>
            <div
                v-if="showNotification"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 p-4"
                @click.self="tutupNotifikasi"
            >
                <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 p-6 shadow-2xl">
                    <div class="text-center">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full"
                            :class="
                                notification.type === 'success'
                                    ? 'bg-green-100 dark:bg-green-900/40'
                                    : 'bg-red-100 dark:bg-red-900/40'
                            "
                        >
                            <svg
                                v-if="notification.type === 'success'"
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-9 w-9 text-green-600 dark:text-green-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <svg
                                v-else
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-9 w-9 text-red-600 dark:text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                            {{ notification.title }}
                        </h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            {{ notification.message }}
                        </p>
                        <div class="mt-6">
                            <AppButton
                                block
                                @click="tutupNotifikasi">
                                Mengerti
                            </AppButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
