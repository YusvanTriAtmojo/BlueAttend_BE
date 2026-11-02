
<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";

import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppTable from "../../components/appTabel.vue";

import { getBle, deleteBle } from "../../services/bleService";
import { PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline";

const router = useRouter();

const bles = ref([]);
const loading = ref(false);
const deleting = ref(false);

const currentPage = ref(1);
const perPage = 10;


// state popup hapus
const showDeleteModal = ref(false);
const selectedBle = ref(null);

// state popup notifikasi
const showNotification = ref(false);
const notification = ref({
    type: "success",
    title: "",
    message: "",
});

// Pagination
const totalPages = computed(() =>
    Math.ceil(bles.value.length / perPage)
);

const paginatedBles = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return bles.value.slice(start, start + perPage);
});

// memuat data BLE
async function loadBle() {
    loading.value = true;

    try {
        const response = await getBle();

        bles.value = response.data.data;

        // halaman jika data pada halaman terakhir habis
        if (currentPage.value > totalPages.value && currentPage.value > 1) {
            currentPage.value = totalPages.value;
        }
    } catch (error) {
        console.error(error);

        tampilkanNotifikasi(
            "error",
            "Gagal Memuat Data",
            error.response?.data?.message ?? "Terjadi kesalahan saat mengambil data BLE."
        );
    } finally {
        loading.value = false;
    }
}

function konfirmasiHapus(ble) {
    selectedBle.value = ble;
    showDeleteModal.value = true;
}

function batalHapus() {
    if (deleting.value) return;

    showDeleteModal.value = false;
    selectedBle.value = null;
}

function tampilkanNotifikasi(type, title, message) {
    notification.value = {
        type,
        title,
        message,
    };

    showNotification.value = true;
}

function tutupNotifikasi() {
    showNotification.value = false;
}

async function hapusBle() {
    if (!selectedBle.value) return;

    deleting.value = true;

    try {
        await deleteBle(selectedBle.value.id);

        showDeleteModal.value = false;
        selectedBle.value = null;

        await loadBle();

        tampilkanNotifikasi(
            "success",
            "Berhasil!",
            "Perangkat BLE berhasil dihapus."
        );
    } catch (error) {
        console.error(error);

        showDeleteModal.value = false;

        tampilkanNotifikasi(
            "error",
            "Gagal Menghapus BLE",
            error.response?.data?.message ?? "Terjadi kesalahan saat menghapus perangkat BLE."
        );
    } finally {
        deleting.value = false;
    }
}

onMounted(() => {
    loadBle();
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Data BLE
                    </h1>

                    <p class="text-slate-500 dark:text-slate-400">
                        Kelola perangkat Bluetooth Low Energy
                    </p>
                </div>

                <AppButton @click="router.push('/ble/create')">
                    + Tambah BLE
                </AppButton>
            </div>

            <AppTable>
                <table class="w-full text-sm">
                    <thead
                        class="bg-slate-100 dark:bg-slate-700
                               text-slate-700 dark:text-slate-200"
                    >
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold w-16">
                                No
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                UUID
                            </th>

                            <th class="px-4 py-3 text-left font-semibold">
                                Nama Device
                            </th>

                            <th class="px-4 py-3 text-center font-semibold w-48">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td
                                colspan="4"
                                class="py-8 text-center text-slate-500
                                       dark:text-slate-400"
                            >
                                Memuat data...
                            </td>
                        </tr>

                        <template v-else-if="bles.length > 0">
                            <tr
                                v-for="(ble, index) in paginatedBles"
                                :key="ble.id"
                                class="border-t border-slate-200
                                       dark:border-slate-700
                                       hover:bg-slate-50 dark:hover:bg-slate-700
                                       transition"
                            >
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ (currentPage - 1) * perPage + index + 1 }}
                                </td>

                                <td class="px-4 py-4 font-mono text-slate-700 dark:text-slate-200">
                                    {{ ble.uuid }}
                                </td>

                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ ble.nama_device }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center items-center gap-2">

                                        <!-- Tombol Edit -->
                                        <button
                                            type="button"
                                            @click="
                                                router.push({
                                                    name: 'ble.edit',
                                                    params: { id: ble.id },
                                                })
                                            "
                                            title="Edit BLE"
                                            aria-label="Edit BLE"
                                            class="inline-flex h-9 w-9 items-center justify-center
                                                rounded-lg bg-amber-50 text-amber-600
                                                hover:bg-amber-100 hover:text-amber-700
                                                dark:bg-amber-900/20 dark:text-amber-400
                                                dark:hover:bg-amber-900/40
                                                transition-all duration-200"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <button
                                            type="button"
                                            @click="konfirmasiHapus(ble)"
                                            title="Hapus BLE"
                                            aria-label="Hapus BLE"
                                            class="inline-flex h-9 w-9 items-center justify-center
                                                rounded-lg bg-rose-50 text-rose-600
                                                hover:bg-rose-100 hover:text-rose-700
                                                dark:bg-rose-900/20 dark:text-rose-400
                                                dark:hover:bg-rose-900/40
                                                transition-all duration-200"
                                        >
                                            <TrashIcon class="h-5 w-5" />
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr v-else>
                            <td
                                colspan="4"
                                class="py-10 text-center text-slate-500
                                       dark:text-slate-400"
                            >
                                Data BLE belum tersedia.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </AppTable>

            <div
                v-if="totalPages > 1"
                class="mt-5 flex items-center justify-between"
            >
                <AppButton
                    color="secondary"
                    :disabled="currentPage === 1"
                    @click="currentPage--"
                >
                    Sebelumnya
                </AppButton>

                <span class="text-sm text-slate-600 dark:text-slate-300">
                    Halaman {{ currentPage }} dari {{ totalPages }}
                </span>

                <AppButton
                    color="secondary"
                    :disabled="currentPage === totalPages"
                    @click="currentPage++"
                >
                    Selanjutnya
                </AppButton>
            </div>
        </div>
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-[100] flex items-center justify-center
                   bg-black/50 p-4"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl
                       dark:bg-slate-800"
            >
                <div class="flex flex-col items-center text-center">
                    <div
                        class="flex h-16 w-16 items-center justify-center
                               rounded-full"
                    >
                        <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-red-600 dark:text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"
                                />
                            </svg>
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-slate-800 dark:text-white">
                        Hapus Perangkat BLE ?
                    </h2>

                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        Apakah kamu yakin ingin menghapus perangkat
                        <span class="font-semibold text-slate-800 dark:text-white">
                            {{ selectedBle?.nama_device }}
                        </span>
                        ?
                        <br />
                    </p>

                    <div class="mt-6 flex w-full justify-center gap-3">
                        <AppButton
                            color="secondary"
                            :disabled="deleting"
                            @click="batalHapus"
                        >
                            Batal
                        </AppButton>

                        <AppButton
                            color="danger"
                            :disabled="deleting"
                            @click="hapusBle"
                        >
                            {{ deleting ? "Menghapus..." : "Ya, Hapus" }}
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="showNotification"
            class="fixed inset-0 z-[110] flex items-center justify-center
                   bg-black/50 p-4"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl
                       dark:bg-slate-800"
            >
                <div class="flex flex-col items-center text-center">

                    <!-- Icon berhasil -->
                    <div
                        v-if="notification.type === 'success'"
                        class="flex h-16 w-16 items-center justify-center
                               rounded-full bg-green-100 dark:bg-green-900/30"
                    >
                        <svg
                            class="h-9 w-9 text-green-600 dark:text-green-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>
                    <div
                        v-else
                        class="flex h-16 w-16 items-center justify-center
                               rounded-full bg-red-100 dark:bg-red-900/30"
                    >
                        <svg
                            class="h-9 w-9 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-slate-800 dark:text-white">
                        {{ notification.title }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        {{ notification.message }}
                    </p>

                    <div class="mt-6 w-full">
                        <AppButton
                            class="w-full"
                            @click="tutupNotifikasi"
                        >
                            Mengerti
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
