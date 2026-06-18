import { ref } from 'vue';

const STORAGE_KEY = 'thumos-theme';
const theme = ref(resolveInitial());

function resolveInitial() {
    if (typeof window === 'undefined') return 'dark';
    return document.documentElement.getAttribute('data-theme') || 'dark';
}

function apply(value) {
    theme.value = value;
    document.documentElement.setAttribute('data-theme', value);
    try {
        localStorage.setItem(STORAGE_KEY, value);
    } catch {
        /* ignore storage errors */
    }
}

export function useTheme() {
    const toggle = () => apply(theme.value === 'dark' ? 'light' : 'dark');

    return { theme, toggle };
}
