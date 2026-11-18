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
    <fieldset class="stack" :disabled="form.processing" style="border: 0; padding: 0; margin: 0; min-width: 0">
        <div class="field">
            <label for="date">Date</label>
            <input id="date" v-model="form.date" type="date" required class="input" />
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
                    :aria-pressed="form.mood === mood.value"
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
                id="content" maxlength="5000" :aria-invalid="Boolean(form.errors.content)" :aria-describedby="form.errors.content ? 'content-error' : undefined"
                v-model="form.content"
                required
                class="textarea"
                style="min-height: 160px"
                placeholder="Qu'as-tu en tête aujourd'hui ?"
            ></textarea>
            <span v-if="form.errors.content" id="content-error" class="error" role="alert">{{ form.errors.content }}</span>
        </div>
    </fieldset>
</template>
