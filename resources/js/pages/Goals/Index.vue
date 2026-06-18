<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '../../Components/Icon.vue';

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

const progress = (goal) => (goal.tasks_count ? Math.round((goal.completed_tasks_count / goal.tasks_count) * 100) : 0);
</script>

<template>
    <div>
        <div class="page-head">
            <div>
                <h1 class="page-title">Objectifs</h1>
                <p class="page-subtitle">Tes buts et leurs sous-tâches</p>
            </div>
            <Link href="/goals/create" class="btn"><Icon name="plus" :size="16" /> Nouvel objectif</Link>
        </div>

        <div v-if="goals.length" class="stack">
            <Link
                v-for="goal in goals"
                :key="goal.id"
                :href="`/goals/${goal.id}`"
                class="card card--link row row--between"
            >
                <div style="min-width: 0; flex: 1">
                    <strong>{{ goal.title }}</strong>
                    <div class="muted" style="font-size: 0.85rem">
                        <span v-if="goal.category">{{ goal.category }} · </span>
                        <span class="tabular">{{ goal.completed_tasks_count }}/{{ goal.tasks_count }}</span> sous-tâches
                    </div>
                    <div v-if="goal.tasks_count" class="progress" style="margin-top: 0.6rem; max-width: 260px">
                        <div class="progress__bar" :style="{ width: progress(goal) + '%' }"></div>
                    </div>
                </div>
                <span class="badge" :class="{ 'badge--done': goal.status === 'done', 'badge--accent': goal.status === 'active' }">
                    {{ statusLabels[goal.status] ?? goal.status }}
                </span>
            </Link>
        </div>

        <div v-else class="empty">
            Aucun objectif pour l'instant.
            <Link href="/goals/create">Crée ton premier objectif</Link>.
        </div>
    </div>
</template>
