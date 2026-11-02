
<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppInput from "../../components/appInput.vue";

import { getBle, updateBle } from "../../services/bleService";

const router = useRouter();
const route = useRoute();

const PREFIX = "19121981-";

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

const form = ref({
    uuid: PREFIX,
    nama_device: "",
});

// Menampilkan popup
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

// mengambil pesan error dari Laravel
const ambilPesanError = (error) => {
    const errors = error.response?.data?.errors;

    if (errors) {
        return Object.values(errors).flat().join("\n");
    }

    return error.response?.data?.message ?? "Terjadi kesalahan. Silakan coba lagi.";
};

// format UUID 
function handleUuidInput(event) {
    let value = event.target.value
        .toLowerCase()
        .replace(/[^0-9a-f]/g, "")
        .slice(0, 28);

    let formatted = value;

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

    uuidError.value = "";
}

// Validasi UUID
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

// data BLE
async function loadData() {
    loading.value = true;

    try {
        const response = await getBle();

        const ble = response.data.data.find(
            (item) => item.id == route.params.id
        );

        if (!ble) {
            tampilkanNotifikasi(
                "error",
                "Data Tidak Ditemukan",
                "Perangkat BLE yang ingin diedit tidak ditemukan.",
                true
            );

            return;
        }

        const uuid = (ble.uuid ?? "").toLowerCase();

        // ambil bagian setelah prefix
        let suffix = "";

        if (uuid.startsWith(PREFIX)) {
            suffix = uuid.substring(PREFIX.length);
        }

        uuidSuffix.value = suffix;

        form.value = {
            uuid: uuid,
            nama_device: ble.nama_device,
        };
    } catch (error) {
        console.error(error);

        tampilkanNotifikasi(
            "error",
            "Gagal Memuat Data",
            ambilPesanError(error)
        );
    } finally {
        loading.value = false;
    }
}


async function simpanPerubahan() {
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
        await updateBle(route.params.id, form.value);

        tampilkanNotifikasi(
            "success",
            "Berhasil!",
            "Data perangkat BLE berhasil diperbarui.",
            true
        );
    } catch (error) {
        console.error(error);

        tampilkanNotifikasi(
            "error",
            "Gagal Memperbarui BLE",
            ambilPesanError(error)
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
        <div class="max-w-2xl mx-auto space-y-6">

            <div>
                <h1 class="text-2xl font-bold dark:text-white">
                    Edit BLE
                </h1>

                <p class="text-slate-500 dark:text-slate-400">
                    Ubah data perangkat Bluetooth Low Energy.
                </p>
            </div>
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-5"
            >

                <div>
                    <label class="mb-2 block font-medium dark:text-white">
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
                    <label class="block mb-2 font-medium dark:text-white">
                        Nama Device
                    </label>

                    <AppInput
                        v-model="form.nama_device"
                        placeholder="Nama Device"
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
                        @click="simpanPerubahan"
                    >
                        {{ loading ? "Menyimpan..." : "Simpan Perubahan" }}
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
