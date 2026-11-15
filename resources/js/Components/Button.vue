<template>
    <component
        :is="componentType"
        :type="type"
        :href="href"
        :class="['btn', variantClass, sizeClass, { 'btn--loading': loading }]"
        :disabled="disabled || loading"
        v-bind="$attrs"
    >
        <span v-if="loading" class="btn__spinner"></span>
        <span :class="{ 'btn__content--hidden': loading }">
            <slot />
        </span>
    </component>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'ghost', 'danger'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    type: {
        type: String,
        default: 'button',
    },
    href: {
        type: String,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const componentType = computed(() => {
    if (props.href) return Link;
    return 'button';
});

const variantClass = computed(() => {
    const variants = {
        primary: '',
        ghost: 'btn--ghost',
        danger: 'btn--danger',
    };
    return variants[props.variant];
});

const sizeClass = computed(() => {
    const sizes = {
        sm: 'btn--sm',
        md: '',
        lg: 'btn--lg',
    };
    return sizes[props.size];
});
</script>

<style scoped>
.btn--lg {
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
}

.btn--loading {
    position: relative;
    pointer-events: none;
}

.btn__spinner {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

.btn__content--hidden {
    visibility: hidden;
}

@keyframes spin {
    to {
        transform: translate(-50%, -50%) rotate(360deg);
    }
}
</style>
