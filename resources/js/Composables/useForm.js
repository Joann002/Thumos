import { reactive, computed } from 'vue';

export function useForm(initialData = {}) {
    const form = reactive({
        data: { ...initialData },
        errors: {},
        processing: false,
        isDirty: false,
    });

    const hasErrors = computed(() => Object.keys(form.errors).length > 0);

    const setError = (field, message) => {
        form.errors[field] = message;
    };

    const clearError = (field) => {
        delete form.errors[field];
    };

    const clearErrors = () => {
        form.errors = {};
    };

    const reset = () => {
        form.data = { ...initialData };
        form.errors = {};
        form.isDirty = false;
    };

    const setData = (field, value) => {
        form.data[field] = value;
        form.isDirty = true;
        clearError(field);
    };

    return {
        form,
        hasErrors,
        setError,
        clearError,
        clearErrors,
        reset,
        setData,
    };
}
