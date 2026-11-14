<template>
    <span :class="['badge', variantClass]">
        <slot />
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'success', 'warning', 'danger', 'accent'].includes(value),
    },
});

const variantClass = computed(() => {
    const variants = {
        default: '',
        success: 'badge--done',
        warning: 'badge--warning',
        danger: 'badge--danger',
        accent: 'badge--accent',
    };
    return variants[props.variant];
});
</script>

<style scoped>
.badge--warning {
    color: var(--warning);
    border-color: transparent;
    background: rgba(251, 191, 36, 0.14);
}

.badge--danger {
    color: var(--danger);
    border-color: transparent;
    background: var(--danger-soft);
}
</style>
