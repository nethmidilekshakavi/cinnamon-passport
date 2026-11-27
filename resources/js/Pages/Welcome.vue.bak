<script setup>
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    videos: { type: Array, default: () => [] },
    featured: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({ products: 0, brands: 0 }) },
});

const user = computed(() => usePage().props.auth?.user ?? null);

const scrollDown = () => {
    document.getElementById('content')?.scrollIntoView({ behavior: 'smooth' });
};

const qualityCards = [
    { title: 'Grade', text: 'Quill grades such as Alba, C5, C4 and H1. Thinner quills are a higher grade.' },
    { title: 'Ingredients', text: 'The full ingredient list, so you know exactly what is inside.' },
    { title: 'Dates', text: 'Production and expiry dates, with a clear warning when a product has expired.' },
    { title: 'Origin and growing', text: 'Batch code, organic or not, and how the cinnamon was grown.' },
];

const steps = [
    { n: 1, title: 'Browse', text: 'Search sticks, powder, tea, oil, balm and perfume. Filter by grade, quality, price and seller location.' },
    { n: 2, title: 'Compare quality facts', text: 'Every product page lists the facts that decide quality, declared by the seller.' },
    { n: 3, title: 'Order from the brand', text: 'Order directly from the Sri Lankan brand that made it.' },
];
</script>

<template>
    <Head title="Cinnamon Trust" />

    <!-- Hero: video intro -->
    <section class="fvi relative h-screen min-h-[560px] w-full overflow-hidden bg-black">
        <div
            class="absolute inset-0 grid gap-1 p-1 sm:gap-1.5 sm:p-1.5"
            :style="{ gridTemplateColumns: 'repeat(' + Math.max(videos.length, 1) + ', minmax(0, 1fr))' }"
        >
            <div
                v-for="(src, i) in videos"
                :key="src"
                class="fvi-panel overflow-hidden rounded-lg bg-[#14503A] sm:rounded-xl"
                :style="{ '--i': i }"
            >
                <video
                    class="block h-full w-full object-cover"
                    :src="src"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="auto"
                    aria-hidden="true"
                />
            </div>
            <div v-if="videos.length === 0" class="rounded-xl bg-gradient-to-br from-[#0B3B2C] to-[#1E6B4C]"></div>
        </div>

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/55 via-black/30 to-black/65"></div>

        <!-- Nav -->
        <div class="fvi-top relative z-10">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4 text-white">
                <Link href="/" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-full bg-white ring-2 ring-[#E3B04B]/60">
                        <img src="/images/welcome/logo2.png" alt="" class="h-full w-full object-cover mix-blend-multiply" />
                    </span>
                    <span class="font-bold">Cinnamon Trust</span>
                </Link>

                <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                    <Link :href="route('market.index')" class="hover:text-[#E3B04B]">Marketplace</Link>
                    <Link :href="route('seller.create')" class="hover:text-[#E3B04B]">Sell on Cinnamon Trust</Link>
                    <template v-if="user">
                        <Link :href="route('profile.edit')" class="hover:text-[#E3B04B]">{{ user.name }}</Link>
                        <Link :href="route('logout')" method="post" as="button" class="hover:text-[#E3B04B]">Log out</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="hover:text-[#E3B04B]">Log in</Link>
                        <Link :href="route('register')" class="rounded-full bg-[#E3B04B] px-4 py-1.5 font-semibold text-[#14241B]">Register</Link>
                    </template>
                </nav>
            </div>
        </div>

        <!-- Welcome text -->
        <div class="pointer-events-none absolute inset-0 z-10 grid place-items-center px-5">
            <h1 class="fvi-welcome text-center text-white">
                <span class="block text-lg tracking-wide text-white/85 sm:text-2xl">Welcome to</span>
                <span class="font-display block text-5xl font-bold tracking-tight text-[#E3B04B] drop-shadow-lg sm:text-7xl lg:text-8xl">
                    Cinnamon Trust
                </span>
                <span class="mx-auto mt-4 block max-w-xl text-base text-white/85 sm:text-lg">
                    Ceylon cinnamon from Sri Lankan brands, with the quality facts you can check.
                </span>
            </h1>
        </div>

        <!-- Buttons + scroll hint -->
        <div class="fvi-bottom absolute inset-x-0 bottom-0 z-10 flex flex-col items-center gap-5 px-5 pb-8">
            <div class="flex flex-wrap justify-center gap-3">
                <Link :href="route('market.index')"
                    class="rounded-full bg-[#E3B04B] px-8 py-3 font-bold text-[#14241B] shadow-lg transition hover:bg-[#d49f3c]">
                    Browse the marketplace
                </Link>
                <Link :href="route('seller.create')"
                    class="rounded-full border border-white/70 px-8 py-3 font-semibold text-white transition hover:bg-white/10">
                    Sell on Cinnamon Trust
                </Link>
            </div>

            <button type="button" aria-label="Scroll down" class="fvi-bounce text-white/80 hover:text-white" @click="scrollDown">
                <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>
        </div>
    </section>

    <!-- Content below the hero -->
    <div id="content" class="bg-[#FFF9EC] text-[#14241B]">
        <!-- Featured products -->
        <section v-if="featured.length" class="mx-auto max-w-6xl px-4 py-14">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-extrabold">Fresh on the marketplace</h2>
                    <p v-if="stats.products > 0" class="mt-1 text-sm text-[#14241B]/60">
                        {{ stats.products }} product<span v-if="stats.products !== 1">s</span>
                        from {{ stats.brands }} brand<span v-if="stats.brands !== 1">s</span>
                    </p>
                </div>
                <Link :href="route('market.index')" class="text-sm font-semibold text-[#14503A] underline">See all products</Link>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="p in featured" :key="p.id" :product="p" />
            </div>
        </section>

        <!-- How it works -->
        <section class="mx-auto max-w-6xl px-4 py-14">
            <h2 class="text-2xl font-extrabold">How it works</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-3">
                <div v-for="s in steps" :key="s.n" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <span class="grid h-10 w-10 place-items-center rounded-full border-2 border-[#14503A] font-bold text-[#14503A]">{{ s.n }}</span>
                    <h3 class="mt-4 text-lg font-bold">{{ s.title }}</h3>
                    <p class="mt-1 text-sm text-[#14241B]/70">{{ s.text }}</p>
                </div>
            </div>
        </section>

        <!-- Quality facts -->
        <section class="bg-white py-14">
            <div class="mx-auto max-w-6xl px-4">
                <h2 class="text-2xl font-extrabold">Quality you can read</h2>
                <p class="mt-1 max-w-2xl text-[#14241B]/70">
                    Cinnamon quality varies a lot. Every product page shows the facts below, so buyers can compare
                    products instead of guessing. The facts are declared by the seller.
                </p>
                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="c in qualityCards" :key="c.title" class="rounded-2xl bg-[#FFF9EC] p-5 ring-1 ring-[#E8DFC9]">
                        <h3 class="font-bold text-[#14503A]">{{ c.title }}</h3>
                        <p class="mt-1 text-sm text-[#14241B]/70">{{ c.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Join -->
        <section id="join" class="mx-auto max-w-6xl px-4 py-14">
            <template v-if="!user">
                <h2 class="text-2xl font-extrabold">Join Cinnamon Trust</h2>
                <p class="mt-1 max-w-2xl text-[#14241B]/70">
                    One account for everyone. Buy Ceylon cinnamon products today, and open your own brand
                    whenever you are ready to sell.
                </p>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <h3 class="text-lg font-bold">New here?</h3>
                        <p class="mt-1 text-sm text-[#14241B]/70">Create a free account. It takes about a minute.</p>
                        <Link :href="route('register')"
                            class="mt-5 inline-block rounded-full bg-[#14503A] px-8 py-3 font-bold text-white transition hover:bg-[#0B3B2C]">
                            Create an account
                        </Link>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <h3 class="text-lg font-bold">Already have an account?</h3>
                        <p class="mt-1 text-sm text-[#14241B]/70">Log in to order, or to manage your brand and products.</p>
                        <Link :href="route('login')"
                            class="mt-5 inline-block rounded-full border-2 border-[#14503A] px-8 py-3 font-bold text-[#14503A] transition hover:bg-[#14503A] hover:text-white">
                            Log in
                        </Link>
                    </div>
                </div>
            </template>

            <template v-else>
                <h2 class="text-2xl font-extrabold">Welcome back, {{ user.name }}</h2>
                <p class="mt-1 text-[#14241B]/70">Pick up where you left off.</p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <Link :href="route('market.index')"
                        class="rounded-full bg-[#14503A] px-8 py-3 font-bold text-white transition hover:bg-[#0B3B2C]">
                        Browse the marketplace
                    </Link>
                    <Link :href="route('seller.create')"
                        class="rounded-full border-2 border-[#14503A] px-8 py-3 font-bold text-[#14503A] transition hover:bg-[#14503A] hover:text-white">
                        Open or manage your brand
                    </Link>
                </div>
            </template>
        </section>

        <!-- Seller call to action -->
        <section class="bg-[#14503A] py-14 text-white">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-6 px-4">
                <div>
                    <h2 class="text-2xl font-extrabold">Run a cinnamon brand?</h2>
                    <p class="mt-1 max-w-xl text-white/80">
                        Register your brand, list your products with their quality facts, and reach buyers around the world.
                    </p>
                </div>
                <Link :href="route('seller.create')"
                    class="rounded-full bg-[#E3B04B] px-8 py-3 font-bold text-[#14241B] hover:bg-[#d49f3c]">
                    Open your brand
                </Link>
            </div>
        </section>

        <footer class="bg-[#0B3B2C] py-6 text-center text-xs text-white/70">
            Cinnamon Trust is a student prototype. Sellers and products shown are sample data.
        </footer>
    </div>
</template>

<style scoped>
.fvi {
    height: 100vh;
    height: 100svh;
}

/* Panels fly in from the right: 0.2s, 0.55s, 0.9s */
.fvi-panel {
    opacity: 0;
    transform: translateX(120%);
    animation: fvi-in 1s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: calc(var(--i) * 0.35s + 0.2s);
}

/* Welcome text wipes in after the last panel lands */
.fvi-welcome {
    opacity: 0;
    clip-path: inset(0 100% 0 0);
    animation: fvi-text 1.1s ease-out 2s forwards;
}

/* Buttons fade up last */
.fvi-bottom {
    opacity: 0;
    transform: translateY(16px);
    animation: fvi-up 0.8s ease-out 3s forwards;
}

.fvi-top {
    opacity: 0;
    animation: fvi-fade 0.8s ease-out 0.4s forwards;
}

.fvi-bounce {
    animation: fvi-bob 1.6s ease-in-out infinite;
}

@keyframes fvi-in {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
@keyframes fvi-text {
    to {
        opacity: 1;
        clip-path: inset(0 0 0 0);
    }
}
@keyframes fvi-up {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fvi-fade {
    to {
        opacity: 1;
    }
}
@keyframes fvi-bob {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(6px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .fvi-panel,
    .fvi-welcome,
    .fvi-bottom,
    .fvi-top {
        animation: none;
        opacity: 1;
        transform: none;
        clip-path: none;
    }
    .fvi-bounce {
        animation: none;
    }
}
</style>