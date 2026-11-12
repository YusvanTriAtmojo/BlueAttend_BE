<script setup>
defineProps({
    title: String,
    value: [String, Number],
    color: {
        type: String,
        default: "blue",
    },
});

const colors = {
    blue: "text-primary",
    green: "text-stat-green",
    yellow: "text-stat-yellow",
    red: "text-stat-red",
};
</script>

<template>
    <div
        class="rounded-2xl border border-border bg-surface p-6 shadow-sm transition-colors duration-300 dark:border-dark-border dark:bg-dark-surface-hover"
    >
        <p class="text-text-secondary dark:text-dark-text-secondary">
            {{ title }}
        </p>

        <h2
            class="mt-3 text-4xl font-bold"
            :class="colors[color] || colors.blue"
        >
            {{ value }}
        </h2>
    </div>
</template>