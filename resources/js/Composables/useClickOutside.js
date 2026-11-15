import { onMounted, onUnmounted } from 'vue';

export function useClickOutside(elementRef, callback) {
    const handleClickOutside = (event) => {
        if (elementRef.value && !elementRef.value.contains(event.target)) {
            callback(event);
        }
    };

    onMounted(() => {
        document.addEventListener('mousedown', handleClickOutside);
    });

    onUnmounted(() => {
        document.removeEventListener('mousedown', handleClickOutside);
    });

    return {
        handleClickOutside,
    };
}
