<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    days: {
        type: Array,
        required: true,
    },
    habitCount: {
        type: Number,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    prev: {
        type: Object,
        required: true,
    },
    next: {
        type: Object,
        required: true,
    },
});

const weekdays = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
</script>

<template>
    <div>
        <div class="row row--between">
            <h1 class="page-title" style="margin: 0; text-transform: capitalize">{{ label }}</h1>
            <div class="row">
                <Link :href="`/calendar?year=${prev.year}&month=${prev.month}`" class="btn btn--ghost">← Préc.</Link>
                <Link href="/calendar" class="btn btn--ghost">Aujourd'hui</Link>
                <Link :href="`/calendar?year=${next.year}&month=${next.month}`" class="btn btn--ghost">Suiv. →</Link>
            </div>
        </div>

        <p class="muted" style="margin-top: 0.5rem">
            Vert = habitudes faites dans la journée. Encadré violet = échéance d'un objectif.
        </p>

        <div class="calendar" style="margin-top: 1rem">
            <div v-for="weekday in weekdays" :key="weekday" class="calendar__weekday">{{ weekday }}</div>

            <div
                v-for="cell in days"
                :key="cell.date"
                class="calendar__cell"
                :class="{ 'calendar__cell--out': !cell.in_month, 'calendar__cell--today': cell.is_today }"
            >
                <span class="calendar__day">{{ cell.day }}</span>
                <span v-if="cell.completed_count" class="calendar__done">
                    ✓ {{ cell.completed_count }}<span v-if="habitCount">/{{ habitCount }}</span>
                </span>
                <Link
                    v-for="goal in cell.goals"
                    :key="goal.id"
                    :href="`/goals/${goal.id}`"
                    class="calendar__goal"
                    :title="goal.title"
                >
                    {{ goal.title }}
                </Link>
            </div>
        </div>
    </div>
</template>
