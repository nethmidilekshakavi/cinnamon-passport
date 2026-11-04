<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import LanguagePicker from '@/Components/LanguagePicker.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import SplashScreen from '@/Components/SplashScreen.vue';
import { useI18n } from '@/i18n';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const { t, chosen } = useI18n();
const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);

const brand = {
    icon: '/images/welcome/logo2.png',
};

const img = {
    steps: [1, 2, 3, 4].map((n) => `/images/welcome/step${n}.jpg`),
    built: '/images/welcome/built.jpg',
};

// A missing photo hides its frame instead of showing a broken image.
const hideOnError = (e) => {
    const frame = e.target.closest('.cw-photo');
    (frame ?? e.target).style.display = 'none';
};

const seen = (() => {
    try {
        return sessionStorage.getItem('cp_splash') === '1';
    } catch {
        return false;
    }
})();
const showSplash = ref(!seen);
const showPicker = computed(() => !showSplash.value && !chosen.value);

onMounted(() => {
    if (seen) return;
    try {
        sessionStorage.setItem('cp_splash', '1');
    } catch {
        // storage blocked
    }
    setTimeout(() => {
        showSplash.value = false;
    }, 2000);
});

const qrCells = (() => {
    const size = 11;
    let seed = 42;
    const cells = [];
    for (let y = 0; y < size; y++) {
        for (let x = 0; x < size; x++) {
            const finder = (x < 3 && y < 3) || (x > size - 4 && y < 3) || (x < 3 && y > size - 4);
            seed = (seed * 1103515245 + 12345) & 0x7fffffff;
            if (finder || seed % 3 === 0) cells.push({ x, y });
        }
    }
    return cells;
})();

const sampleFields = computed(() => [
    { label: t('welcome.field.plot'), value: 'P1' },
    { label: t('welcome.field.weight'), value: t('welcome.value.weight') },
    { label: t('welcome.field.grade'), value: 'ALBA' },
    { label: t('welcome.field.harvested'), value: t('welcome.value.harvested') },
    { label: t('welcome.field.drying'), value: t('welcome.value.drying') },
]);
const mrz = 'CIN<<0042<<P1<<40KG<<ALBA<<<<';

const steps = computed(() =>
    [1, 2, 3, 4].map((n) => ({ title: t(`welcome.step${n}.title`), text: t(`welcome.step${n}.text`) })),
);
const points = computed(() =>
    [1, 2, 3].map((n) => ({ title: t(`welcome.point${n}.title`), text: t(`welcome.point${n}.text`) })),
);
const chips = computed(() => [1, 2, 3].map((n) => t(`welcome.chip${n}`)));

const roleIcons = [
    'M5 19c0-9 5-14 14-14 0 9-5 14-14 14zM5 19l8-8',
    'M4 8h14l-3-3M20 16H6l3 3',
    'M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6zM9 12l2 2 4-4',
];
// One tone per role card so the row reads as a set, not three copies.
const roleTones = [
    'bg-[#E3B04B] text-[#14241B]',
    'bg-[#14503A] text-white',
    'bg-white text-[#14241B] ring-1 ring-[#E8DFC9]',
];
const roles = computed(() =>
    [1, 2, 3].map((n) => ({
        title: t(`welcome.role${n}.title`),
        text: t(`welcome.role${n}.text`),
        icon: roleIcons[n - 1],
        tone: roleTones[n - 1],
    })),
);

// Palette (everything lives in this file):
// deep #0B3B2C, green #14503A, emerald #1E6B4C, gold #E3B04B,
// cream #FFF9EE, ink #14241B, mint #DDEFE3, line #E8DFC9
const btnGold =
    'inline-flex min-h-[52px] w-full items-center justify-center rounded-full bg-[#E3B04B] px-8 font-bold text-[#14241B] shadow-lg shadow-black/20 transition hover:bg-[#F0C566] active:scale-[.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:w-auto';
const btnGlass =
    'inline-flex min-h-[52px] w-full items-center justify-center rounded-full border-2 border-white/40 px-8 font-semibold text-white transition hover:bg-white/10 active:scale-[.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:w-auto';
</script>

<template>
    <Head :title="t('welcome.pageTitle')" />

    <SplashScreen :show="showSplash" />

    <Transition name="cin-fade">
        <LanguagePicker v-if="showPicker" />
    </Transition>

    <div class="min-h-screen bg-[#FFF9EE] text-[#14241B]">
        <!-- Hero with sticky nav -->
        <div class="cw-hero relative overflow-hidden text-white">
            <div class="cw-glow pointer-events-none absolute -right-40 -top-40 h-[34rem] w-[34rem] rounded-full"></div>
            <div class="pointer-events-none absolute -bottom-52 -left-40 h-[30rem] w-[30rem] rounded-full bg-[#1E6B4C]/40 blur-3xl"></div>

            <header class="sticky top-0 z-40 border-b border-white/10 bg-[#0B3B2C]/85 backdrop-blur">
                <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-4 gap-y-3 px-5 py-3 sm:px-6">
                    <Link href="/" class="flex items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full bg-white">
                            <img :src="brand.icon" alt="" width="40" height="40" class="h-10 w-10 object-cover mix-blend-multiply" />
                        </span>
                        <span class="leading-tight">
                            <span class="font-display block text-lg font-bold">Cinnamon Trust</span>
                            <span class="block text-[11px] tracking-wide text-white/65">{{ t('welcome.brandSub') }}</span>
                        </span>
                    </Link>

                    <div class="flex items-center gap-3 text-sm">
                        <Link v-if="user" :href="route('dashboard')" class="rounded-full bg-[#E3B04B] px-4 py-2 font-bold text-[#14241B]">
                            {{ t('nav.openArea') }}
                        </Link>
                        <template v-else>
                            <Link v-if="canLogin" :href="route('login')" class="px-2 py-2 font-medium text-white/90 hover:text-white">
                                {{ t('nav.login') }}
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="rounded-full bg-[#E3B04B] px-4 py-2 font-bold text-[#14241B]">
                                {{ t('nav.register') }}
                            </Link>
                        </template>
                    </div>

                    <div class="order-last w-full sm:order-none sm:w-auto">
                        <LanguageSwitcher />
                    </div>
                </nav>
            </header>

            <section class="relative mx-auto grid max-w-6xl items-center gap-14 px-5 pb-24 pt-10 sm:px-6 md:grid-cols-12 md:gap-8 md:pb-32 md:pt-16">
                <div class="md:col-span-7">
                    <!-- Big logo -->
                    <div class="cw-rise mb-7 grid h-32 w-32 place-items-center overflow-hidden rounded-full bg-white shadow-2xl shadow-black/30 ring-[6px] ring-[#E3B04B]/50 sm:h-40 sm:w-40" style="--d: 0s">
                        <img :src="brand.icon" alt="Cinnamon Trust" width="160" height="160" class="h-full w-full object-cover mix-blend-multiply" />
                    </div>

                    <p class="cw-rise inline-flex items-center gap-2 rounded-full border border-[#E3B04B]/50 bg-white/5 px-3.5 py-1.5 text-sm font-medium text-[#F0C566]" style="--d: 0.08s">
                        <span class="h-2 w-2 rounded-full bg-[#E3B04B]"></span>
                        {{ t('welcome.badge') }}
                    </p>
                    <h1 class="cw-rise font-display cin-h1 mt-5 text-[2.3rem] font-extrabold sm:text-6xl" style="--d: 0.16s">
                        {{ t('welcome.h1') }}
                    </h1>
                    <p class="cw-rise mt-5 max-w-md text-base text-white/80 sm:text-lg" style="--d: 0.24s">
                        {{ t('welcome.intro') }}
                    </p>

                    <div class="cw-rise mt-8 flex flex-col gap-3 sm:flex-row" style="--d: 0.32s">
                        <Link v-if="user" :href="route('dashboard')" :class="btnGold">{{ t('nav.openArea') }}</Link>
                        <template v-else>
                            <Link v-if="canRegister" :href="route('register')" :class="btnGold">
                                {{ t('welcome.createAccount') }}
                            </Link>
                            <Link v-if="canLogin" :href="route('login')" :class="btnGlass">{{ t('nav.login') }}</Link>
                        </template>
                    </div>
                    <Link
                        :href="route('verify.index')"
                        class="cw-rise mt-5 inline-block text-sm font-semibold text-[#F0C566] underline underline-offset-4 hover:text-white"
                        style="--d: 0.4s"
                    >
                        {{ t('welcome.ctaButton') }} →
                    </Link>
                </div>

                <!-- Sample passport -->
                <div class="cw-rise relative mx-auto w-full max-w-sm md:col-span-5" style="--d: 0.3s">
                    <div class="absolute inset-0 translate-x-3 translate-y-3 rotate-3 rounded-3xl bg-[#E3B04B]/70"></div>
                    <div class="cw-float relative overflow-hidden rounded-3xl bg-white text-[#14241B] shadow-2xl shadow-black/40">
                        <div class="flex items-center justify-between gap-3 bg-[#14503A] px-5 py-3 text-sm font-semibold text-white">
                            <span>Cinnamon Trust</span>
                            <span class="font-normal text-white/75">{{ t('welcome.sampleTitle') }}</span>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-4">
                                <p class="font-display text-4xl font-extrabold">CIN-0042</p>
                                <svg
                                    viewBox="0 0 11 11"
                                    class="h-16 w-16 shrink-0"
                                    shape-rendering="crispEdges"
                                    role="img"
                                    :aria-label="t('welcome.qrLabel')"
                                >
                                    <rect v-for="c in qrCells" :key="`${c.x}-${c.y}`" :x="c.x" :y="c.y" width="1" height="1" fill="#0B3B2C" />
                                </svg>
                            </div>

                            <dl class="mt-5 grid grid-cols-2 gap-2.5 text-sm">
                                <div v-for="(f, i) in sampleFields" :key="i" class="rounded-xl bg-[#FFF9EE] px-3 py-2.5 ring-1 ring-[#E8DFC9]">
                                    <dt class="text-[#14241B]/55">{{ f.label }}</dt>
                                    <dd class="mt-0.5 font-semibold">{{ f.value }}</dd>
                                </div>
                            </dl>

                            <p class="mt-5 break-all rounded-lg bg-[#DDEFE3] px-3 py-2 font-mono text-[11px] tracking-widest text-[#14503A]">
                                {{ mrz }}
                            </p>
                            <div class="h-10"></div>
                        </div>

                        <span class="cw-seal absolute bottom-4 right-4 rounded-full border-[3px] border-[#1E6B4C] bg-white/90 px-4 py-2 text-center text-xs font-bold leading-tight text-[#1E6B4C]">
                            {{ t('welcome.seal') }}
                        </span>
                    </div>

                    <!-- Floating SMS chip: ties the passport to how it is made -->
                    <p class="absolute -bottom-5 -left-3 hidden rounded-2xl rounded-bl-sm bg-[#1E6B4C] px-3 py-2 font-mono text-xs text-white shadow-xl ring-2 ring-white/20 sm:block md:-left-8">
                        BATCH P1 40KG
                    </p>
                </div>
            </section>

            <svg class="absolute bottom-0 left-0 block h-10 w-full sm:h-16" viewBox="0 0 1440 80" preserveAspectRatio="none" fill="#FFF9EE" aria-hidden="true">
                <path d="M0 45C240 90 480 0 720 30s480 60 720 0V80H0Z" />
            </svg>
        </div>

        <!-- How a batch travels -->
        <section class="mx-auto max-w-6xl px-5 py-14 sm:px-6 md:py-20">
            <h2 class="font-display max-w-2xl text-3xl font-extrabold sm:text-5xl">{{ t('welcome.travelTitle') }}</h2>
            <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <li
                    v-for="(s, i) in steps"
                    :key="i"
                    class="group overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-[#E8DFC9] transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="cw-photo aspect-[4/3] overflow-hidden bg-[#E8DFC9]">
                        <img
                            :src="img.steps[i]"
                            :alt="s.title"
                            loading="lazy"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            @error="hideOnError"
                        />
                    </div>
                    <div class="relative p-5 pt-7">
                        <span class="font-display absolute -top-6 left-5 grid h-12 w-12 place-items-center rounded-2xl bg-[#E3B04B] text-xl font-extrabold text-[#14241B] shadow-lg">
                            {{ i + 1 }}
                        </span>
                        <h3 class="mt-1 text-lg font-bold">{{ s.title }}</h3>
                        <p class="mt-1 text-sm text-[#14241B]/70">{{ s.text }}</p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- SMS -->
        <section class="relative overflow-hidden bg-[#0B3B2C] text-white">
            <div class="cw-glow pointer-events-none absolute -left-40 -top-40 h-[28rem] w-[28rem] rounded-full opacity-60"></div>
            <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-5 py-16 sm:px-6 md:grid-cols-2 md:py-24">
                <div>
                    <h2 class="font-display text-3xl font-extrabold sm:text-5xl">{{ t('welcome.smsTitle') }}</h2>
                    <p class="mt-4 max-w-md text-lg text-white/80">{{ t('welcome.smsText') }}</p>
                    <ul class="mt-7 flex flex-wrap gap-2.5">
                        <li v-for="c in chips" :key="c" class="rounded-full border border-[#E3B04B]/60 px-4 py-1.5 text-sm font-medium text-[#F0C566]">
                            {{ c }}
                        </li>
                    </ul>
                </div>

                <div class="mx-auto w-full max-w-[290px] rounded-[2.4rem] bg-[#E3B04B] p-3 shadow-2xl shadow-black/40 md:rotate-2">
                    <div class="rounded-[1.9rem] bg-[#FFF9EE] p-3 text-[#14241B]">
                        <p class="py-1 text-center text-xs font-semibold text-[#14241B]/55">Messages</p>
                        <div class="space-y-3 py-3">
                            <div class="flex justify-end">
                                <p class="max-w-[85%] break-words rounded-2xl rounded-br-sm bg-[#14503A] px-3 py-2 font-mono text-[13px] text-white">
                                    BATCH P1 40KG 12OCT DRY3 ALBA
                                </p>
                            </div>
                            <div class="flex justify-start">
                                <p class="max-w-[85%] rounded-2xl rounded-bl-sm bg-[#DDEFE3] px-3 py-2 text-[13px]">
                                    OK CIN-0042 saved: P1, 40kg, 12 Oct, dried 3 days.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Roles: three tones -->
        <section class="mx-auto max-w-6xl px-5 py-14 sm:px-6 md:py-24">
            <h2 class="font-display max-w-2xl text-3xl font-extrabold sm:text-5xl">{{ t('welcome.rolesTitle') }}</h2>
            <div class="mt-12 grid gap-5 md:grid-cols-3">
                <div v-for="r in roles" :key="r.title" :class="['rounded-[2rem] p-7 transition hover:-translate-y-1', r.tone]">
                    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-black/10">
                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path :d="r.icon" />
                        </svg>
                    </span>
                    <h3 class="mt-6 text-xl font-bold">{{ r.title }}</h3>
                    <p class="mt-2 opacity-80">{{ r.text }}</p>
                </div>
            </div>
        </section>

        <!-- Built for -->
        <section class="bg-[#DDEFE3]/60">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-5 py-14 sm:px-6 md:grid-cols-5 md:py-24">
                <div class="relative order-last md:order-none md:col-span-2">
                    <div class="absolute inset-0 -translate-x-3 translate-y-3 rounded-[2rem] bg-[#E3B04B]"></div>
                    <div class="cw-photo relative aspect-[4/5] overflow-hidden rounded-[2rem] bg-[#E8DFC9]">
                        <img :src="img.built" alt="" loading="lazy" class="h-full w-full object-cover" @error="hideOnError" />
                    </div>
                </div>
                <div class="md:col-span-3">
                    <h2 class="font-display max-w-xl text-3xl font-extrabold sm:text-5xl">{{ t('welcome.builtTitle') }}</h2>
                    <ul class="mt-8 space-y-5">
                        <li v-for="(p, i) in points" :key="i" class="flex gap-4">
                            <span class="font-display grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#14503A] font-bold text-[#E3B04B]">
                                {{ i + 1 }}
                            </span>
                            <div>
                                <h3 class="text-lg font-bold">{{ p.title }}</h3>
                                <p class="mt-1 text-[#14241B]/70">{{ p.text }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Verify call to action -->
        <section class="px-5 py-14 sm:px-6 md:py-20">
            <div class="cw-cta mx-auto max-w-5xl rounded-[2.5rem] px-6 py-14 text-center text-[#14241B] sm:px-12">
                <h2 class="font-display text-3xl font-extrabold sm:text-5xl">{{ t('welcome.ctaTitle') }}</h2>
                <p class="mx-auto mt-4 max-w-lg text-lg text-[#14241B]/80">{{ t('welcome.ctaText') }}</p>
                <Link
                    :href="route('verify.index')"
                    class="mt-8 inline-flex min-h-[52px] items-center justify-center rounded-full bg-[#0B3B2C] px-8 font-bold text-white shadow-xl transition hover:bg-[#14503A] active:scale-[.98]"
                >
                    {{ t('welcome.ctaButton') }}
                </Link>
            </div>
        </section>

        <footer class="bg-[#0B3B2C] px-5 py-8 text-sm text-white/70 sm:px-6">
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-3 text-center sm:flex-row sm:text-left">
                <span class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-full bg-white">
                    <img :src="brand.icon" alt="" width="44" height="44" class="h-11 w-11 object-cover mix-blend-multiply" />
                </span>
                <p>
                    <span class="font-semibold text-white">Cinnamon Trust</span> · {{ t('welcome.brandSub') }}<br />
                    {{ t('welcome.footer') }}
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.cw-hero {
    background: linear-gradient(160deg, #0B3B2C 0%, #14503A 60%, #1E6B4C 100%);
}
.cw-glow {
    background: radial-gradient(circle, rgba(227, 176, 75, 0.45), transparent 68%);
}
.cw-cta {
    background: linear-gradient(135deg, #E3B04B 0%, #F0C566 55%, #E3B04B 100%);
}

/* One orchestrated entrance: hero items rise in order */
.cw-rise {
    opacity: 0;
    animation: cw-rise 0.7s ease-out var(--d, 0s) forwards;
}
@keyframes cw-rise {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: none; }
}

/* Passport floats gently */
.cw-float {
    animation: cw-float 6s ease-in-out infinite;
}
@keyframes cw-float {
    0%, 100% { transform: translateY(0) rotate(-1deg); }
    50% { transform: translateY(-8px) rotate(-1deg); }
}

/* Seal lands once */
.cw-seal {
    animation: cw-stamp 0.45s cubic-bezier(0.2, 0.9, 0.3, 1.2) 1s both;
}
@keyframes cw-stamp {
    from { opacity: 0; transform: rotate(-8deg) scale(1.8); }
    to { opacity: 1; transform: rotate(-8deg) scale(1); }
}

@media (prefers-reduced-motion: reduce) {
    .cw-rise { opacity: 1; animation: none; }
    .cw-float { animation: none; }
    .cw-seal { animation: none; transform: rotate(-8deg); }
}
</style>
