<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="app-layout">
        <main class="app-main" style="max-width: 420px">
            <div style="text-align: center; margin-bottom: 1.5rem">
                <Link href="/" class="app-header__brand" style="font-size: 1.5rem">Thumos</Link>
                <p class="muted" style="margin: 0.25rem 0 0">Ton tableau de bord de vie</p>
            </div>
            <slot />
        </main>
    </div>
</template>
