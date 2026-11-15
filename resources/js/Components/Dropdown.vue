<template>
    <div class="dropdown" ref="dropdownRef">
        <button 
            type="button"
            class="dropdown__trigger"
            @click="toggle"
            :aria-expanded="isOpen"
        >
            <slot name="trigger">
                <span>Options</span>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </slot>
        </button>

        <Transition name="dropdown">
            <div v-if="isOpen" class="dropdown__menu" :class="alignmentClass">
                <slot />
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'left',
        validator: (value) => ['left', 'right'].includes(value),
    },
});

const isOpen = ref(false);
const dropdownRef = ref(null);

const alignmentClass = computed(() => `dropdown__menu--${props.align}`);

const toggle = () => {
    isOpen.value = !isOpen.value;
};

const close = () => {
    isOpen.value = false;
};

const handleClickOutside = (event) => {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        close();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

defineExpose({ close });
</script>

<style scoped>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown__trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.5rem 0.75rem;
    color: var(--text);
    cursor: pointer;
    transition: background 0.15s ease;
}

.dropdown__trigger:hover {
    background: var(--surface-hover);
}

.dropdown__menu {
    position: absolute;
    top: calc(100% + 0.5rem);
    min-width: 200px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 0.5rem;
    z-index: 50;
}

.dropdown__menu--left {
    left: 0;
}

.dropdown__menu--right {
    right: 0;
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
