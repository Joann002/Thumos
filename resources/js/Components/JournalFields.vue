<script setup>
import { moods } from './moods';

defineProps({
    form: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="stack">
        <div class="field">
            <label for="date">Date</label>
            <input id="date" v-model="form.date" type="date" class="input" />
            <span v-if="form.errors.date" class="error">{{ form.errors.date }}</span>
        </div>

        <div class="field">
            <label>Humeur</label>
            <div class="row" style="flex-wrap: wrap; gap: 0.5rem">
                <button
                    v-for="mood in moods"
                    :key="mood.value"
                    type="button"
                    class="badge"
                    :style="{
                        cursor: 'pointer',
                        borderColor: form.mood === mood.value ? '#1f2430' : '#e2e5ea',
                        background: form.mood === mood.value ? '#eef2ff' : '#f6f7f9',
                    }"
                    @click="form.mood = form.mood === mood.value ? null : mood.value"
                >
                    {{ mood.emoji }} {{ mood.label }}
                </button>
            </div>
            <span v-if="form.errors.mood" class="error">{{ form.errors.mood }}</span>
        </div>

        <div class="field">
            <label for="content">Contenu</label>
            <textarea
                id="content"
                v-model="form.content"
                class="textarea"
                style="min-height: 160px"
                placeholder="Qu'as-tu en tête aujourd'hui ?"
            ></textarea>
            <span v-if="form.errors.content" class="error">{{ form.errors.content }}</span>
        </div>
    </div>
</template>
