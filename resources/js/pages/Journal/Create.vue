<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import JournalFields from '../../Components/JournalFields.vue';

const props = defineProps({
    today: {
        type: String,
        required: true,
    },
});

const form = useForm({
    date: props.today,
    content: '',
    mood: null,
});

const submit = () => form.post('/journal');
</script>

<template>
    <div>
        <h1 class="page-title">Nouvelle entrée</h1>

        <form class="card" @submit.prevent="submit">
            <JournalFields :form="form" />
            <div class="row" style="margin-top: 1.25rem">
                <button type="submit" class="btn" :disabled="form.processing">Enregistrer</button>
                <Link href="/journal" class="btn btn--ghost">Annuler</Link>
            </div>
        </form>
    </div>
</template>
