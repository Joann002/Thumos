<script setup>
import { Link, router } from '@inertiajs/vue3';

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
    router.post(
        `/habits/${habit.id}/toggle`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => (habit.done_today = !habit.done_today),
        },
    );
};

const remove = (habit) => {
    if (confirm('Supprimer cette habitude ?')) {
        router.delete(`/habits/${habit.id}`);
    }
};
</script>

<template>
    <div>
        <div class="row row--between">
            <h1 class="page-title">Habitudes</h1>
            <Link href="/habits/create" class="btn">Nouvelle habitude</Link>
        </div>

        <p class="muted" style="margin-top: -0.75rem; text-transform: capitalize">Check du jour — {{ todayLabel }}</p>

        <div v-if="habits.length" class="stack">
            <div v-for="habit in habits" :key="habit.id" class="card row row--between">
                <label class="row" style="cursor: pointer; gap: 0.75rem">
                    <input type="checkbox" :checked="habit.done_today" @change="toggle(habit)" />
                    <span
                        :style="{
                            width: '12px',
                            height: '12px',
                            borderRadius: '50%',
                            background: habit.color || '#4f46e5',
                            display: 'inline-block',
                        }"
                    ></span>
                    <span>
                        <strong :style="{ textDecoration: habit.done_today ? 'line-through' : 'none' }">
                            {{ habit.name }}
                        </strong>
                        <span class="muted"> · {{ frequencyLabels[habit.frequency] }}</span>
                        <br />
                        <span class="muted" style="font-size: 0.85rem">
                            Série : <strong>{{ habit.current_streak }}</strong> j
                            <span v-if="habit.longest_streak"> · record {{ habit.longest_streak }} j</span>
                        </span>
                    </span>
                </label>
                <div class="row">
                    <Link :href="`/habits/${habit.id}/edit`" class="btn btn--ghost">Modifier</Link>
                    <button class="btn btn--danger" @click="remove(habit)">Suppr.</button>
                </div>
            </div>
        </div>

        <div v-else class="card empty">Aucune habitude pour l'instant. Crée ta première habitude.</div>
    </div>
</template>
