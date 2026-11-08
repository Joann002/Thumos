<template>
    <div class="empty">
        <div v-if="icon" class="empty__icon" v-html="icon"></div>
        <h3 class="empty__title">{{ title }}</h3>
        <p v-if="description" class="empty__description">{{ description }}</p>
        <slot name="action"></slot>
    </div>
</template>

<script setup>
defineProps({
    icon: {
        type: String,
        default: null,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: null,
    },
});
</script>

<style scoped>
.empty__icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty__title {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text);
}

.empty__description {
    margin: 0 0 1rem;
    color: var(--muted);
    font-size: 0.9rem;
}
</style>
