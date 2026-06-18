<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '../Components/Icon.vue';
import { useTheme } from '../composables/useTheme';

const page = usePage();
const { theme, toggle } = useTheme();

const flash = computed(() => page.props.flash?.success);
const user = computed(() => page.props.auth?.user);
const url = computed(() => page.url);

const initials = computed(() => {
    const name = user.value?.name ?? '';
    return name.trim().slice(0, 1).toUpperCase() || '?';
});

const nav = [
    { label: 'Accueil', href: '/dashboard', icon: 'dashboard' },
    { label: 'Objectifs', href: '/goals', icon: 'target' },
    { label: 'Habitudes', href: '/habits', icon: 'habit' },
    { label: 'Journal', href: '/journal', icon: 'journal' },
    { label: 'Calendrier', href: '/calendar', icon: 'calendar' },
    { label: 'Régularité', href: '/heatmap', icon: 'activity' },
];

const isActive = (href) => url.value === href || url.value.startsWith(href + '/') || url.value.startsWith(href + '?');

const logout = () => router.post('/logout');
</script>

<template>
    <div class="shell">
        <!-- Desktop sidebar -->
        <aside class="sidebar">
            <Link href="/dashboard" class="sidebar__brand">
                <span class="sidebar__logo"><Icon name="sparkle" :size="17" /></span>
                Thumos
            </Link>

            <nav class="stack--sm" style="display: flex; flex-direction: column; gap: 0.25rem">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="nav-link"
                    :class="{ 'nav-link--active': isActive(item.href) }"
                >
                    <Icon :name="item.icon" :size="18" />
                    {{ item.label }}
                </Link>
            </nav>

            <div class="sidebar__footer">
                <button class="nav-link" type="button" style="width: 100%; cursor: pointer; background: none; border: none; text-align: left" @click="toggle">
                    <Icon :name="theme === 'dark' ? 'sun' : 'moon'" :size="18" />
                    {{ theme === 'dark' ? 'Thème clair' : 'Thème sombre' }}
                </button>
                <div class="sidebar__user" v-if="user">
                    <span class="avatar">{{ initials }}</span>
                    <span style="min-width: 0; flex: 1">
                        <span style="display: block; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap">{{ user.name }}</span>
                    </span>
                    <button class="icon-btn" type="button" title="Déconnexion" aria-label="Déconnexion" @click="logout">
                        <Icon name="logout" :size="17" />
                    </button>
                </div>
            </div>
        </aside>

        <div class="content">
            <!-- Mobile top bar -->
            <header class="topbar">
                <Link href="/dashboard" class="sidebar__brand" style="padding: 0; font-size: 1rem">
                    <span class="sidebar__logo"><Icon name="sparkle" :size="15" /></span>
                    Thumos
                </Link>
                <nav class="topbar__nav">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="nav-link"
                        :class="{ 'nav-link--active': isActive(item.href) }"
                        :title="item.label"
                    >
                        <Icon :name="item.icon" :size="18" />
                    </Link>
                </nav>
                <button class="icon-btn" type="button" :title="theme === 'dark' ? 'Thème clair' : 'Thème sombre'" @click="toggle">
                    <Icon :name="theme === 'dark' ? 'sun' : 'moon'" :size="17" />
                </button>
                <button v-if="user" class="icon-btn" type="button" title="Déconnexion" aria-label="Déconnexion" @click="logout">
                    <Icon name="logout" :size="17" />
                </button>
            </header>

            <main class="main">
                <p v-if="flash" class="flash"><Icon name="check" :size="16" /> {{ flash }}</p>
                <slot />
            </main>
        </div>
    </div>
</template>
