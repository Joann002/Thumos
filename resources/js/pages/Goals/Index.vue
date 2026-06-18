<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    goals: {
        type: Array,
        required: true,
    },
});

const statusLabels = {
    active: 'En cours',
    paused: 'En pause',
    done: 'Terminé',
};
</script>

<template>
    <div>
        <div class="row row--between">
            <h1 class="page-title">Objectifs</h1>
            <Link href="/goals/create" class="btn">Nouvel objectif</Link>
        </div>

        <div v-if="goals.length" class="stack">
            <Link
                v-for="goal in goals"
                :key="goal.id"
                :href="`/goals/${goal.id}`"
                class="card row row--between"
                style="text-decoration: none; color: inherit"
            >
                <div>
                    <strong>{{ goal.title }}</strong>
                    <div class="muted" style="font-size: 0.85rem">
                        <span v-if="goal.category">{{ goal.category }} · </span>
                        {{ goal.completed_tasks_count }}/{{ goal.tasks_count }} sous-tâches
                    </div>
                </div>
                <span class="badge" :class="{ 'badge--done': goal.status === 'done' }">
                    {{ statusLabels[goal.status] ?? goal.status }}
                </span>
            </Link>
        </div>

        <div v-else class="card empty">
            Aucun objectif pour l'instant. Crée ton premier objectif.
        </div>
    </div>
</template>
