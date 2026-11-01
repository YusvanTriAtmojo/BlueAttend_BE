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
    primary: "bg-blue-600 hover:bg-blue-700 text-white",
    success: "bg-green-600 hover:bg-green-700 text-white",
    danger: "bg-red-600 hover:bg-red-700 text-white",
    warning: "bg-yellow-500 hover:bg-yellow-600 text-white",
    secondary: "bg-slate-200 hover:bg-slate-300 text-slate-700",
};

</script>

<template>
    <button
        :type="type"
        :class="['px-4 py-2 rounded-lg transition font-medium', colors[color]]"
    >
        <slot />
    </button>
</template>
