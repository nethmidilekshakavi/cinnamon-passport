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

const logo = '/images/welcome/logo2.png';

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

// A small fixed pattern that looks like a QR code (decorative).
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

// Line icons: leaf (grower), swap arrows (collector), shield with tick (buyer).
const roleIcons = [
    'M5 19c0-9 5-14 14-14 0 9-5 14-14 14zM5 19l8-8',
    'M4 8h14l-3-3M20 16H6l3 3',
    'M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6zM9 12l2 2 4-4',
];
const roles = computed(() =>
    [1, 2, 3].map((n) => ({
        title: t(`welcome.role${n}.title`),
        text: t(`welcome.role${n}.text`),
        icon: roleIcons[n - 1],
    })),
);

// Palette: deep #0B3B2C, green #14503A, gold #E3B04B, cream #FFF9EE, ink #14241B, mint #DDEFE3, line #E8DFC9
const btnGold =
    'inline-flex min-h-[48px] w-full items-center justify-center rounded-full bg-[#E3B04B] px-7 font-bold text-[#14241B] transition hover:bg-[#F0C566] active:scale-[.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:w-auto';
</script>

<template>
    <Head :title="t('welcome.pageTitle')" />

    <SplashScreen :show="showSplash" />

    <Transition name="cin-fade">
        <LanguagePicker v-if="showPicker" />
    </Transition>

    <div class="min-h-screen bg-[#FFF9EE] text-[#14241B]">
        <!-- Top: nav and hero on one flat colour -->
        <div class="bg-[#0B3B2C] text-white">
            <nav class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-4 gap-y-3 px-5 py-4 sm:px-6">
                <Link href="/" class="flex items-center gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full bg-white">
                        <img :src="logo" alt="" width="40" height="40" class="h-10 w-10 object-cover mix-blend-multiply" />
                    </span>
                    <span class="leading-tight">
                        <span class="font-display block text-lg font-bold">Cinnamon Trust</span>
                        <span class="block text-[11px] tracking-wide text-white/60">{{ t('welcome.brandSub') }}</span>
                    </span>
                </Link>

                <div class="flex items-center gap-3 text-sm">
                    <Link v-if="user" :href="route('dashboard')" class="rounded-full bg-[#E3B04B] px-4 py-2 font-bold text-[#14241B]">
                        {{ t('nav.openArea') }}
                    </Link>
                    <template v-else>
                        <Link v-if="canLogin" :href="route('login')" class="px-2 py-2 font-medium text-white/85 hover:text-white">
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

            <section class="mx-auto grid max-w-6xl items-center gap-12 px-5 pb-14 pt-8 sm:px-6 md:grid-cols-2 md:gap-16 md:pb-24 md:pt-16">
                <div>
                    <p class="inline-block rounded-full border border-white/25 px-3 py-1 text-sm text-white/80">
                        {{ t('welcome.badge') }}
                    </p>
                    <h1 class="font-display cin-h1 mt-5 text-[2.1rem] font-bold tracking-tight sm:text-5xl lg:text-6xl">
                        {{ t('welcome.h1') }}
                    </h1>
                    <p class="mt-5 max-w-md text-base text-white/75 sm:text-lg">
                        {{ t('welcome.intro') }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-5">
                        <Link v-if="user" :href="route('dashboard')" :class="btnGold">{{ t('nav.openArea') }}</Link>
                        <Link v-else-if="canRegister" :href="route('register')" :class="btnGold">
                            {{ t('welcome.createAccount') }}
                        </Link>
                        <Link
                            :href="route('verify.index')"
                            class="inline-flex min-h-[48px] items-center justify-center font-semibold text-[#F0C566] hover:text-white"
                        >
                            {{ t('welcome.ctaButton') }} →
                        </Link>
                    </div>
                </div>

                <!-- Sample passport -->
                <div class="mx-auto w-full max-w-sm">
                    <div class="rounded-2xl bg-white p-6 text-[#14241B] shadow-2xl shadow-black/30">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm text-[#14241B]/55">{{ t('welcome.sampleTitle') }}</p>
                                <p class="font-display mt-1 text-3xl font-bold">CIN-0042</p>
                            </div>
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

                        <dl class="mt-5 divide-y divide-[#E8DFC9] text-sm">
                            <div v-for="(f, i) in sampleFields" :key="i" class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-[#14241B]/55">{{ f.label }}</dt>
                                <dd class="font-semibold">{{ f.value }}</dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#DDEFE3] px-3 py-1 text-sm font-semibold text-[#14503A]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M5 12l5 5 9-10" />
                                </svg>
                                {{ t('welcome.seal') }}
                            </span>
                            <p class="min-w-0 truncate font-mono text-[10px] tracking-wider text-[#14241B]/40">{{ mrz }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- How a batch travels -->
        <section class="mx-auto max-w-6xl px-5 py-14 sm:px-6 md:py-20">
            <h2 class="font-display max-w-2xl text-2xl font-bold tracking-tight sm:text-4xl">{{ t('welcome.travelTitle') }}</h2>
            <ol class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <li v-for="(s, i) in steps" :key="i">
                    <span class="font-display grid h-10 w-10 place-items-center rounded-full border-2 border-[#14503A] font-bold text-[#14503A]">
                        {{ i + 1 }}
                    </span>
                    <h3 class="mt-4 text-lg font-semibold">{{ s.title }}</h3>
                    <p class="mt-1 text-sm text-[#14241B]/65">{{ s.text }}</p>
                </li>
            </ol>
        </section>

        <!-- SMS -->
        <section class="border-y border-[#E8DFC9] bg-white">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-5 py-14 sm:px-6 md:grid-cols-2 md:py-20">
                <div>
                    <h2 class="font-display text-2xl font-bold tracking-tight sm:text-4xl">{{ t('welcome.smsTitle') }}</h2>
                    <p class="mt-4 max-w-md text-[#14241B]/70">{{ t('welcome.smsText') }}</p>
                    <ul class="mt-6 flex flex-wrap gap-2">
                        <li v-for="c in chips" :key="c" class="rounded-full bg-[#DDEFE3] px-3.5 py-1.5 text-sm font-medium text-[#14503A]">
                            {{ c }}
                        </li>
                    </ul>
                </div>

                <div class="mx-auto w-full max-w-[290px] rounded-[2rem] border border-[#E8DFC9] bg-[#FFF9EE] p-4 shadow-xl shadow-[#0B3B2C]/10">
                    <p class="pb-2 text-center text-xs font-semibold text-[#14241B]/45">Messages</p>
                    <div class="space-y-3">
                        <div class="flex justify-end">
                            <p class="max-w-[88%] break-words rounded-2xl rounded-br-sm bg-[#14503A] px-3 py-2 font-mono text-[13px] text-white">
                                BATCH P1 40KG 12OCT DRY3 ALBA
                            </p>
                        </div>
                        <div class="flex justify-start">
                            <p class="max-w-[88%] rounded-2xl rounded-bl-sm bg-white px-3 py-2 text-[13px] ring-1 ring-[#E8DFC9]">
                                OK CIN-0042 saved: P1, 40kg, 12 Oct, dried 3 days.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Roles -->
        <section class="mx-auto max-w-6xl px-5 py-14 sm:px-6 md:py-20">
            <h2 class="font-display max-w-2xl text-2xl font-bold tracking-tight sm:text-4xl">{{ t('welcome.rolesTitle') }}</h2>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <div v-for="r in roles" :key="r.title" class="rounded-2xl border border-[#E8DFC9] bg-white p-6">
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-[#DDEFE3] text-[#14503A]">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path :d="r.icon" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-lg font-semibold">{{ r.title }}</h3>
                    <p class="mt-1 text-sm text-[#14241B]/65">{{ r.text }}</p>
                </div>
            </div>
        </section>

        <!-- Built for -->
        <section class="border-y border-[#E8DFC9] bg-white">
            <div class="mx-auto max-w-6xl px-5 py-14 sm:px-6 md:py-20">
                <h2 class="font-display max-w-2xl text-2xl font-bold tracking-tight sm:text-4xl">{{ t('welcome.builtTitle') }}</h2>
                <div class="mt-10 grid gap-8 md:grid-cols-3">
                    <div v-for="(p, i) in points" :key="i">
                        <p class="font-display text-3xl font-bold text-[#E3B04B]">0{{ i + 1 }}</p>
                        <h3 class="mt-2 text-lg font-semibold">{{ p.title }}</h3>
                        <p class="mt-1 text-sm text-[#14241B]/65">{{ p.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Verify call to action -->
        <section class="bg-[#0B3B2C] text-white">
            <div class="mx-auto max-w-3xl px-5 py-14 text-center sm:px-6 md:py-20">
                <h2 class="font-display text-2xl font-bold tracking-tight sm:text-4xl">{{ t('welcome.ctaTitle') }}</h2>
                <p class="mx-auto mt-4 max-w-lg text-white/75">{{ t('welcome.ctaText') }}</p>
                <Link :href="route('verify.index')" :class="[btnGold, 'mt-8 sm:w-auto']">
                    {{ t('welcome.ctaButton') }}
                </Link>
            </div>
        </section>

        <footer class="px-5 py-8 text-sm text-[#14241B]/60 sm:px-6">
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-3 text-center sm:flex-row sm:text-left">
                <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full bg-white ring-1 ring-[#E8DFC9]">
                    <img :src="logo" alt="" width="40" height="40" class="h-10 w-10 object-cover mix-blend-multiply" />
                </span>
                <p>
                    <span class="font-semibold text-[#14241B]">Cinnamon Trust</span> · {{ t('welcome.brandSub') }}<br />
                    {{ t('welcome.footer') }}
                </p>
            </div>
        </footer>
    </div>
</template>
