
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppInput from "../../components/appInput.vue";
import { createUser } from "../../services/userService";

const router = useRouter();
const loading = ref(false);
const showNotification = ref(false);
const redirectAfterClose = ref(false);

const notification = ref({
    type: "success",
    title: "",
    message: "",
});
const form = ref({
    id_role: 2,
    nama: "",
    nip: "",
    email: "",
    notlp: "",
    alamat: "",
    password: "",
});

const tampilkanNotifikasi = (type, title, message, redirect = false) => {
    notification.value = {
        type,
        title,
        message,
    };

    redirectAfterClose.value = redirect;
    showNotification.value = true;
};
const tutupNotifikasi = () => {
    showNotification.value = false;

    if (redirectAfterClose.value) {
        router.push("/peserta");
    }
};
const ambilPesanError = (error) => {
    const errors = error.response?.data?.errors;

    if (errors) {
        return Object.values(errors).flat().join("\n");
    }

    return error.response?.data?.message ?? "Terjadi kesalahan. Silakan coba lagi.";
};

const simpanUser = async () => {
    loading.value = true;
    try {
        await createUser(form.value);
        tampilkanNotifikasi(
            "success",
            "Berhasil!",
            "Data user berhasil ditambahkan.",
            true
        );
    } catch (error) {
        console.error(error);
        tampilkanNotifikasi(
            "error",
            "Gagal Menambahkan User",
            ambilPesanError(error)
        );
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold dark:text-white">
                    Tambah User
                </h1>
                <p class="text-slate-500 dark:text-slate-400">
                    Tambahkan data user pegawai.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-5">
                <div>
                    <label class="block mb-2 dark:text-white">
                        Nama
                    </label>
                    <AppInput
                        v-model="form.nama"
                        placeholder="Masukkan nama"/>
                </div>

                <div>
                    <label class="block mb-2 dark:text-white">
                        NIP
                    </label>
                    <AppInput
                        v-model="form.nip"
                        placeholder="Masukkan NIP"/>
                </div>
                <div>
                    <label class="block mb-2 dark:text-white">
                        Email
                    </label>
                    <AppInput
                        type="email"
                        v-model="form.email"
                        placeholder="Masukkan email"
                    />
                </div>

                <div>
                    <label class="block mb-2 dark:text-white">
                        Nomor Telepon
                    </label>
                    <AppInput
                        v-model="form.notlp"
                        placeholder="08xxxxxxxxxx" />
                </div>

                <div>
                    <label class="block mb-2 dark:text-white">
                        Alamat
                    </label>
                    <textarea
                        v-model="form.alamat"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               dark:bg-slate-800 dark:border-slate-600
                               dark:text-white"
                        placeholder="Masukkan alamat">
                    </textarea>
                </div>
                <div>
                    <label class="block mb-2 dark:text-white">
                        Password
                    </label>

                    <AppInput
                        type="password"
                        v-model="form.password"
                        placeholder="Masukkan password"/>
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
                        @click="simpanUser">
                        {{ loading ? "Menyimpan..." : "Simpan" }}
                    </AppButton>
                </div>
            </div>
        </div>

        <div
            v-if="showNotification"
            class="fixed inset-0 z-[100] flex items-center justify-center
                   bg-black/50 p-4">
            <div
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl
                dark:bg-slate-800">
                <div class="flex flex-col items-center text-center">
                    <div
                        v-if="notification.type === 'success'"
                        class="flex h-16 w-16 items-center justify-center
                        rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg
                            class="h-9 w-9 text-green-600 dark:text-green-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
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
                        rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg
                            class="h-9 w-9 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
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
                        dark:text-white">
                        {{ notification.title }}
                    </h2>

                    <p
                        class="mt-2 text-sm text-slate-600 whitespace-pre-line
                        dark:text-slate-300">
                        {{ notification.message }}
                    </p>
                    <div class="mt-6 w-full">
                        <AppButton
                            class="w-full"
                            @click="tutupNotifikasi">
                            {{
                                redirectAfterClose
                                    ? "Kembali ke Data User"
                                    : "Mengerti"
                            }}
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
