<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '../../Components/Icon.vue';

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
        <div class="page-head">
            <h1 class="page-title" style="text-transform: capitalize">{{ label }}</h1>
            <div class="row" style="gap: 0.4rem">
                <Link :href="`/calendar?year=${prev.year}&month=${prev.month}`" class="icon-btn" title="Mois précédent"><Icon name="chevronLeft" :size="18" /></Link>
                <Link href="/calendar" class="btn btn--ghost btn--sm">Aujourd'hui</Link>
                <Link :href="`/calendar?year=${next.year}&month=${next.month}`" class="icon-btn" title="Mois suivant"><Icon name="chevronRight" :size="18" /></Link>
            </div>
        </div>

        <p class="muted" style="margin-top: -0.75rem; font-size: 0.85rem">
            « ✓ » = habitudes faites ce jour-là · pastille violette = échéance d'un objectif.
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
