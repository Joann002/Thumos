<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import HabitFields from '../../Components/HabitFields.vue';

const props = defineProps({
    habit: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.habit.name,
    frequency: props.habit.frequency,
    days_of_week: props.habit.days_of_week ?? [],
    color: props.habit.color ?? '#4f46e5',
});

const submit = () => form.put(`/habits/${props.habit.id}`);
</script>

<template>
    <div>
        <h1 class="page-title">Modifier l'habitude</h1>

        <form class="card" @submit.prevent="submit">
            <HabitFields :form="form" />
            <div class="row" style="margin-top: 1.25rem">
                <button type="submit" class="btn" :disabled="form.processing">Enregistrer</button>
                <Link href="/habits" class="btn btn--ghost">Annuler</Link>
            </div>
        </form>
    </div>
</template>
