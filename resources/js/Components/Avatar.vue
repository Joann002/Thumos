<template>
    <div :class="['avatar', sizeClass]" :style="{ backgroundColor: color }">
        <img v-if="src" :src="src" :alt="name" class="avatar__image" />
        <span v-else class="avatar__initials">{{ initials }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    src: {
        type: String,
        default: null,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value),
    },
    color: {
        type: String,
        default: 'var(--primary-soft)',
    },
});

const initials = computed(() => {
    const words = props.name.trim().split(' ');
    if (words.length >= 2) {
        return (words[0][0] + words[1][0]).toUpperCase();
    }
    return props.name.substring(0, 2).toUpperCase();
});

const sizeClass = computed(() => `avatar--${props.size}`);
</script>

<style scoped>
.avatar {
    display: grid;
    place-items: center;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar--sm {
    width: 32px;
    height: 32px;
    font-size: 0.75rem;
}

.avatar--md {
    width: 40px;
    height: 40px;
    font-size: 0.875rem;
}

.avatar--lg {
    width: 56px;
    height: 56px;
    font-size: 1.125rem;
}

.avatar--xl {
    width: 80px;
    height: 80px;
    font-size: 1.5rem;
}

.avatar__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar__initials {
    color: var(--primary);
    font-weight: 700;
}
</style>
