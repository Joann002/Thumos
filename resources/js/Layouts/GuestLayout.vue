<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '../Components/Icon.vue';
</script>

<template>
    <div style="min-height: 100dvh; display: grid; place-items: center; padding: 1.5rem; background: var(--bg)">
        <div style="width: 100%; max-width: 400px">
            <div style="text-align: center; margin-bottom: 1.75rem">
                <Link href="/" class="sidebar__brand" style="justify-content: center; padding: 0; font-size: 1.5rem">
                    <span class="sidebar__logo" style="width: 36px; height: 36px"><Icon name="sparkle" :size="20" /></span>
                    Thumos
                </Link>
                <p class="muted" style="margin: 0.4rem 0 0">Ton tableau de bord de vie</p>
            </div>
            <slot />
        </div>
    </div>
</template>
