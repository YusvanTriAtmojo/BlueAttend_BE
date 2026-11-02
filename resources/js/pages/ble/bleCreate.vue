
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppInput from "../../components/appInput.vue";

import { createBle } from "../../services/bleService";

const router = useRouter();

const PREFIX = "19121981-";

const form = ref({
    uuid: PREFIX,
    nama_device: "",
});

const loading = ref(false);
const uuidError = ref("");
const uuidSuffix = ref("");

// popup
const showNotification = ref(false);
const redirectAfterClose = ref(false);

const notification = ref({
    type: "success",
    title: "",
    message: "",
});

// menampilkan popup
const tampilkanNotifikasi = (type, title, message, redirect = false) => {
    notification.value = {
        type,
        title,
        message,
    };

    redirectAfterClose.value = redirect;
    showNotification.value = true;
};

// menutup popup
const tutupNotifikasi = () => {
    showNotification.value = false;

    if (redirectAfterClose.value) {
        router.push("/ble");
    }
};

// Format isian UUID 
function handleUuidInput(event) {
    let value = event.target.value
        .toLowerCase()
        .replace(/[^0-9a-f]/g, "")
        .slice(0, 28);

    let formatted = value;

    // format 4-4-4-12
    if (value.length > 4) {
        formatted =
            value.slice(0, 4) +
            "-" +
            value.slice(4);
    }

    if (value.length > 8) {
        formatted =
            value.slice(0, 4) +
            "-" +
            value.slice(4, 8) +
            "-" +
            value.slice(8);
    }

    if (value.length > 12) {
        formatted =
            value.slice(0, 4) +
            "-" +
            value.slice(4, 8) +
            "-" +
            value.slice(8, 12) +
            "-" +
            value.slice(12);
    }

    event.target.value = formatted;

    uuidSuffix.value = formatted;
    form.value.uuid = PREFIX + formatted;

    // hapus error saat pengguna mulai memperbaiki UUID
    uuidError.value = "";
}

// validasi UUID
function validateUuid() {
    const uuidRegex =
        /^19121981-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/;

    if (!uuidRegex.test(form.value.uuid)) {
        uuidError.value =
            "UUID harus berformat 8-4-4-4-12 dan hanya menggunakan 0-9, a-f.";

        tampilkanNotifikasi(
            "error",
            "UUID Tidak Valid",
            uuidError.value
        );

        return false;
    }

    uuidError.value = "";
    return true;
}

// mengambil pesan error dari Laravel
const ambilPesanError = (error) => {
    const errors = error.response?.data?.errors;

    if (errors) {
        return Object.values(errors).flat().join("\n");
    }

    return error.response?.data?.message ?? "Terjadi kesalahan. Silakan coba lagi.";
};

// Simpan BLE
async function simpanBle() {
    if (!validateUuid()) {
        return;
    }

    if (!form.value.nama_device.trim()) {
        tampilkanNotifikasi(
            "error",
            "Nama Device Kosong",
            "Nama device wajib diisi."
        );

        return;
    }

    loading.value = true;

    try {
        await createBle(form.value);

        tampilkanNotifikasi(
            "success",
            "Berhasil!",
            "Perangkat BLE berhasil ditambahkan.",
            true
        );
    } catch (error) {
        console.error(error);

        tampilkanNotifikasi(
            "error",
            "Gagal Menambahkan BLE",
            ambilPesanError(error)
        );
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Tambah BLE
                </h1>

                <p class="text-slate-500 dark:text-slate-400">
                    Tambahkan perangkat Bluetooth Low Energy baru.
                </p>
            </div>
            <div
                class="space-y-5 rounded-xl bg-white p-6 shadow dark:bg-slate-800"
            >
                <div>
                    <label
                        class="mb-2 block text-sm font-medium dark:text-white"
                    >
                        UUID
                    </label>

                    <div
                        class="flex overflow-hidden rounded-lg border border-slate-300
                               bg-white dark:border-slate-600 dark:bg-slate-700"
                    >

                        <div
                            class="flex shrink-0 items-center bg-slate-100 px-3
                                   font-mono text-sm font-semibold text-slate-600
                                   dark:bg-slate-600 dark:text-slate-200"
                        >
                            19121981-
                        </div>
                        <input
                            :value="uuidSuffix"
                            type="text"
                            maxlength="36"
                            autocomplete="off"
                            spellcheck="false"
                            placeholder="xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                            class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5
                                   font-mono text-sm text-slate-900 outline-none
                                   focus:ring-0 dark:text-white"
                            @input="handleUuidInput"
                        />
                    </div>

                    <p
                        v-if="uuidError"
                        class="mt-2 text-sm text-red-500"
                    >
                        {{ uuidError }}
                    </p>

                    <p
                        v-else
                        class="mt-2 text-xs text-slate-500 dark:text-slate-400"
                    >
                        Format: 19121981-XXXX-XXXX-XXXX-XXXXXXXXXXXX
                    </p>
                </div>

                <div>
                    <label
                        class="mb-2 block text-sm font-medium dark:text-white"
                    >
                        Nama Device
                    </label>

                    <AppInput
                        v-model="form.nama_device"
                        placeholder="Masukkan nama device"
                    />
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
                        @click="simpanBle"
                    >
                        {{ loading ? "Menyimpan..." : "Simpan" }}
                    </AppButton>
                </div>
            </div>
        </div>

        <div
            v-if="showNotification"
            class="fixed inset-0 z-[100] flex items-center justify-center
                   bg-black/50 p-4"
        >
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl
                       dark:bg-slate-800"
            >
                <div class="flex flex-col items-center text-center">
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

                    <h2
                        class="mt-4 text-xl font-bold text-slate-800
                               dark:text-white"
                    >
                        {{ notification.title }}
                    </h2>

                    <p
                        class="mt-2 text-sm text-slate-600 whitespace-pre-line
                               dark:text-slate-300"
                    >
                        {{ notification.message }}
                    </p>

                    <div class="mt-6 w-full">
                        <AppButton
                            class="w-full"
                            @click="tutupNotifikasi"
                        >
                            {{
                                redirectAfterClose
                                    ? "Kembali ke Data BLE"
                                    : "Mengerti"
                            }}
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
