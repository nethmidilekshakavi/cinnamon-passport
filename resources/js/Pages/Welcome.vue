<script setup>
import { computed, onMounted, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import CinnamonMark from '@/Components/CinnamonMark.vue';
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

// The loading screen shows once per browser session.
const seen = (() => {
    try {
        return sessionStorage.getItem('cp_splash') === '1';
    } catch {
        return false;
    }
})();
const showSplash = ref(!seen);

// After the loading screen, a visitor who has not chosen a language yet picks one.
const showPicker = computed(() => !showSplash.value && !chosen.value);

onMounted(() => {
    if (seen) return;
    try {
        sessionStorage.setItem('cp_splash', '1');
    } catch {
        // storage blocked: the splash simply shows again next time
    }
    setTimeout(() => {
        showSplash.value = false;
    }, 2000);
});

// Decorative sample QR code (not a real code).
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
</script>

<template>
    <Head :title="t('welcome.pageTitle')" />

    <SplashScreen :show="showSplash" />

    <Transition name="cin-fade">
        <LanguagePicker v-if="showPicker" />
    </Transition>

    <div class="min-h-screen">
        <header class="cin-hero">
            <CinnamonMark :size="640" class="cin-bg-mark" />

            <div class="relative mx-auto max-w-6xl px-6 pt-6">
                <nav class="flex flex-wrap items-center justify-between gap-4">
                    <Link href="/" class="flex items-center gap-3 text-white">
                        <CinnamonMark :size="36" />
                        <span class="font-display text-lg font-bold">Cinnamon Passport</span>
                    </Link>

                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <LanguageSwitcher />
                        <Link v-if="user" :href="route('dashboard')" class="cin-btn cin-btn-light">{{ t('nav.openArea') }}</Link>
                        <template v-else>
                            <Link v-if="canLogin" :href="route('login')" class="font-medium text-white/90 hover:text-white">{{ t('nav.login') }}</Link>
                            <Link v-if="canRegister" :href="route('register')" class="cin-btn cin-btn-light">{{ t('nav.register') }}</Link>
                        </template>
                    </div>
                </nav>

                <div class="grid items-center gap-14 py-16 md:grid-cols-2 md:py-24">
                    <div>
                        <p class="inline-block rounded-full border border-white/30 px-3 py-1 text-sm text-white/85">
                            {{ t('welcome.badge') }}
                        </p>
                        <h1 class="font-display cin-h1 mt-5 text-4xl font-bold text-white md:text-6xl">
                            {{ t('welcome.h1') }}
                        </h1>
                        <p class="mt-5 max-w-md text-white/80">
                            {{ t('welcome.intro') }}
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Link v-if="user" :href="route('dashboard')" class="cin-btn cin-btn-light">{{ t('nav.openArea') }}</Link>
                            <template v-else>
                                <Link v-if="canRegister" :href="route('register')" class="cin-btn cin-btn-light">{{ t('welcome.createAccount') }}</Link>
                                <Link v-if="canLogin" :href="route('login')" class="cin-btn cin-btn-outline-light">{{ t('nav.login') }}</Link>
                            </template>
                        </div>
                    </div>

                    <div class="px-2 md:px-6">
                        <div class="cin-stamp relative -rotate-2 p-6" style="color: var(--ink)">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="cin-muted text-sm">{{ t('welcome.sampleTitle') }}</p>
                                    <p class="font-display mt-1 text-4xl font-bold">CIN-0042</p>
                                </div>
                                <svg viewBox="0 0 11 11" class="h-20 w-20 shrink-0" shape-rendering="crispEdges" role="img" :aria-label="t('welcome.qrLabel')">
                                    <rect v-for="c in qrCells" :key="`${c.x}-${c.y}`" :x="c.x" :y="c.y" width="1" height="1" fill="#261611" />
                                </svg>
                            </div>

                            <dl class="mt-6 grid grid-cols-3 gap-x-4 gap-y-4 text-sm">
                                <div v-for="(f, i) in sampleFields" :key="i">
                                    <dt class="cin-muted">{{ f.label }}</dt>
                                    <dd class="font-semibold">{{ f.value }}</dd>
                                </div>
                            </dl>

                            <div class="cin-tear mt-6 pt-4">
                                <p class="cin-mrz">{{ mrz }}</p>
                                <p class="h-10"></p>
                            </div>

                            <span class="cin-seal">{{ t('welcome.seal') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="mx-auto max-w-6xl px-6 py-16 md:py-20">
            <h2 class="font-display text-3xl font-bold">{{ t('welcome.travelTitle') }}</h2>
            <ol class="mt-8 grid gap-8 md:grid-cols-4">
                <li v-for="(s, i) in steps" :key="i" class="pt-4" style="border-top: 2px solid var(--bark-900)">
                    <span class="font-display text-3xl font-bold" style="color: var(--bark-700)">{{ i + 1 }}</span>
                    <h3 class="mt-2 text-lg font-semibold">{{ s.title }}</h3>
                    <p class="cin-muted mt-1">{{ s.text }}</p>
                </li>
            </ol>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-6xl px-6 py-16 md:py-20">
                <h2 class="font-display max-w-xl text-3xl font-bold">{{ t('welcome.builtTitle') }}</h2>
                <div class="mt-8 grid gap-8 md:grid-cols-3">
                    <div
                        v-for="(p, i) in points"
                        :key="i"
                        class="md:border-l md:pl-6 md:first:border-l-0 md:first:pl-0"
                        style="border-color: var(--line)"
                    >
                        <h3 class="text-lg font-semibold">{{ p.title }}</h3>
                        <p class="cin-muted mt-1">{{ p.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="px-6 py-8 text-sm cin-muted" style="border-top: 1px solid var(--line)">
            <div class="mx-auto max-w-6xl">{{ t('welcome.footer') }}</div>
        </footer>
    </div>
</template>
