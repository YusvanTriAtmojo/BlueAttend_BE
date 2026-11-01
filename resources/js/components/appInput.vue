<script setup>
defineProps({
    modelValue: String,
    placeholder: String,
    type: {
        type: String,
        default: "text",
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <input
        :type="type"
        :placeholder="placeholder"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white"
    />
</template>
