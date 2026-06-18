<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

defineOptions({ layout: GuestLayout });

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () =>
    form
        .transform((data) => ({ ...data, remember: data.remember ? 'on' : '' }))
        .post('/login', {
            onfinish: () => form.reset('password'),
        });
</script>

<template>
    <form class="card stack" @submit.prevent="submit">
        <h1 style="margin: 0 0 0.5rem; font-size: 1.3rem">Connexion</h1>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" v-model="form.email" type="email" class="input" autofocus />
            <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
        </div>

        <div class="field">
            <label for="password">Mot de passe</label>
            <input id="password" v-model="form.password" type="password" class="input" />
            <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
        </div>

        <label class="row" style="gap: 0.5rem; cursor: pointer">
            <input v-model="form.remember" type="checkbox" />
            <span class="muted">Se souvenir de moi</span>
        </label>

        <button type="submit" class="btn" :disabled="form.processing">Se connecter</button>

        <p class="muted" style="margin: 0; text-align: center">
            Pas encore de compte ? <Link href="/register">Créer un compte</Link>
        </p>
    </form>
</template>
