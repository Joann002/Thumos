<template>
    <textarea
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="['textarea', { 'textarea--error': error }]"
        v-bind="$attrs"
    />
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    error: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
.textarea--error {
    border-color: var(--danger);
}

.textarea--error:focus {
    box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.2);
}
</style>
