import { ref } from 'vue';

const isOpen = ref(false);
const config = ref({});
let resolvePromise = null;

export function useConfirm() {
    const confirm = (options = {}) => {
        config.value = {
            title: options.title || 'Confirmation',
            message: options.message || 'Êtes-vous sûr ?',
            confirmText: options.confirmText || 'Confirmer',
            dangerMode: options.dangerMode || false,
        };

        isOpen.value = true;

        return new Promise((resolve) => {
            resolvePromise = resolve;
        });
    };

    const handleConfirm = () => {
        isOpen.value = false;
        if (resolvePromise) {
            resolvePromise(true);
            resolvePromise = null;
        }
    };

    const handleCancel = () => {
        isOpen.value = false;
        if (resolvePromise) {
            resolvePromise(false);
            resolvePromise = null;
        }
    };

    return {
        isOpen,
        config,
        confirm,
        handleConfirm,
        handleCancel,
    };
}
