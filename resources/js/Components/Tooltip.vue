<template>
    <div class="tooltip-wrapper" @mouseenter="show" @mouseleave="hide">
        <slot />
        <Transition name="tooltip">
            <div v-if="isVisible" :class="['tooltip', positionClass]">
                {{ text }}
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    text: {
        type: String,
        required: true,
    },
    position: {
        type: String,
        default: 'top',
        validator: (value) => ['top', 'bottom', 'left', 'right'].includes(value),
    },
});

const isVisible = ref(false);

const positionClass = computed(() => `tooltip--${props.position}`);

const show = () => {
    isVisible.value = true;
};

const hide = () => {
    isVisible.value = false;
};
</script>

<style scoped>
.tooltip-wrapper {
    position: relative;
    display: inline-block;
}

.tooltip {
    position: absolute;
    background: var(--surface-2);
    color: var(--text);
    padding: 0.5rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    white-space: nowrap;
    z-index: 100;
    box-shadow: var(--shadow);
    pointer-events: none;
}

.tooltip--top {
    bottom: calc(100% + 0.5rem);
    left: 50%;
    transform: translateX(-50%);
}

.tooltip--bottom {
    top: calc(100% + 0.5rem);
    left: 50%;
    transform: translateX(-50%);
}

.tooltip--left {
    right: calc(100% + 0.5rem);
    top: 50%;
    transform: translateY(-50%);
}

.tooltip--right {
    left: calc(100% + 0.5rem);
    top: 50%;
    transform: translateY(-50%);
}

.tooltip-enter-active,
.tooltip-leave-active {
    transition: opacity 0.2s ease;
}

.tooltip-enter-from,
.tooltip-leave-to {
    opacity: 0;
}
</style>
