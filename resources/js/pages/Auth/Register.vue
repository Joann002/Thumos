<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

defineOptions({ layout: GuestLayout });

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () =>
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
</script>

<template>
    <form class="card stack" @submit.prevent="submit">
        <h1 style="margin: 0 0 0.5rem; font-size: 1.3rem">Créer un compte</h1>

        <div class="field">
            <label for="name">Nom</label>
            <input id="name" v-model="form.name" type="text" class="input" autofocus />
            <span v-if="form.errors.name" class="error">{{ form.errors.name }}</span>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" v-model="form.email" type="email" class="input" />
            <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
        </div>

        <div class="field">
            <label for="password">Mot de passe</label>
            <input id="password" v-model="form.password" type="password" class="input" />
            <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="input" />
        </div>

        <button type="submit" class="btn" :disabled="form.processing">S'inscrire</button>

        <p class="muted" style="margin: 0; text-align: center">
            Déjà un compte ? <Link href="/login">Se connecter</Link>
        </p>
    </form>
</template>
