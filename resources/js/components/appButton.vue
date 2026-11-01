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
    primary: "bg-indigo-600 hover:bg-indigo-700 text-white",
    success: "bg-teal-600 hover:bg-teal-700 text-white",
    danger: "bg-rose-600 hover:bg-rose-700 text-white",
    warning: "bg-amber-500 hover:bg-amber-600 text-white",
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
