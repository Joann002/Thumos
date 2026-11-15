<template>
    <div class="guest-layout">
        <div class="guest-layout__content">
            <div class="guest-layout__logo">
                <div class="logo">
                    <div class="logo__icon">⚡</div>
                    <span class="logo__text">Thumos</span>
                </div>
            </div>
            <slot />
        </div>
    </div>
</template>

<style scoped>
.guest-layout {
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 2rem;
    background: var(--bg);
}

.guest-layout__content {
    width: 100%;
    max-width: 420px;
}

.guest-layout__logo {
    text-align: center;
    margin-bottom: 2rem;
}

.logo {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}

.logo__icon {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    font-size: 1.5rem;
}

.logo__text {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text);
}
</style>
