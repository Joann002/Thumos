<template>
    <div class="error-page">
        <div class="error-content">
            <h1 class="error-code">{{ status }}</h1>
            <h2 class="error-title">{{ title }}</h2>
            <p class="error-description">{{ description }}</p>
            <Link href="/dashboard" class="btn">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M10 13L5 8L10 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Retour au tableau de bord
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: Number,
        required: true,
    },
});

const title = computed(() => {
    const titles = {
        403: 'Accès interdit',
        404: 'Page introuvable',
        500: 'Erreur serveur',
        503: 'Service indisponible',
    };
    return titles[props.status] || 'Erreur';
});

const description = computed(() => {
    const descriptions = {
        403: "Vous n'avez pas l'autorisation d'accéder à cette ressource.",
        404: "La page que vous recherchez n'existe pas ou a été déplacée.",
        500: "Une erreur s'est produite sur le serveur. Veuillez réessayer plus tard.",
        503: 'Le service est temporairement indisponible. Veuillez réessayer dans quelques instants.',
    };
    return descriptions[props.status] || 'Une erreur inattendue est survenue.';
});
</script>

<style scoped>
.error-page {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 2rem;
    background: var(--bg);
}

.error-content {
    text-align: center;
    max-width: 500px;
}

.error-code {
    font-size: 6rem;
    font-weight: 800;
    margin: 0;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}

.error-title {
    margin: 1rem 0 0.5rem;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text);
}

.error-description {
    margin: 0 0 2rem;
    color: var(--muted);
    font-size: 1rem;
    line-height: 1.6;
}
</style>
