<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import JournalFields from '../../Components/JournalFields.vue';

const props = defineProps({
    entry: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    date: props.entry.date ? props.entry.date.substring(0, 10) : '',
    content: props.entry.content,
    mood: props.entry.mood ?? null,
});

const submit = () => form.put(`/journal/${props.entry.id}`);
</script>

<template>
    <div>
        <h1 class="page-title">Modifier l'entrée</h1>

        <form class="card" @submit.prevent="submit">
            <JournalFields :form="form" />
            <div class="row" style="margin-top: 1.25rem">
                <button type="submit" class="btn" :disabled="form.processing">Enregistrer</button>
                <Link href="/journal" class="btn btn--ghost">Annuler</Link>
            </div>
        </form>
    </div>
</template>
