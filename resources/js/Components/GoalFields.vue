<script setup>
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
            <label for="title">Titre</label>
            <input id="title" maxlength="255" :aria-invalid="Boolean(form.errors.title)" :aria-describedby="form.errors.title ? 'title-error' : undefined" v-model="form.title" type="text" required class="input" />
            <span v-if="form.errors.title" id="title-error" class="error" role="alert">{{ form.errors.title }}</span>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" maxlength="2000" :aria-invalid="Boolean(form.errors.description)" :aria-describedby="form.errors.description ? 'description-error' : undefined" v-model="form.description" class="textarea"></textarea>
            <span v-if="form.errors.description" id="description-error" class="error" role="alert">{{ form.errors.description }}</span>
        </div>

        <div class="field">
            <label for="category">Catégorie</label>
            <input id="category" maxlength="100" :aria-invalid="Boolean(form.errors.category)" :aria-describedby="form.errors.category ? 'category-error' : undefined" v-model="form.category" type="text" class="input" placeholder="Santé, Carrière…" />
            <span v-if="form.errors.category" id="category-error" class="error" role="alert">{{ form.errors.category }}</span>
        </div>

        <div class="field">
            <label for="target_date">Date cible</label>
            <input id="target_date" v-model="form.target_date" type="date" class="input" />
            <span v-if="form.errors.target_date" class="error">{{ form.errors.target_date }}</span>
        </div>

        <div class="field">
            <label for="status">Statut</label>
            <select id="status" v-model="form.status" class="select">
                <option value="active">En cours</option>
                <option value="paused">En pause</option>
                <option value="done">Terminé</option>
            </select>
            <span v-if="form.errors.status" class="error">{{ form.errors.status }}</span>
        </div>
    </fieldset>
</template>
