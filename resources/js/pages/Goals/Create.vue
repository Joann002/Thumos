<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GoalFields from '../../Components/GoalFields.vue';

const form = useForm({
    title: '',
    description: '',
    category: '',
    target_date: '',
    status: 'active',
});

const submit = () => form.post('/goals');
</script>

<template>
    <div>
        <h1 class="page-title">Nouvel objectif</h1>

        <form class="card" @submit.prevent="submit">
            <GoalFields :form="form" />
            <div class="row" style="margin-top: 1.25rem">
                <button type="submit" class="btn" :disabled="form.processing">Créer</button>
                <Link href="/goals" class="btn btn--ghost">Annuler</Link>
            </div>
        </form>
    </div>
</template>
