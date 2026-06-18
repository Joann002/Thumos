<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Icon from '../../Components/Icon.vue';
import Modal from '../../Components/Modal.vue';

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

// Modal state
const showModal = ref(false);
const modalLoading = ref(false);
const dayDetails = ref(null);

const moodEmojis = {
    excellent: '😄',
    good: '🙂',
    neutral: '😐',
    bad: '😞',
    terrible: '😢',
};

const statusLabels = {
    planned: 'Planifié',
    in_progress: 'En cours',
    completed: 'Terminé',
    cancelled: 'Annulé',
};

const openDayModal = async (date) => {
    showModal.value = true;
    modalLoading.value = true;
    dayDetails.value = null;

    try {
        const response = await fetch(`/calendar/${date}`);
        dayDetails.value = await response.json();
    } catch (error) {
        console.error('Error loading day details:', error);
    } finally {
        modalLoading.value = false;
    }
};

const closeModal = () => {
    showModal.value = false;
    dayDetails.value = null;
};
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
                :class="{ 'calendar__cell--out': !cell.in_month, 'calendar__cell--today': cell.is_today, 'calendar__cell--clickable': cell.in_month }"
                @click="cell.in_month && openDayModal(cell.date)"
            >
                <span class="calendar__day">{{ cell.day }}</span>
                <span v-if="cell.completed_count" class="calendar__done">
                    ✓ {{ cell.completed_count }}<span v-if="habitCount">/{{ habitCount }}</span>
                </span>
                <span
                    v-for="goal in cell.goals"
                    :key="goal.id"
                    class="calendar__goal"
                    :title="goal.title"
                >
                    {{ goal.title }}
                </span>
            </div>
        </div>

        <!-- Modal pour afficher les détails du jour -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <template #header>
                <h2 style="margin: 0; font-size: 1.25rem">{{ dayDetails?.date }}</h2>
            </template>

            <div v-if="modalLoading" class="loading-state">
                <p>Chargement...</p>
            </div>

            <div v-else-if="dayDetails" class="day-details">
                <!-- Habitudes -->
                <section v-if="dayDetails.habits.length" class="detail-section">
                    <h3 class="section-title">
                        <Icon name="habit" :size="18" />
                        Habitudes
                    </h3>
                    <ul class="habits-list">
                        <li
                            v-for="habit in dayDetails.habits"
                            :key="habit.id"
                            class="habit-item"
                            :class="{
                                'habit-item--completed': habit.completed,
                                'habit-item--not-due': !habit.is_due
                            }"
                        >
                            <span class="habit-dot" :style="{ background: habit.color }"></span>
                            <span class="habit-name">{{ habit.name }}</span>
                            <span v-if="!habit.is_due" class="habit-status muted">(pas prévu)</span>
                            <span v-else-if="habit.completed" class="habit-status">✓</span>
                            <span v-else class="habit-status muted">—</span>
                        </li>
                    </ul>
                </section>

                <!-- Objectifs avec échéance -->
                <section v-if="dayDetails.goals.length" class="detail-section">
                    <h3 class="section-title">
                        <Icon name="target" :size="18" />
                        Objectifs (échéance)
                    </h3>
                    <div class="goals-list">
                        <div v-for="goal in dayDetails.goals" :key="goal.id" class="goal-card">
                            <div class="goal-header">
                                <Link :href="`/goals/${goal.id}`" class="goal-title">{{ goal.title }}</Link>
                                <span class="badge" :class="{
                                    'badge--done': goal.status === 'completed',
                                    'badge--accent': goal.status === 'in_progress'
                                }">
                                    {{ statusLabels[goal.status] }}
                                </span>
                            </div>
                            <p v-if="goal.description" class="goal-description">{{ goal.description }}</p>
                            <div v-if="goal.tasks.length" class="goal-tasks">
                                <p class="muted" style="font-size: 0.85rem; margin-bottom: 0.5rem">
                                    {{ goal.tasks.filter(t => t.done).length }}/{{ goal.tasks.length }} sous-tâches
                                </p>
                                <ul class="task-list">
                                    <li v-for="task in goal.tasks" :key="task.id" class="task-item">
                                        <Icon :name="task.done ? 'check' : 'circle'" :size="14" />
                                        <span :style="{ textDecoration: task.done ? 'line-through' : 'none' }">
                                            {{ task.title }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Entrée de journal -->
                <section v-if="dayDetails.journal" class="detail-section">
                    <h3 class="section-title">
                        <Icon name="journal" :size="18" />
                        Journal
                    </h3>
                    <div class="journal-entry">
                        <div v-if="dayDetails.journal.mood" class="journal-mood">
                            {{ moodEmojis[dayDetails.journal.mood] }}
                        </div>
                        <p class="journal-content">{{ dayDetails.journal.content }}</p>
                    </div>
                </section>

                <!-- État vide -->
                <div v-if="!dayDetails.habits.length && !dayDetails.goals.length && !dayDetails.journal" class="empty-state">
                    <p class="muted">Aucune activité enregistrée pour ce jour.</p>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
.calendar__cell--clickable {
    cursor: pointer;
    transition: background-color 0.15s;
}

.calendar__cell--clickable:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.loading-state {
    padding: 2rem;
    text-align: center;
    color: var(--text-soft);
}

.day-details {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.detail-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    color: var(--text);
}

/* Habitudes */
.habits-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.habit-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--bg-soft);
    border-radius: 0.375rem;
}

.habit-item--completed {
    opacity: 0.7;
}

.habit-item--not-due {
    opacity: 0.4;
}

.habit-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.habit-name {
    flex: 1;
    font-weight: 500;
}

.habit-status {
    font-size: 0.9rem;
}

/* Objectifs */
.goals-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.goal-card {
    padding: 1rem;
    background: var(--bg-soft);
    border-radius: 0.5rem;
    border: 1px solid var(--border);
}

.goal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.goal-title {
    font-weight: 600;
    color: var(--text);
    text-decoration: none;
}

.goal-title:hover {
    color: var(--primary);
}

.goal-description {
    margin: 0.5rem 0;
    font-size: 0.9rem;
    color: var(--text-soft);
}

.goal-tasks {
    margin-top: 0.75rem;
}

.task-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-soft);
}

/* Journal */
.journal-entry {
    padding: 1rem;
    background: var(--bg-soft);
    border-radius: 0.5rem;
    border-left: 3px solid var(--primary);
}

.journal-mood {
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
}

.journal-content {
    margin: 0;
    white-space: pre-wrap;
    line-height: 1.6;
}

/* Empty state */
.empty-state {
    padding: 2rem;
    text-align: center;
}
</style>