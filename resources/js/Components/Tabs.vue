<template>
    <div class="tabs">
        <div class="tabs__header">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                type="button"
                :class="['tabs__button', { 'tabs__button--active': modelValue === tab.value }]"
                @click="emit('update:modelValue', tab.value)"
            >
                {{ tab.label }}
            </button>
        </div>
        <div class="tabs__content">
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({
    tabs: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue']);
</script>

<style scoped>
.tabs__header {
    display: flex;
    gap: 0.25rem;
    border-bottom: 2px solid var(--border);
    margin-bottom: 1.5rem;
}

.tabs__button {
    padding: 0.75rem 1rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    color: var(--muted);
    font-weight: 500;
    cursor: pointer;
    transition: color 0.15s ease, border-color 0.15s ease;
}

.tabs__button:hover {
    color: var(--text-soft);
}

.tabs__button--active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}
</style>
