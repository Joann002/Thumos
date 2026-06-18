<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
    goal: {
        type: Object,
        required: true,
    },
});

const statusLabels = {
    active: 'En cours',
    paused: 'En pause',
    done: 'Terminé',
};

const tasks = ref([...props.goal.tasks]);

const progress = computed(() => {
    if (!tasks.value.length) return 0;
    const done = tasks.value.filter((t) => t.done).length;
    return Math.round((done / tasks.value.length) * 100);
});

const newTask = useForm({ title: '' });

const addTask = () => {
    newTask.post(`/goals/${props.goal.id}/tasks`, {
        preserveScroll: true,
        onSuccess: () => newTask.reset('title'),
    });
};

const toggleTask = (task) => {
    router.patch(
        `/tasks/${task.id}`,
        { done: !task.done },
        { preserveScroll: true, onSuccess: () => (task.done = !task.done) },
    );
};

const deleteTask = (task) => {
    router.delete(`/tasks/${task.id}`, {
        preserveScroll: true,
        onSuccess: () => (tasks.value = tasks.value.filter((t) => t.id !== task.id)),
    });
};

const persistOrder = () => {
    router.post(
        `/goals/${props.goal.id}/tasks/reorder`,
        { ids: tasks.value.map((t) => t.id) },
        { preserveScroll: true },
    );
};

const deleteGoal = () => {
    if (confirm('Supprimer cet objectif ?')) {
        router.delete(`/goals/${props.goal.id}`);
    }
};
</script>

<template>
    <div class="stack">
        <div class="row row--between">
            <h1 class="page-title" style="margin: 0">{{ goal.title }}</h1>
            <div class="row">
                <Link :href="`/goals/${goal.id}/edit`" class="btn btn--ghost">Modifier</Link>
                <button class="btn btn--danger" @click="deleteGoal">Supprimer</button>
            </div>
        </div>

        <div class="card stack">
            <div class="row">
                <span class="badge" :class="{ 'badge--done': goal.status === 'done' }">
                    {{ statusLabels[goal.status] ?? goal.status }}
                </span>
                <span v-if="goal.category" class="badge">{{ goal.category }}</span>
                <span v-if="goal.target_date" class="muted">
                    Échéance : {{ new Date(goal.target_date).toLocaleDateString('fr-FR') }}
                </span>
            </div>
            <p v-if="goal.description" style="margin: 0; white-space: pre-wrap">{{ goal.description }}</p>
        </div>

        <div class="card stack">
            <div class="row row--between">
                <strong>Sous-tâches</strong>
                <span class="muted">{{ progress }}%</span>
            </div>

            <draggable
                v-if="tasks.length"
                v-model="tasks"
                item-key="id"
                handle=".drag-handle"
                class="checklist"
                @end="persistOrder"
            >
                <template #item="{ element: task }">
                    <li class="checklist__item" :class="{ 'checklist__item--done': task.done }">
                        <span class="drag-handle muted" style="cursor: grab">⠿</span>
                        <input type="checkbox" :checked="task.done" @change="toggleTask(task)" />
                        <span style="flex: 1">{{ task.title }}</span>
                        <button class="btn btn--danger" @click="deleteTask(task)">Suppr.</button>
                    </li>
                </template>
            </draggable>

            <p v-else class="muted" style="margin: 0">Aucune sous-tâche.</p>

            <form class="row" @submit.prevent="addTask">
                <input
                    v-model="newTask.title"
                    type="text"
                    class="input"
                    placeholder="Ajouter une sous-tâche…"
                />
                <button type="submit" class="btn" :disabled="newTask.processing">Ajouter</button>
            </form>
            <span v-if="newTask.errors.title" class="error">{{ newTask.errors.title }}</span>
        </div>

        <div>
            <Link href="/goals" class="muted">← Retour aux objectifs</Link>
        </div>
    </div>
</template>
