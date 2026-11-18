<script setup>
import { nextTick, onMounted, onUnmounted, ref, useId, watch } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref(null);
const titleId = useId();
let previousFocus = null;
let previousOverflow = null;

const restorePage = () => {
    if (typeof document === 'undefined' || previousOverflow === null) return;
    document.body.style.overflow = previousOverflow;
    previousOverflow = null;
    previousFocus?.focus();
};

watch(() => props.show, async (show) => {
    if (show) {
        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        await nextTick();
        dialog.value?.focus();
    } else {
        restorePage();
    }
}, { flush: 'post' });

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
    if (e.key !== 'Tab' || !props.show || !dialog.value) return;
    const controls = [...dialog.value.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex="0"]')]
        .filter((element) => element.getClientRects().length > 0);
    const first = controls[0];
    const last = controls.at(-1);
    if (!first) {
        e.preventDefault();
        dialog.value.focus();
    } else if (e.shiftKey && (document.activeElement === first || document.activeElement === dialog.value)) {
        e.preventDefault();
        last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    restorePage();
});

const maxWidthClass = {
    'sm': 'max-w-sm',
    'md': 'max-w-md',
    'lg': 'max-w-lg',
    'xl': 'max-w-xl',
    '2xl': 'max-w-2xl',
    '4xl': 'max-w-4xl',
}[props.maxWidth];
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-show="show" class="modal-overlay" @click="close">
                <div
                    ref="dialog"
                    role="dialog"
                    aria-modal="true"
                    :aria-labelledby="$slots.header ? titleId : undefined"
                    :aria-label="$slots.header ? undefined : 'Dialogue'"
                    tabindex="-1"
                    :class="['modal-content', maxWidthClass]"
                    @click.stop
                >
                    <div class="modal-header">
                        <div :id="titleId"><slot name="header" /></div>
                        <button
                            v-if="closeable"
                            type="button"
                            class="modal-close"
                            aria-label="Fermer la fenêtre"
                            @click="close"
                        >
                            <Icon name="close" :size="20" />
                        </button>
                    </div>

                    <div class="modal-body">
                        <slot />
                    </div>

                    <div v-if="$slots.footer" class="modal-footer">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: 1rem;
    overflow-y: auto;
}

.modal-content {
    background: var(--bg);
    border-radius: 0.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid var(--border);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    transition: all 0.15s;
}

.modal-close:hover {
    background-color: var(--bg-soft);
    color: var(--text);
}

.max-w-sm { max-width: 24rem; }
.max-w-md { max-width: 28rem; }
.max-w-lg { max-width: 32rem; }
.max-w-xl { max-width: 36rem; }
.max-w-2xl { max-width: 42rem; }
.max-w-4xl { max-width: 56rem; }

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
    transition: transform 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
    transform: scale(0.95);
}

@media (prefers-reduced-motion: reduce) {
    .modal-enter-active, .modal-leave-active,
    .modal-enter-active .modal-content, .modal-leave-active .modal-content {
        transition: none;
    }
}
</style>
