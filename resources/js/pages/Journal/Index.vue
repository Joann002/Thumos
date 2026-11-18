<script setup>
import { Link, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import Icon from '../../Components/Icon.vue';
import { moodMap, moods } from '../../Components/moods';
import { useDateFormatter } from '../../Composables/useDateFormatter';

const props = defineProps({ entries: { type: Object, required: true }, filters: { type: Object, required: true } });
const filters = reactive({ q: props.filters.q ?? '', mood: props.filters.mood ?? '' });
const deleting = ref(null);
const { formatDate } = useDateFormatter();
const search = () => router.get('/journal', filters, { preserveState: true, replace: true });
const reset = () => { filters.q = ''; filters.mood = ''; search(); };
const remove = (entry) => {
    if (deleting.value !== null || !confirm('Supprimer cette entrée ?')) return;
    deleting.value = entry.id;
    router.delete(`/journal/${entry.id}`, { preserveScroll: true, onFinish: () => { deleting.value = null; } });
};
</script>

<template>
    <div>
        <div class="page-head">
            <div><h1 class="page-title">Journal</h1><p class="page-subtitle">Tes réflexions, jour après jour</p></div>
            <Link href="/journal/create" class="btn"><Icon name="plus" :size="16" /> Nouvelle entrée</Link>
        </div>
        <form class="card journal-filters" role="search" @submit.prevent="search">
            <div class="field"><label for="journal-search">Rechercher dans le contenu</label>
                <input id="journal-search" v-model="filters.q" type="search" maxlength="200" class="input" placeholder="Un souvenir, une idée…" />
            </div>
            <div class="field"><label for="journal-mood">Humeur</label>
                <select id="journal-mood" v-model="filters.mood" class="select"><option value="">Toutes les humeurs</option>
                    <option v-for="mood in moods" :key="mood.value" :value="mood.value">{{ mood.label }}</option>
                </select>
            </div>
            <button class="btn" type="submit">Rechercher</button><button class="btn btn--ghost" type="button" @click="reset">Réinitialiser</button>
        </form>
        <p class="muted" role="status">{{ entries.total }} entrée(s)</p>
        <div v-if="entries.data.length" class="stack">
            <article v-for="entry in entries.data" :key="entry.id" class="card stack--sm">
                <div class="row row--between" style="flex-wrap: wrap">
                    <strong>{{ formatDate(entry.date, { weekday: 'long' }) }}</strong>
                    <div class="row" style="gap: 0.4rem">
                        <span v-if="entry.mood && moodMap[entry.mood]" class="badge">{{ moodMap[entry.mood].emoji }} {{ moodMap[entry.mood].label }}</span>
                        <Link :href="`/journal/${entry.id}/edit`" class="icon-btn" aria-label="Modifier l’entrée"><Icon name="edit" :size="16" /></Link>
                        <button type="button" class="icon-btn" aria-label="Supprimer l’entrée" :disabled="deleting !== null" @click="remove(entry)"><Icon name="trash" :size="16" /></button>
                    </div>
                </div>
                <p style="margin: 0; white-space: pre-wrap; overflow-wrap: anywhere; color: var(--text-soft)">{{ entry.content }}</p>
            </article>
        </div>
        <p v-else class="empty">Aucune entrée ne correspond. <Link href="/journal/create">Écrire une réflexion</Link>.</p>
        <nav v-if="entries.last_page > 1" class="row" aria-label="Pagination du journal" style="margin-top: 1.5rem; flex-wrap: wrap">
            <Link v-if="entries.prev_page_url" :href="entries.prev_page_url" class="btn btn--ghost" preserve-scroll>Précédent</Link>
            <span aria-current="page">Page {{ entries.current_page }} sur {{ entries.last_page }}</span>
            <Link v-if="entries.next_page_url" :href="entries.next_page_url" class="btn btn--ghost" preserve-scroll>Suivant</Link>
        </nav>
    </div>
</template>

<style scoped>
.journal-filters { display: flex; align-items: end; flex-wrap: wrap; gap: 1rem; }
.journal-filters .field { flex: 1 1 12rem; }
</style>
