<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GoalFields from '../../Components/GoalFields.vue';

const props = defineProps({
    goal: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.goal.title,
    description: props.goal.description ?? '',
    category: props.goal.category ?? '',
    target_date: props.goal.target_date ? props.goal.target_date.substring(0, 10) : '',
    status: props.goal.status,
});

const submit = () => form.put(`/goals/${props.goal.id}`);
</script>

<template>
    <div>
        <h1 class="page-title">Modifier l'objectif</h1>

        <form class="card" @submit.prevent="submit">
            <GoalFields :form="form" />
            <div class="row" style="margin-top: 1.25rem">
                <button type="submit" class="btn" :disabled="form.processing">Enregistrer</button>
                <Link :href="`/goals/${goal.id}`" class="btn btn--ghost">Annuler</Link>
            </div>
        </form>
    </div>
</template>
