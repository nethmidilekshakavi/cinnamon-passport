<script setup>
import InputError from '@/Components/InputError.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const { t } = useI18n();

const brand = {
    icon: '/images/welcome/logo2.png',
};

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head :title="t('auth.registerTitle')" />

    <!-- One full page: gradient background, everything centred -->
    <div class="cw-bg relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-5 py-10 text-white">
        <!-- Background: glows and a faint dot grid -->
        <div class="cw-dots pointer-events-none absolute inset-0"></div>
        <div class="cw-glow pointer-events-none absolute -right-40 -top-40 h-[34rem] w-[34rem] rounded-full"></div>
        <div class="pointer-events-none absolute -bottom-52 -left-40 h-[30rem] w-[30rem] rounded-full bg-[#2A8C63]/30 blur-3xl"></div>

        <div class="relative w-full max-w-md">
            <!-- Logo and name -->
            <Link href="/" class="cw-rise mx-auto flex flex-col items-center text-center" style="--d: 0s">
                <span class="grid h-24 w-24 place-items-center overflow-hidden rounded-full bg-white shadow-2xl shadow-black/30 ring-[6px] ring-[#E3B04B]/50 sm:h-28 sm:w-28">
                    <img :src="brand.icon" alt="Cinnamon Trust" width="112" height="112" class="h-full w-full object-cover mix-blend-multiply" />
                </span>
                <span class="font-display mt-4 text-2xl font-bold">Cinnamon Trust</span>
                <span class="text-sm tracking-wide text-white/70">{{ t('welcome.brandSub') }}</span>
            </Link>

            <!-- Form card -->
            <div class="cw-rise mt-7 rounded-[2rem] bg-white p-7 text-[#14241B] shadow-2xl shadow-black/30 sm:p-9" style="--d: 0.12s">
                <h1 class="font-display cin-h1 text-3xl font-extrabold">{{ t('auth.registerTitle') }}</h1>
                <div class="mt-3 h-1 w-12 rounded-full bg-[#E3B04B]"></div>

                <form class="mt-6" @submit.prevent="submit">
                    <div>
                        <label for="name" class="block text-sm font-semibold">{{ t('auth.name') }}</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="cw-input mt-1.5"
                            required
                            autofocus
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-5">
                        <label for="email" class="block text-sm font-semibold">{{ t('auth.email') }}</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="cw-input mt-1.5"
                            required
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-5">
                        <label for="password" class="block text-sm font-semibold">{{ t('auth.password') }}</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="cw-input mt-1.5"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-5">
                        <label for="password_confirmation" class="block text-sm font-semibold">{{ t('auth.confirmPassword') }}</label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="cw-input mt-1.5"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <button
                        type="submit"
                        class="mt-7 inline-flex min-h-[52px] w-full items-center justify-center rounded-full bg-[#14503A] px-8 font-bold text-white shadow-lg shadow-[#14503A]/30 transition hover:bg-[#0B3B2C] active:scale-[.98] disabled:cursor-not-allowed disabled:opacity-60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#14503A]"
                        :disabled="form.processing"
                    >
                        {{ t('auth.registerTitle') }}
                    </button>

                    <p class="mt-5 text-center">
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold text-[#B5602A] underline underline-offset-4 hover:text-[#8E4A1F]"
                        >
                            {{ t('auth.already') }}
                        </Link>
                    </p>
                </form>
            </div>

            <div class="cw-rise mt-7 flex justify-center" style="--d: 0.24s">
                <LanguageSwitcher />
            </div>
        </div>
    </div>
</template>

<style scoped>
.cw-bg {
    background: linear-gradient(160deg, #0b3b2c 0%, #14503a 55%, #1e6b4c 100%);
}
.cw-glow {
    background: radial-gradient(circle, rgba(227, 176, 75, 0.45), transparent 68%);
}
.cw-dots {
    background-image: radial-gradient(rgba(255, 255, 255, 0.09) 1px, transparent 1px);
    background-size: 26px 26px;
    mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
    -webkit-mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
}

.cw-input {
    display: block;
    width: 100%;
    min-height: 3rem;
    padding: 0.7rem 1rem;
    border: 1.5px solid #e8dfc9;
    border-radius: 0.9rem;
    background: #fffdf8;
    color: #14241b;
    transition: border-color 0.15s, box-shadow 0.15s;
}
/* Beats the global brown focus ring in app.css */
.cw-input:focus {
    border-color: #14503a !important;
    box-shadow: 0 0 0 3px rgba(30, 107, 76, 0.2) !important;
    outline: none !important;
    background: #fff;
}

/* Logo, card, language switcher rise in order */
.cw-rise {
    opacity: 0;
    animation: cw-rise 0.6s ease-out var(--d, 0s) forwards;
}
@keyframes cw-rise {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: none; }
}
@media (prefers-reduced-motion: reduce) {
    .cw-rise { opacity: 1; animation: none; }
}
</style>
