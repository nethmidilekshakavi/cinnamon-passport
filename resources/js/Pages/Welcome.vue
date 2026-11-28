<script setup>
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    videos: { type: Array, default: () => [] },
    featured: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
});

// phones get horizontal strips, bigger screens get three columns
const isMobile = ref(false);
const check = () => {
    isMobile.value = window.innerWidth < 768;
};
onMounted(() => {
    check();
    window.addEventListener('resize', check);
});
onBeforeUnmount(() => window.removeEventListener('resize', check));

// Optional phone-only videos: public/videos/mobile/1.mp4, 2.mp4, 3.mp4 (if missing, the normal video is used)
const panels = computed(() =>
    props.videos
        .map((v) => (typeof v === 'string' ? v : (v.url ?? v.src ?? v.path ?? null)))
        .filter(Boolean)
        .slice(0, 3)
        .map((src, i) => ({
            key: src,
            sources: isMobile.value ? ['/videos/mobile/' + (i + 1) + '.mp4', src] : [src],
        })),
);

const count = computed(() => Math.max(panels.value.length, 1));
const gridStyle = computed(() =>
    isMobile.value
        ? { gridTemplateColumns: '1fr', gridTemplateRows: 'repeat(' + count.value + ', minmax(0, 1fr))' }
        : { gridTemplateRows: '1fr', gridTemplateColumns: 'repeat(' + count.value + ', minmax(0, 1fr))' },
);

const scrollDown = () => document.getElementById('join')?.scrollIntoView({ behavior: 'smooth' });

const points = [
    ['Quality facts you can read', 'Every product shows its grade, ingredients, quantity, production and expiry dates and batch code.'],
    ['Direct from Sri Lankan brands', 'Brands register with their location and sell sticks, powder, tea, oil, balm and perfume.'],
    ['Ratings from real buyers', 'Only buyers with a delivered order can rate a seller.'],
];
</script>

<template>
    <Head title="Welcome to Cinnamon Trust" />

    <div class="bg-[#FFF9EC] text-[#14241B]">
        <!-- PAGE 1: intro with three videos -->
        <section class="relative h-screen min-h-[560px] overflow-hidden bg-black text-white">
            <div class="absolute inset-0 grid bg-black" :style="gridStyle">
                <div v-for="p in panels" :key="p.key + (isMobile ? '-m' : '-d')" class="relative overflow-hidden">
                    <video autoplay muted loop playsinline preload="auto" class="absolute inset-0 h-full w-full object-cover">
                        <source v-for="s in p.sources" :key="s" :src="s" type="video/mp4" />
                    </video>
                </div>
                <div v-if="panels.length === 0" class="bg-gradient-to-br from-[#0B3B2C] via-[#14503A] to-[#8A5427]"></div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/45 to-black/75"></div>

            <header class="absolute inset-x-0 top-0 z-10">
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-4">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-full bg-white ring-2 ring-[#E3B04B]/70">
                            <img src="/images/welcome/logo2.png" alt="" class="h-full w-full object-cover mix-blend-multiply" />
                        </span>
                        <span class="font-bold">Cinnamon Trust</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm font-medium">
                        <ThemeToggle />
                        <Link :href="route('login')" class="rounded-full border border-white/50 px-4 py-1.5 hover:bg-white/15">Log in</Link>
                    </div>
                </div>
            </header>

            <div class="absolute inset-0 z-10 grid place-items-center px-4 text-center">
                <div class="max-w-3xl">
                    <p class="text-lg sm:text-2xl" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6);">Welcome to</p>
                    <h1 class="mt-1 font-extrabold leading-none text-[#E3B04B]" style="font-size: clamp(2.6rem, 9vw, 6rem); text-shadow: 0 4px 24px rgba(0,0,0,0.6);">Cinnamon Trust</h1>
                    <p class="mx-auto mt-5 max-w-xl text-base text-white/90 sm:text-lg" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6);">
                        The marketplace where Sri Lankan cinnamon brands meet buyers around the world.
                    </p>
                    <button type="button" @click="scrollDown"
                        class="mt-8 rounded-full bg-[#E3B04B] px-10 py-4 text-lg font-bold text-[#14241B] shadow-lg transition hover:-translate-y-0.5 hover:bg-[#f0c260]">
                        Get started
                    </button>
                </div>
            </div>

            <button type="button" @click="scrollDown" aria-label="Scroll down"
                class="absolute bottom-6 left-1/2 z-10 -translate-x-1/2 animate-bounce rounded-full bg-white/15 p-3 backdrop-blur hover:bg-white/25">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>
        </section>

        <!-- PAGE 2: about the app, then choose buyer or seller -->
        <section id="join" class="relative overflow-hidden bg-[#0B3B2C] py-16 text-white sm:py-24">
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#E3B04B]/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-6xl px-4">
                <p class="text-sm font-semibold uppercase tracking-wider text-[#E3B04B]">About Cinnamon Trust</p>
                <h2 class="mt-3 max-w-3xl text-3xl font-extrabold leading-tight sm:text-5xl">
                    Real Ceylon cinnamon, with facts you can check
                </h2>
                <p class="mt-4 max-w-2xl text-base text-white/80 sm:text-lg">
                    Cinnamon Trust helps Sri Lankan cinnamon brands sell their products online, and helps buyers
                    compare quality instead of guessing.
                </p>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <div v-for="[title, text] in points" :key="title" class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
                        <h3 class="font-bold text-[#E3B04B]">{{ title }}</h3>
                        <p class="mt-2 text-sm text-white/75">{{ text }}</p>
                    </div>
                </div>

                <h3 class="mt-16 text-2xl font-bold sm:text-3xl">Start here: are you a buyer or a seller?</h3>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl bg-white p-6 text-[#14241B] shadow-2xl shadow-black/20 sm:p-8">
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#B5832F]">I want to buy</p>
                        <h4 class="mt-2 text-2xl font-extrabold">I am a buyer</h4>
                        <ul class="mt-4 space-y-2 text-sm text-[#14241B]/75">
                            <li>&#10003; Browse products from Sri Lankan brands</li>
                            <li>&#10003; Compare grade, ingredients and expiry dates</li>
                            <li>&#10003; Order online and rate the seller</li>
                        </ul>
                        <Link :href="route('start', 'buyer')"
                            class="mt-6 inline-block w-full rounded-full bg-[#0B3B2C] px-6 py-3 text-center font-semibold text-white hover:bg-[#14503A] sm:w-auto">
                            Create a buyer account
                        </Link>
                    </div>

                    <div class="rounded-3xl bg-[#E3B04B] p-6 text-[#14241B] shadow-2xl shadow-black/20 sm:p-8">
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#6B4E16]">I want to sell</p>
                        <h4 class="mt-2 text-2xl font-extrabold">I am a seller</h4>
                        <ul class="mt-4 space-y-2 text-sm text-[#14241B]/80">
                            <li>&#10003; Register your brand and its location</li>
                            <li>&#10003; List products with quality facts</li>
                            <li>&#10003; Receive orders and build your rating</li>
                        </ul>
                        <Link :href="route('start', 'seller')"
                            class="mt-6 inline-block w-full rounded-full bg-[#0B3B2C] px-6 py-3 text-center font-semibold text-white hover:bg-[#14503A] sm:w-auto">
                            Create a seller account
                        </Link>
                    </div>
                </div>

                <p class="mt-8 text-center text-white/80">
                    Already have an account?
                    <Link :href="route('login')" class="font-semibold text-[#E3B04B] underline">Log in</Link>
                    and we will take you to your pages.
                </p>
            </div>
        </section>

        <footer class="bg-[#082A20] py-5 text-center text-xs text-white/60">
            Cinnamon Trust is a student prototype. Sellers and products shown are sample data.
        </footer>
    </div>
</template>