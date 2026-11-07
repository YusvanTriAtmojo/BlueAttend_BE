
<script setup>
import { ref, computed, onMounted } from "vue";

import AdminLayout from "../../layouts/adminLayout.vue";
import AppButton from "../../components/appButton.vue";
import AppTable from "../../components/appTabel.vue";
import { getUser, deleteUser } from "../../services/userService";
import { useRouter } from "vue-router";
import { EyeIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/outline";

const router = useRouter();
const users = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const perPage = 10;
const totalPages = computed(() =>
    Math.ceil(users.value.length / perPage)
);

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return users.value.slice(start, start + perPage);
});
const showDetail = ref(false);
const selectedUser = ref(null);
const lihatDetail = (user) => {
    selectedUser.value = user;
    showDetail.value = true;
};
const tutupDetail = () => {
    showDetail.value = false;
    selectedUser.value = null;
};

const showDelete = ref(false);
const selectedDeleteUser = ref(null);
const deleting = ref(false);
const konfirmasiHapus = (user) => {
    selectedDeleteUser.value = user;
    showDelete.value = true;
};
const batalHapus = () => {
    if (deleting.value) return;

    showDelete.value = false;
    selectedDeleteUser.value = null;
};
const showNotification = ref(false);
const notification = ref({
    type: "success",
    title: "",
    message: "",
});
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

async function loadUser() {
    loading.value = true;
    try {
        const response = await getUser();
        users.value = response.data.data || [];
        if (
            currentPage.value > totalPages.value &&
            currentPage.value > 1
        ) {
            currentPage.value = totalPages.value;
        }

        return true;
    } catch (error) {
        console.error(error);
        tampilkanNotifikasi(
            "error",
            "Gagal Memuat Data",
            error.response?.data?.message ??
                "Data peserta gagal dimuat."
        );
        return false;
    } finally {
        loading.value = false;
    }
}

async function hapusUser() {
    if (!selectedDeleteUser.value) return;
    deleting.value = true;
    try {
        await deleteUser(selectedDeleteUser.value.id);
        showDelete.value = false;
        selectedDeleteUser.value = null;
        const berhasilMemuat = await loadUser();
        if (berhasilMemuat) {
            tampilkanNotifikasi(
                "success",
                "Berhasil!",
                "Data peserta berhasil dihapus."
            );
        }
    } catch (error) {
        console.error(error);
        showDelete.value = false;
        selectedDeleteUser.value = null;
        tampilkanNotifikasi(
            "error",
            "Gagal Menghapus!",
            error.response?.data?.message ??
                "Gagal menghapus peserta. Silakan coba lagi."
        );
    } finally {
        deleting.value = false;
    }
}

onMounted(() => {
    loadUser();
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Data Peserta
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400">
                        Kelola Data Peserta
                    </p>
                </div>

                <AppButton @click="router.push('/peserta/create')">
                    + Tambah Peserta
                </AppButton>
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
                                Nama
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                NIP
                            </th>
                            <th class="px-4 py-3 text-left font-semibold">
                                Email
                            </th>
                            <th class="px-4 py-3 text-center font-semibold w-64">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td
                                colspan="5"
                                class="py-8 text-center text-slate-500 dark:text-slate-400">
                                Memuat data...
                            </td>
                        </tr>

                        <template v-else-if="users.length > 0">
                            <tr
                                v-for="(user, index) in paginatedUsers"
                                :key="user.id"
                                class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ (currentPage - 1) * perPage + index + 1 }}
                                </td>
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ user.nama }}
                                </td>
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ user.nip }}
                                </td>
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-200">
                                    {{ user.email }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                       <button
                                            type="button"
                                            @click="lihatDetail(user)"
                                            title="Detail User"
                                            aria-label="Detail User"
                                            class="inline-flex h-9 w-9 items-center justify-center
                                                rounded-lg bg-sky-50 text-sky-600
                                                hover:bg-sky-100 hover:text-sky-700
                                                dark:bg-sky-900/20 dark:text-sky-400
                                                dark:hover:bg-sky-900/40
                                                transition-all duration-200"
                                        >
                                            <EyeIcon class="h-5 w-5" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="
                                                router.push({
                                                    name: 'user.edit',
                                                    params: { id: user.id },
                                                })
                                            "
                                            title="Edit User"
                                            aria-label="Edit User"
                                            class="inline-flex h-9 w-9 items-center justify-center
                                                rounded-lg bg-amber-50 text-amber-600
                                                hover:bg-amber-100 hover:text-amber-700
                                                dark:bg-amber-900/20 dark:text-amber-400
                                                dark:hover:bg-amber-900/40
                                                transition-all duration-200"
                                        >
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </button>
                                        <button
                                            type="button"
                                            @click="konfirmasiHapus(user)"
                                            title="Hapus User"
                                            aria-label="Hapus User"
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
                                colspan="5"
                                class="py-10 text-center text-slate-500 dark:text-slate-400">
                                Data peserta belum tersedia.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </AppTable>

            <div v-if="totalPages > 1" class="mt-5 flex items-center justify-between">
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
                v-if="showDetail"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                @click.self="tutupDetail">
                <div
                    class="w-full max-w-lg rounded-xl bg-white dark:bg-slate-800 shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 px-6 py-4">
                        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">
                            Detail Peserta
                        </h2>
                        <button
                            @click="tutupDetail"
                            class="text-slate-500 hover:text-red-500 text-2xl">
                            &times;
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Nama
                            </p>
                            <p class="font-medium text-slate-800 dark:text-white">
                                {{ selectedUser?.nama }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Role
                            </p>
                            <p class="font-medium text-slate-800 dark:text-white">
                                {{ selectedUser?.nama_role }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                NIP
                            </p>
                            <p class="font-medium text-slate-800 dark:text-white">
                                {{ selectedUser?.nip }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Email
                            </p>
                            <p class="font-medium text-slate-800 dark:text-white">
                                {{ selectedUser?.email }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Nomor Telepon
                            </p>
                            <p class="font-medium text-slate-800 dark:text-white">
                                {{ selectedUser?.notlp }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                Alamat
                            </p>
                            <p class="font-medium text-slate-800 dark:text-white">
                                {{ selectedUser?.alamat }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-end border-t border-slate-200 dark:border-slate-700 px-6 py-4">
                        <AppButton
                            color="secondary"
                            @click="tutupDetail">
                            Tutup
                        </AppButton>
                    </div>
                </div>
            </div>

            <div
                v-if="showDelete"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
                @click.self="batalHapus">
                <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-800 shadow-2xl overflow-hidden">
                    <div class="p-6 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
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
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                            Hapus Peserta ?
                        </h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                            Apakah kamu yakin ingin menghapus peserta
                            <span class="font-semibold text-slate-800 dark:text-white">
                                {{ selectedDeleteUser?.nama }}
                            </span>
                            ?
                        </p>
                    </div>
                    <div
                        class="flex justify-center gap-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 px-6 py-4"
                    >
                        <AppButton
                            color="secondary"
                            :disabled="deleting"
                            @click="batalHapus">
                            Batal
                        </AppButton>
                        <AppButton
                            color="danger"
                            :disabled="deleting"
                            @click="hapusUser">
                            {{ deleting ? "Menghapus..." : "Ya, Hapus" }}
                        </AppButton>
                    </div>
                </div>
            </div>

            <div
                v-if="showNotification"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 p-4"
                @click.self="tutupNotifikasi">
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
                                stroke-width="2">
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
                                stroke-width="2">
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
