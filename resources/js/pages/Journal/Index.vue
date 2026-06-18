<script setup>
import { Link, router } from '@inertiajs/vue3';
import { moodMap } from '../../Components/moods';

defineProps({
    entries: {
        type: Array,
        required: true,
    },
});

const formatDate = (date) =>
    new Date(date).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

const remove = (entry) => {
    if (confirm('Supprimer cette entrée ?')) {
        router.delete(`/journal/${entry.id}`);
    }
};
</script>

<template>
    <div>
        <div class="row row--between">
            <h1 class="page-title">Journal</h1>
            <Link href="/journal/create" class="btn">Nouvelle entrée</Link>
        </div>

        <div v-if="entries.length" class="stack">
            <article v-for="entry in entries" :key="entry.id" class="card stack">
                <div class="row row--between">
                    <strong style="text-transform: capitalize">{{ formatDate(entry.date) }}</strong>
                    <div class="row">
                        <span v-if="entry.mood && moodMap[entry.mood]" class="badge">
                            {{ moodMap[entry.mood].emoji }} {{ moodMap[entry.mood].label }}
                        </span>
                        <Link :href="`/journal/${entry.id}/edit`" class="btn btn--ghost">Modifier</Link>
                        <button class="btn btn--danger" @click="remove(entry)">Suppr.</button>
                    </div>
                </div>
                <p style="margin: 0; white-space: pre-wrap">{{ entry.content }}</p>
            </article>
        </div>

        <div v-else class="card empty">Aucune entrée pour l'instant. Écris ta première réflexion.</div>
    </div>
</template>
