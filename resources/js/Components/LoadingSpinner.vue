<template>
    <div :class="['spinner', sizeClass]" :style="{ borderTopColor: color }"></div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    color: {
        type: String,
        default: 'var(--primary)',
    },
});

const sizeClass = computed(() => {
    if (props.size === 'sm') return 'spinner--sm';
    if (props.size === 'lg') return 'spinner--lg';
    return '';
});
</script>

<style scoped>
.spinner--sm {
    width: 16px;
    height: 16px;
    border-width: 2px;
}
</style>
