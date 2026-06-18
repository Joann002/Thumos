<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash?.success);
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
                </nav>
            </div>
        </header>
        <main class="app-main">
            <p v-if="flash" class="badge badge--done" style="margin-bottom: 1rem">{{ flash }}</p>
            <slot />
        </main>
    </div>
</template>
