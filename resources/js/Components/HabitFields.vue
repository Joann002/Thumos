<script setup>
defineProps({
    form: {
        type: Object,
        required: true,
    },
});

const weekdays = [
    { value: 1, label: 'Lun' },
    { value: 2, label: 'Mar' },
    { value: 3, label: 'Mer' },
    { value: 4, label: 'Jeu' },
    { value: 5, label: 'Ven' },
    { value: 6, label: 'Sam' },
    { value: 7, label: 'Dim' },
];

const colors = ['#4f46e5', '#16a34a', '#dc2626', '#d97706', '#0891b2', '#7c3aed'];
</script>

<template>
    <div class="stack">
        <div class="field">
            <label for="name">Nom</label>
            <input id="name" v-model="form.name" type="text" class="input" placeholder="Méditer, Lire…" />
            <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
        </div>

        <div class="field">
            <label for="frequency">Fréquence</label>
            <select id="frequency" v-model="form.frequency" class="select">
                <option value="daily">Quotidienne</option>
                <option value="weekly">Hebdomadaire</option>
            </select>
            <span v-if="form.errors.frequency" class="error">{{ form.errors.frequency }}</span>
        </div>

        <div v-if="form.frequency === 'weekly'" class="field">
            <label>Jours concernés</label>
            <div class="row" style="flex-wrap: wrap; gap: 0.5rem">
                <label
                    v-for="day in weekdays"
                    :key="day.value"
                    class="badge"
                    style="cursor: pointer; display: inline-flex; gap: 0.35rem; align-items: center"
                >
                    <input type="checkbox" :value="day.value" v-model="form.days_of_week" />
                    {{ day.label }}
                </label>
            </div>
            <span v-if="form.errors.days_of_week" class="error">{{ form.errors.days_of_week }}</span>
        </div>

        <div class="field">
            <label>Couleur</label>
            <div class="row" style="gap: 0.5rem">
                <button
                    v-for="color in colors"
                    :key="color"
                    type="button"
                    @click="form.color = color"
                    :style="{
                        width: '26px',
                        height: '26px',
                        borderRadius: '50%',
                        background: color,
                        border: form.color === color ? '3px solid #1f2430' : '1px solid #e2e5ea',
                        cursor: 'pointer',
                    }"
                    :aria-label="color"
                ></button>
            </div>
        </div>
    </div>
</template>
