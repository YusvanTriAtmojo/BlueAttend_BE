
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";
const router = useRouter();
const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");
const login = async () => {
    loading.value = true;
    error.value = "";

    try {
        const response = await api.post("/login", {
            email: email.value,
            password: password.value,
        });
        localStorage.setItem("token", response.data.data.token);
        localStorage.setItem("user", JSON.stringify(response.data.data));
        router.push("/");
    } catch (err) {
        if (err.response) {
            error.value = err.response.data.message;
        } else {
            error.value = "Tidak dapat terhubung ke server.";
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
            <div class="mb-4">
                <label>Email</label>
                <input
                    type="email"
                    v-model="email"
                    class="w-full border rounded-lg px-3 py-2 mt-1"/>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input
                    type="password"
                    v-model="password"
                    class="w-full border rounded-lg px-3 py-2 mt-1"/>
            </div>

            <p v-if="error" class="text-red-500 mb-4">
                {{ error }}
            </p>

            <button
                @click="login"
                :disabled="loading"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg"
            >
                {{ loading ? "Loading..." : "Login" }}
            </button>
        </div>
    </div>
</template>