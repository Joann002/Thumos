<script setup>
import { Link, router } from '@inertiajs/vue3';
import Icon from '../../Components/Icon.vue';
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
        <div class="page-head">
            <div>
                <h1 class="page-title">Journal</h1>
                <p class="page-subtitle">Tes réflexions, jour après jour</p>
            </div>
            <Link href="/journal/create" class="btn"><Icon name="plus" :size="16" /> Nouvelle entrée</Link>
        </div>

        <div v-if="entries.length" class="stack">
            <article v-for="entry in entries" :key="entry.id" class="card stack--sm">
                <div class="row row--between">
                    <strong style="text-transform: capitalize">{{ formatDate(entry.date) }}</strong>
                    <div class="row" style="gap: 0.4rem">
                        <span v-if="entry.mood && moodMap[entry.mood]" class="badge">
                            {{ moodMap[entry.mood].emoji }} {{ moodMap[entry.mood].label }}
                        </span>
                        <Link :href="`/journal/${entry.id}/edit`" class="icon-btn" title="Modifier"><Icon name="edit" :size="16" /></Link>
                        <button class="icon-btn" title="Supprimer" @click="remove(entry)"><Icon name="trash" :size="16" /></button>
                    </div>
                </div>
                <p style="margin: 0; white-space: pre-wrap; color: var(--text-soft)">{{ entry.content }}</p>
            </article>
        </div>

        <div v-else class="empty">
            Aucune entrée pour l'instant.
            <Link href="/journal/create">Écris ta première réflexion</Link>.
        </div>
    </div>
</template>
