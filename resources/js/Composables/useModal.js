import { ref } from 'vue';

export function useModal() {
    const isOpen = ref(false);

    const open = () => {
        isOpen.value = true;
        document.body.style.overflow = 'hidden';
    };

    const close = () => {
        isOpen.value = false;
        document.body.style.overflow = '';
    };

    const toggle = () => {
        if (isOpen.value) {
            close();
        } else {
            open();
        }
    };

    return {
        isOpen,
        open,
        close,
        toggle,
    };
}
