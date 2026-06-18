<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import HabitFields from '../../Components/HabitFields.vue';

const form = useForm({
    name: '',
    frequency: 'daily',
    days_of_week: [],
    color: '#4f46e5',
});

const submit = () => form.post('/habits');
</script>

<template>
    <div>
        <h1 class="page-title">Nouvelle habitude</h1>

        <form class="card" @submit.prevent="submit">
            <HabitFields :form="form" />
            <div class="row" style="margin-top: 1.25rem">
                <button type="submit" class="btn" :disabled="form.processing">Créer</button>
                <Link href="/habits" class="btn btn--ghost">Annuler</Link>
            </div>
        </form>
    </div>
</template>
