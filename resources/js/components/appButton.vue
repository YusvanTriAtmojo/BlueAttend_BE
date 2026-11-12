<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },

    color: {
        type: String,
        default: "primary",
    },
});

const colors = {
    primary: "bg-primary hover:bg-primary-hover text-white",
    success: "bg-success hover:bg-success-hover text-white",
    danger: "bg-danger hover:bg-danger-hover text-white",
    warning: "bg-warning hover:bg-warning-hover text-white",
    secondary: "bg-secondary hover:bg-secondary-hover text-secondary-text",
};
</script>

<template>
    <button
        :type="type"
        :class="[
            'px-4 py-2 rounded-lg transition font-medium',
            colors[color],
        ]"
    >
        <slot />
    </button>
</template>