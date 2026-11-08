<template>
    <Modal :show="show" @close="cancel" max-width="sm">
        <template #title>{{ title }}</template>
        <template #body>
            <p>{{ message }}</p>
        </template>
        <template #footer>
            <button type="button" class="btn btn--ghost" @click="cancel">
                Annuler
            </button>
            <button 
                type="button" 
                :class="['btn', dangerMode ? 'btn--danger' : '']" 
                @click="confirm"
            >
                {{ confirmText }}
            </button>
        </template>
    </Modal>
</template>

<script setup>
import Modal from './Modal.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirmation',
    },
    message: {
        type: String,
        required: true,
    },
    confirmText: {
        type: String,
        default: 'Confirmer',
    },
    dangerMode: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['confirm', 'cancel']);

const confirm = () => {
    emit('confirm');
};

const cancel = () => {
    emit('cancel');
};
</script>
