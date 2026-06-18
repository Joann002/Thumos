<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash?.success);
const user = computed(() => page.props.auth?.user);

const logout = () => router.post('/logout');
</script>

<template>
    <div class="app-layout">
        <header class="app-header">
            <div class="app-header__inner">
                <Link href="/" class="app-header__brand">Thumos</Link>
                <nav class="app-nav">
                    <Link href="/goals">Objectifs</Link>
                    <Link href="/habits">Habitudes</Link>
                    <Link href="/journal">Journal</Link>
                    <Link href="/calendar">Calendrier</Link>
                    <Link href="/heatmap">Régularité</Link>
                </nav>
                <div v-if="user" class="row" style="margin-left: auto; gap: 0.75rem">
                    <span class="muted">{{ user.name }}</span>
                    <button class="btn btn--ghost" @click="logout">Déconnexion</button>
                </div>
            </div>
        </header>
        <main class="app-main">
            <p v-if="flash" class="badge badge--done" style="margin-bottom: 1rem">{{ flash }}</p>
            <slot />
        </main>
    </div>
</template>
