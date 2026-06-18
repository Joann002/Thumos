<script setup>
import { Link, router } from '@inertiajs/vue3';
import Icon from '../../Components/Icon.vue';

const props = defineProps({
    habits: {
        type: Array,
        required: true,
    },
    today: {
        type: String,
        required: true,
    },
});

const todayLabel = new Date(props.today).toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
});

const frequencyLabels = {
    daily: 'Quotidienne',
    weekly: 'Hebdomadaire',
};

const toggle = (habit) => {
    router.post(`/habits/${habit.id}/toggle`, {}, {
        preserveScroll: true,
        onSuccess: () => (habit.done_today = !habit.done_today),
    });
};

const remove = (habit) => {
    if (confirm('Supprimer cette habitude ?')) {
        router.delete(`/habits/${habit.id}`);
    }
};
</script>

<template>
    <div>
        <div class="page-head">
            <div>
                <h1 class="page-title">Habitudes</h1>
                <p class="page-subtitle" style="text-transform: capitalize">Check du jour — {{ todayLabel }}</p>
            </div>
            <Link href="/habits/create" class="btn"><Icon name="plus" :size="16" /> Nouvelle habitude</Link>
        </div>

        <div v-if="habits.length" class="stack">
            <div v-for="habit in habits" :key="habit.id" class="card row row--between">
                <label class="row" style="cursor: pointer; gap: 0.75rem; min-width: 0; flex: 1">
                    <input type="checkbox" :checked="habit.done_today" @change="toggle(habit)" />
                    <span class="dot" :style="{ background: habit.color || 'var(--primary)' }"></span>
                    <span style="min-width: 0">
                        <strong :style="{ textDecoration: habit.done_today ? 'line-through' : 'none' }">{{ habit.name }}</strong>
                        <span class="muted"> · {{ frequencyLabels[habit.frequency] }}</span>
                        <span class="row" style="gap: 0.35rem; font-size: 0.82rem; color: var(--muted); margin-top: 0.15rem">
                            <Icon name="flame" :size="14" />
                            <span class="tabular">Série {{ habit.current_streak }} j</span>
                            <span v-if="habit.longest_streak" class="tabular"> · record {{ habit.longest_streak }} j</span>
                        </span>
                    </span>
                </label>
                <div class="row" style="gap: 0.4rem">
                    <Link :href="`/habits/${habit.id}/edit`" class="icon-btn" title="Modifier"><Icon name="edit" :size="16" /></Link>
                    <button class="icon-btn" title="Supprimer" @click="remove(habit)"><Icon name="trash" :size="16" /></button>
                </div>
            </div>
        </div>

        <div v-else class="empty">
            Aucune habitude pour l'instant.
            <Link href="/habits/create">Crée ta première habitude</Link>.
        </div>
    </div>
</template>
