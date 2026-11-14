import { ref } from 'vue';

export function useAsync(asyncFunction) {
    const data = ref(null);
    const error = ref(null);
    const loading = ref(false);

    const execute = async (...args) => {
        loading.value = true;
        error.value = null;
        data.value = null;

        try {
            const result = await asyncFunction(...args);
            data.value = result;
            return result;
        } catch (err) {
            error.value = err;
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        data,
        error,
        loading,
        execute,
    };
}
