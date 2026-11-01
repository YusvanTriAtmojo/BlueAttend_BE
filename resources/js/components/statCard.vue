<template>
    <div
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-colors duration-300 dark:border-slate-700 dark:bg-slate-800"
    >
        <p
            class="text-slate-500 dark:text-slate-400"
        >
            {{ title }}
        </p>

        <h2
            class="mt-3 text-4xl font-bold dark:text-white"
            :class="colors[color]"
        >
            {{ value }}
        </h2>
    </div>
</template>

<script setup>
const props = defineProps({
    title: String,
    value: [String, Number],
    color: String,
});

const colors = {
    blue: "text-[#6C9BD2]",
    green: "text-green-500",
    yellow: "text-yellow-500",
    red: "text-red-500",
};
</script>