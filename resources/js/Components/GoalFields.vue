<script setup>
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
            <label for="title">Titre</label>
            <input id="title" v-model="form.title" type="text" class="input" />
            <span v-if="form.errors.title" class="error">{{ form.errors.title }}</span>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" v-model="form.description" class="textarea"></textarea>
            <span v-if="form.errors.description" class="error">{{ form.errors.description }}</span>
        </div>

        <div class="field">
            <label for="category">Catégorie</label>
            <input id="category" v-model="form.category" type="text" class="input" placeholder="Santé, Carrière…" />
            <span v-if="form.errors.category" class="error">{{ form.errors.category }}</span>
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
    </div>
</template>
