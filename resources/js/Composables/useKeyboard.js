import { onMounted, onUnmounted } from 'vue';

export function useKeyboard(keyBindings) {
    const handleKeydown = (event) => {
        const key = event.key.toLowerCase();
        const ctrl = event.ctrlKey || event.metaKey;
        const shift = event.shiftKey;
        const alt = event.altKey;

        Object.entries(keyBindings).forEach(([binding, callback]) => {
            const parts = binding.toLowerCase().split('+');
            const bindingKey = parts[parts.length - 1];
            const needsCtrl = parts.includes('ctrl') || parts.includes('cmd');
            const needsShift = parts.includes('shift');
            const needsAlt = parts.includes('alt');

            if (
                key === bindingKey &&
                ctrl === needsCtrl &&
                shift === needsShift &&
                alt === needsAlt
            ) {
                event.preventDefault();
                callback(event);
            }
        });
    };

    onMounted(() => {
        window.addEventListener('keydown', handleKeydown);
    });

    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeydown);
    });

    return {
        handleKeydown,
    };
}
