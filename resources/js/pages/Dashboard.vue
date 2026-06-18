<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '../Components/Icon.vue';

const props = defineProps({
    today: { type: String, required: true },
    stats: { type: Object, required: true },
    todayHabits: { type: Array, required: true },
    upcomingGoals: { type: Array, required: true },
});

const page = usePage();
const firstName = computed(() => (page.props.auth?.user?.name ?? '').split(' ')[0]);

const todayLabel = new Date(props.today).toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
});

const statCards = computed(() => [
    { icon: 'check', label: "Habitudes du jour", value: `${props.stats.done_today}/${props.stats.due_today}` },
    { icon: 'flame', label: 'Meilleure série', value: `${props.stats.best_streak} j` },
    { icon: 'target', label: 'Objectifs actifs', value: props.stats.active_goals },
    { icon: 'journal', label: 'Entrées de journal', value: props.stats.journal_entries },
]);

const toggle = (habit) => {
    router.post(`/habits/${habit.id}/toggle`, {}, {
        preserveScroll: true,
        onSuccess: () => (habit.done_today = !habit.done_today),
    });
};

const formatDeadline = (date) =>
    new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
</script>

<template>
    <div class="stack">
        <div class="page-head">
            <div>
                <h1 class="page-title">Bonjour{{ firstName ? ' ' + firstName : '' }}</h1>
                <p class="page-subtitle" style="text-transform: capitalize">{{ todayLabel }}</p>
            </div>
            <Link href="/journal/create" class="btn"><Icon name="plus" :size="16" /> Note du jour</Link>
        </div>

        <!-- Stats -->
        <div class="grid-stats">
            <div v-for="card in statCards" :key="card.label" class="stat">
                <span class="stat__icon"><Icon :name="card.icon" :size="18" /></span>
                <span class="stat__value tabular">{{ card.value }}</span>
                <span class="stat__label">{{ card.label }}</span>
            </div>
        </div>

        <!-- Today's completion bar -->
        <div class="card stack--sm" v-if="stats.due_today">
            <div class="row row--between">
                <strong>Progression du jour</strong>
                <span class="muted tabular">{{ stats.completion }}%</span>
            </div>
            <div class="progress"><div class="progress__bar" :style="{ width: stats.completion + '%' }"></div></div>
        </div>

        <div class="grid-2">
            <!-- Today's habits -->
            <div class="card stack--sm">
                <div class="row row--between">
                    <strong>Habitudes du jour</strong>
                    <Link href="/habits" class="muted" style="font-size: 0.85rem">Tout voir</Link>
                </div>

                <ul v-if="todayHabits.length" class="checklist">
                    <li
                        v-for="habit in todayHabits"
                        :key="habit.id"
                        class="checklist__item"
                        :class="{ 'checklist__item--done': habit.done_today }"
                    >
                        <input type="checkbox" :checked="habit.done_today" @change="toggle(habit)" />
                        <span class="dot" :style="{ background: habit.color || 'var(--primary)' }"></span>
                        <span class="checklist__title" style="flex: 1">{{ habit.name }}</span>
                    </li>
                </ul>
                <p v-else class="empty" style="padding: 1.5rem">
                    Aucune habitude prévue aujourd'hui.
                    <Link href="/habits/create">En créer une</Link>
                </p>
            </div>

            <!-- Upcoming deadlines -->
            <div class="card stack--sm">
                <div class="row row--between">
                    <strong>Prochaines échéances</strong>
                    <Link href="/goals" class="muted" style="font-size: 0.85rem">Tout voir</Link>
                </div>

                <div v-if="upcomingGoals.length" class="stack--sm">
                    <Link
                        v-for="goal in upcomingGoals"
                        :key="goal.id"
                        :href="`/goals/${goal.id}`"
                        class="checklist__item"
                        style="text-decoration: none; color: inherit"
                    >
                        <Icon name="target" :size="16" />
                        <span style="flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap">{{ goal.title }}</span>
                        <span class="badge">{{ formatDeadline(goal.target_date) }}</span>
                    </Link>
                </div>
                <p v-else class="empty" style="padding: 1.5rem">Aucune échéance à venir.</p>
            </div>
        </div>
    </div>
</template>
