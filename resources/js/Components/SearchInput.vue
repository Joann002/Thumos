<template>
    <div class="search-input">
        <div class="search-input__wrapper">
            <svg class="search-input__icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input
                v-model="searchQuery"
                type="text"
                class="search-input__field"
                :placeholder="placeholder"
                @input="handleInput"
            />
            <button
                v-if="searchQuery"
                type="button"
                class="search-input__clear"
                @click="clear"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M12 4L4 12M4 4l8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { debounce } from '../Composables/useDebounce';

const props = defineProps({
    placeholder: {
        type: String,
        default: 'Rechercher...',
    },
    delay: {
        type: Number,
        default: 300,
    },
});

const emit = defineEmits(['search', 'clear']);

const searchQuery = ref('');

const handleInput = debounce(() => {
    emit('search', searchQuery.value);
}, props.delay);

const clear = () => {
    searchQuery.value = '';
    emit('clear');
};
</script>

<style scoped>
.search-input__wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input__icon {
    position: absolute;
    left: 0.75rem;
    color: var(--muted);
}

.search-input__field {
    width: 100%;
    padding: 0.65rem 2.5rem 0.65rem 2.75rem;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface-2);
    color: var(--text);
    font-family: inherit;
    transition: border-color 0.15s ease;
}

.search-input__field:focus {
    outline: none;
    border-color: var(--primary);
}

.search-input__clear {
    position: absolute;
    right: 0.5rem;
    display: grid;
    place-items: center;
    width: 24px;
    height: 24px;
    border: none;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
    border-radius: 4px;
    transition: background 0.15s ease, color 0.15s ease;
}

.search-input__clear:hover {
    background: var(--surface-hover);
    color: var(--text);
}
</style>
