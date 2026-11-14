<template>
    <component 
        :is="componentType" 
        :href="href"
        :class="['card', { 'card--link': isLink, 'card--compact': compact }]"
    >
        <div v-if="$slots.header" class="card__header">
            <slot name="header" />
        </div>
        <div class="card__body">
            <slot />
        </div>
        <div v-if="$slots.footer" class="card__footer">
            <slot name="footer" />
        </div>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: null,
    },
    compact: {
        type: Boolean,
        default: false,
    },
});

const isLink = computed(() => !!props.href);
const componentType = computed(() => props.href ? Link : 'div');
</script>

<style scoped>
.card--compact {
    padding: 0.875rem;
}

.card__header {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.card__body {
    flex: 1;
}

.card__footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
</style>
