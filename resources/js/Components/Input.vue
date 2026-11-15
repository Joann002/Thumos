<template>
    <input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="['input', { 'input--error': error }]"
        v-bind="$attrs"
    />
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number, null],
        default: null,
    },
    error: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
.input--error {
    border-color: var(--danger);
}

.input--error:focus {
    box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.2);
}
</style>
