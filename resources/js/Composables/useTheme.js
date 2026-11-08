import { ref, watch, onMounted } from 'vue';

const theme = ref('dark');

export function useTheme() {
    const setTheme = (newTheme) => {
        theme.value = newTheme;
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    };

    const toggleTheme = () => {
        const newTheme = theme.value === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    };

    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
        setTheme(initialTheme);
    };

    onMounted(() => {
        initTheme();
    });

    return {
        theme,
        setTheme,
        toggleTheme,
        initTheme,
    };
}
