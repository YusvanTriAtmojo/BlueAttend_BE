
<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppInput from "../../components/appInput.vue";
import { getBle } from "../../services/bleService";
import { getEvent, updateEvent } from "../../services/eventService";

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const bles = ref([]);
const showNotification = ref(false);
const redirectAfterClose = ref(false);
const notification = ref({
    type: "success",
    title: "",
    message: "",
});

function tampilkanNotifikasi(type, title, message, redirect = false) {
    notification.value = { type, title, message };
    redirectAfterClose.value = redirect;
    showNotification.value = true;
}

function tutupNotifikasi() {
    showNotification.value = false;
    if (redirectAfterClose.value) {
        router.push("/event");
    }
}

const form = ref({
    nama_event: "",
    waktu_mulai: "",
    tenggat_waktu: "",
    token: "",
    area: "",
    ble: [],
});

function ambilPesanError(error, pesanDefault) {
    const data = error.response?.data;
    if (data?.errors) {
        return Object.values(data.errors).flat().join("\n");
    }
    return data?.message ?? pesanDefault;
}

async function loadData() {
    loading.value = true;
    try {
        const bleResponse = await getBle();
        bles.value = bleResponse.data.data;
        const eventResponse = await getEvent();
        const event = eventResponse.data.data.find(
            (item) => item.id == route.params.id
        );

        if (!event) {
            tampilkanNotifikasi(
                "error",
                "Event Tidak Ditemukan",
                "Data event yang ingin diubah tidak ditemukan.",
                true
            );
            return;
        }

        form.value = {
            nama_event: event.nama_event,
            waktu_mulai: event.waktu_mulai,
            tenggat_waktu: event.tenggat_waktu,
            token: event.token,
            area: event.area,
            ble: event.ble.map((item) => item.uuid),
        };

    } catch (error) {
        console.error(error);
        tampilkanNotifikasi(
            "error",
            "Gagal Memuat Data",
            ambilPesanError(
                error,
                "Terjadi kesalahan saat mengambil data event."
            ),
            true
        );
    } finally {
        loading.value = false;
    }
}

async function simpanPerubahan() {
    loading.value = true;
    try {
        await updateEvent(route.params.id, form.value);
        tampilkanNotifikasi(
            "success",
            "Berhasil!",
            "Data event berhasil diperbarui.",
            true
        );
    } catch (error) {
        console.error(error);
        tampilkanNotifikasi(
            "error",
            "Gagal Memperbarui Event",
            ambilPesanError(
                error,
                "Terjadi kesalahan saat memperbarui event."
            )
        );

    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadData();
});
</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Edit Event
                </h1>
                <p class="text-slate-500 dark:text-slate-400">
                    Ubah data event.
                </p>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-5"
            >
                <div>
                    <label class="block mb-2 font-medium dark:text-white">
                        Nama Event
                    </label>
                    <AppInput
                        v-model="form.nama_event"
                        placeholder="Masukkan nama event"
                    />
                </div>

                <div>
                    <label class="block mb-2 font-medium dark:text-white">
                        Waktu Mulai
                    </label>
                    <AppInput
                        type="datetime-local"
                        v-model="form.waktu_mulai"
                    />
                </div>

                <div>
                    <label class="block mb-2 font-medium dark:text-white">
                        Tenggat Waktu
                    </label>
                    <AppInput
                        type="datetime-local"
                        v-model="form.tenggat_waktu"
                    />
                </div>

                <div>
                    <label class="block mb-2 font-medium dark:text-white">
                        Token
                    </label>
                    <AppInput
                        v-model="form.token"
                        placeholder="Masukkan token"
                    />
                </div>
>
                <div>
                    <label class="block mb-2 font-medium dark:text-white">
                        Area
                    </label>

                    <select
                        v-model="form.area"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 dark:bg-slate-800 dark:border-slate-600 dark:text-white"
                    >
                        <option disabled value="">Pilih Area</option>
                        <option value="Sangat Dekat">Sangat Dekat</option>
                        <option value="Dekat">Dekat</option>
                        <option value="Menengah">Menengah</option>
                        <option value="Cukup Jauh">Cukup Jauh</option>
                        <option value="Jauh">Jauh</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-3 font-medium dark:text-white">
                        Pilih BLE
                    </label>

                    <div class="border border-slate-300 dark:border-slate-700 rounded-lg p-4 space-y-3">
                        <label
                            v-for="ble in bles"
                            :key="ble.id"
                            class="flex items-start gap-3 cursor-pointer dark:text-white"
                        >
                            <input
                                type="checkbox"
                                :value="ble.uuid"
                                v-model="form.ble"
                                class="mt-1 accent-blue-600"
                            />

                            <div>
                                <p class="font-medium text-slate-800 dark:text-white">
                                    {{ ble.nama_device }}
                                </p>

                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ ble.uuid }}
                                </p>
                            </div>
                        </label>

                        <p
                            v-if="bles.length === 0"
                            class="text-center text-slate-500"
                        >
                            Belum ada data BLE.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <AppButton
                        color="secondary"
                        :disabled="loading"
                        @click="router.back()"
                    >
                        Batal
                    </AppButton>
                    <AppButton
                        :disabled="loading"
                        @click="simpanPerubahan"
                    >
                        {{ loading ? "Menyimpan..." : "Simpan Perubahan" }}
                    </AppButton>
                </div>
            </div>
        </div>
        <div
            v-if="showNotification"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl overflow-hidden"
            >
                <div class="p-6 text-center">
                    <div
                        v-if="notification.type === 'success'"
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-9 w-9 text-green-600 dark:text-green-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>
                    <div
                        v-else
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
                    >
                        <svg
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
                    <h2
                        class="text-xl font-bold text-slate-800 dark:text-white"
                    >
                        {{ notification.title }}
                    </h2>
                    <p
                        class="mt-2 text-sm text-slate-500 dark:text-slate-400 whitespace-pre-line"
                    >
                        {{ notification.message }}
                    </p>
                </div>

                <div
                    class="flex justify-center border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 px-6 py-4"
                >
                    <AppButton
                        :color="notification.type === 'success' ? 'primary' : 'danger'"
                        @click="tutupNotifikasi"
                    >
                        {{
                            redirectAfterClose
                                ? "Kembali ke Data Event"
                                : "Mengerti"
                        }}
                    </AppButton>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
