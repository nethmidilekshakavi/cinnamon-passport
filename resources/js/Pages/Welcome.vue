<script setup>
import CategoryArt from '@/Components/Market/CategoryArt.vue';
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    videos: { type: Array, default: () => [] },
    featured: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({ products: 0, brands: 0 }) },
});

const user = computed(() => usePage().props.auth?.user ?? null);

const panels = computed(() =>
    props.videos
        .map((v) => (typeof v === 'string' ? v : (v.url ?? v.src ?? v.path ?? null)))
        .filter(Boolean)
        .slice(0, 3),
);

const role = ref(null);
onMounted(async () => {
    if (!user.value) return;
    try {
        const r = await fetch(route('me.role'), { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        if (r.ok) role.value = await r.json();
    } catch (e) {
        role.value = null;
    }
});
const brand = computed(() => role.value?.brand ?? null);

const scrollDown = () => document.getElementById('join')?.scrollIntoView({ behavior: 'smooth' });

const categories = [
    { name: 'Cinnamon sticks', slug: 'cinnamon-sticks', tone: 'from-[#D79A5B] to-[#8A5427]' },
    { name: 'Powder', slug: 'cinnamon-powder', tone: 'from-[#E0AE74] to-[#A86A32]' },
    { name: 'Tea', slug: 'cinnamon-tea', tone: 'from-[#A9C486] to-[#4F7A4B]' },
    { name: 'Oil', slug: 'cinnamon-oil', tone: 'from-[#EBC066] to-[#B5832F]' },
    { name: 'Balm', slug: 'cinnamon-balm', tone: 'from-[#CDB892] to-[#8C7550]' },
    { name: 'Perfume', slug: 'cinnamon-perfume', tone: 'from-[#C29AB5] to-[#6F4A68]' },
];

const steps = [
    ['1', 'Browse', 'Search sticks, powder, tea, oil, balm and perfume. Filter by grade, quality, price and seller location.'],
    ['2', 'Compare the facts', 'Every product lists grade, ingredients, dates and batch code, declared by the seller.'],
    ['3', 'Order from the brand', 'Order directly from the Sri Lankan brand that made it, then rate the seller after delivery.'],
];

const reasons = [
    ['Grade', 'Quill grades such as Alba, C5, C4 and H1. Thinner quills are a higher grade.'],
    ['Ingredients', 'The full ingredient list, so you know exactly what is inside.'],
    ['Dates', 'Production and expiry dates, with a clear warning when a product has expired.'],
    ['Origin and growing', 'Batch code, organic or not, and how the cinnamon was grown.'],
];
</script>

<template>
    <Head title="Ceylon cinnamon marketplace" />

    <div class="bg-[#FFF9EC] text-[#14241B]">
        <!-- PAGE 1: video intro -->
        <section class="relative h-screen min-h-[560px] overflow-hidden bg-[#0B3B2C] text-white">
            <div class="absolute inset-0 grid gap-1.5 p-1.5"
                :style="{ gridTemplateColumns: 'repeat(' + Math.max(panels.length, 1) + ', minmax(0, 1fr))' }">
                <div v-for="(src, i) in panels" :key="src" class="overflow-hidden rounded-2xl" :class="i > 0 ? 'hidden md:block' : ''">
                    <video :src="src" autoplay muted loop playsinline class="h-full w-full object-cover"></video>
                </div>
                <div v-if="panels.length === 0" class="rounded-2xl bg-gradient-to-br from-[#0B3B2C] via-[#14503A] to-[#8A5427]"></div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black/70"></div>

            <!-- top nav -->
            <header class="absolute inset-x-0 top-0 z-10">
                <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
                    <Link href="/" class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-full bg-white ring-2 ring-[#E3B04B]/70">
                            <img src="/images/welcome/logo2.png" alt="" class="h-full w-full object-cover mix-blend-multiply" />
                        </span>
                        <span class="font-bold">Cinnamon Trust</span>
                    </Link>
                    <nav class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm font-medium">
                        <Link :href="route('market.index')" class="hover:text-[#E3B04B]">Marketplace</Link>
                        <Link :href="route('seller.create')" class="hidden hover:text-[#E3B04B] sm:inline">Sell on Cinnamon Trust</Link>
                        <template v-if="user">
                            <Link :href="route('orders.index')" class="hover:text-[#E3B04B]">My orders</Link>
                            <Link :href="route('profile.edit')" class="rounded-full bg-[#E3B04B] px-4 py-1.5 font-semibold text-[#14241B]">{{ user.name }}</Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="hover:text-[#E3B04B]">Log in</Link>
                            <Link :href="route('register')" class="rounded-full bg-[#E3B04B] px-4 py-1.5 font-semibold text-[#14241B] hover:bg-[#f0c260]">Register</Link>
                        </template>
                    </nav>
                </div>
            </header>

            <!-- centre text -->
            <div class="absolute inset-0 z-10 grid place-items-center px-4 text-center">
                <div class="max-w-3xl">
                    <p class="text-lg text-white sm:text-2xl" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6);">Welcome to</p>
                    <h1 class="mt-1 font-extrabold leading-none text-[#E3B04B]" style="font-size: clamp(3rem, 9vw, 6rem); text-shadow: 0 4px 24px rgba(0,0,0,0.6);">Cinnamon Trust</h1>
                    <p class="mx-auto mt-5 max-w-xl text-lg text-white/90 drop-shadow">
                        Ceylon cinnamon from Sri Lankan brands, with the quality facts you can check.
                    </p>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        <Link :href="route('market.index')"
                            class="rounded-full bg-[#E3B04B] px-8 py-3.5 font-bold text-[#14241B] shadow-lg transition hover:-translate-y-0.5 hover:bg-[#f0c260]">
                            Browse the marketplace
                        </Link>
                        <Link :href="route('seller.create')"
                            class="rounded-full border border-white/60 px-8 py-3.5 font-semibold backdrop-blur transition hover:bg-white/15">
                            Sell on Cinnamon Trust
                        </Link>
                    </div>
                </div>
            </div>

            <button type="button" @click="scrollDown" aria-label="Scroll down"
                class="absolute bottom-6 left-1/2 z-10 -translate-x-1/2 animate-bounce rounded-full bg-white/15 p-3 backdrop-blur hover:bg-white/25">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9l6 6 6-6" />
                </svg>
            </button>
        </section>

        <!-- PAGE 2: about the app, then the buyer / seller paths -->
        <section id="join" class="relative min-h-screen bg-[#0B3B2C] py-20 text-white">
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#E3B04B]/20 blur-3xl"></div>
            <div class="relative mx-auto max-w-6xl px-4">
                <p class="text-sm font-semibold uppercase tracking-wider text-[#E3B04B]">About Cinnamon Trust</p>
                <h2 class="mt-3 max-w-3xl text-4xl font-extrabold leading-tight sm:text-5xl">
                    A marketplace for Sri Lankan cinnamon brands and buyers around the world
                </h2>
                <p class="mt-4 max-w-2xl text-lg text-white/80">
                    Brands register, list sticks, powder, tea, oil, balm and perfume with their quality facts,
                    and buyers order directly and rate the seller after delivery.
                </p>

                <dl class="mt-8 flex flex-wrap gap-x-10 gap-y-4">
                    <div><dt class="text-xs uppercase tracking-wider text-white/60">Products</dt><dd class="text-2xl font-extrabold">{{ stats.products }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wider text-white/60">Brands</dt><dd class="text-2xl font-extrabold">{{ stats.brands }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wider text-white/60">Categories</dt><dd class="text-2xl font-extrabold">6</dd></div>
                </dl>

                <h3 class="mt-14 text-2xl font-bold">How do you want to use it?</h3>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <!-- Buyer -->
                    <div class="relative rounded-3xl bg-white p-7 text-[#14241B] shadow-2xl shadow-black/20">
                        <span v-if="user && !brand" class="absolute right-5 top-5 rounded-full bg-[#E3B04B] px-3 py-1 text-xs font-bold">Your area</span>
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#B5832F]">I want to buy</p>
                        <h4 class="mt-2 text-2xl font-extrabold">Buyer</h4>
                        <ul class="mt-4 space-y-2 text-sm text-[#14241B]/75">
                            <li>&#10003; Search and filter by grade, quality, price and location</li>
                            <li>&#10003; Read the quality facts before you order</li>
                            <li>&#10003; Order from the brand and rate the seller</li>
                        </ul>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <Link :href="route('market.index')" class="rounded-full bg-[#0B3B2C] px-6 py-3 font-semibold text-white hover:bg-[#14503A]">Browse the marketplace</Link>
                            <Link v-if="user" :href="route('orders.index')" class="rounded-full border-2 border-[#0B3B2C] px-6 py-3 font-semibold text-[#0B3B2C] hover:bg-[#0B3B2C] hover:text-white">My orders</Link>
                            <template v-else>
                                <Link :href="route('register')" class="rounded-full border-2 border-[#0B3B2C] px-6 py-3 font-semibold text-[#0B3B2C] hover:bg-[#0B3B2C] hover:text-white">Create an account</Link>
                                <Link :href="route('login')" class="px-3 py-3 font-semibold text-[#14503A] underline">Log in</Link>
                            </template>
                        </div>
                    </div>

                    <!-- Seller -->
                    <div class="relative rounded-3xl bg-[#E3B04B] p-7 text-[#14241B] shadow-2xl shadow-black/20">
                        <span v-if="user && brand" class="absolute right-5 top-5 rounded-full bg-[#0B3B2C] px-3 py-1 text-xs font-bold text-white">Your area</span>
                        <p class="text-sm font-semibold uppercase tracking-wider text-[#6B4E16]">I want to sell</p>
                        <h4 class="mt-2 text-2xl font-extrabold">Seller</h4>
                        <template v-if="brand">
                            <p class="mt-4 text-sm font-semibold">{{ brand.name }}</p>
                            <ul class="mt-2 space-y-2 text-sm text-[#14241B]/80">
                                <li>&#10003; Add and manage your products</li>
                                <li>&#10003; Ship the orders you receive</li>
                                <li>&#10003; See your seller ratings</li>
                            </ul>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Link :href="route('seller.dashboard')" class="rounded-full bg-[#0B3B2C] px-6 py-3 font-semibold text-white hover:bg-[#14503A]">Seller dashboard</Link>
                                <Link :href="route('seller.products.create')" class="rounded-full border-2 border-[#0B3B2C] px-6 py-3 font-semibold text-[#0B3B2C] hover:bg-[#0B3B2C] hover:text-white">Add product</Link>
                                <Link :href="route('seller.orders.index')" class="rounded-full border-2 border-[#0B3B2C] px-6 py-3 font-semibold text-[#0B3B2C] hover:bg-[#0B3B2C] hover:text-white">Orders</Link>
                            </div>
                        </template>
                        <template v-else>
                            <ul class="mt-4 space-y-2 text-sm text-[#14241B]/80">
                                <li>&#10003; Register your brand with its location</li>
                                <li>&#10003; List products with ingredients, quantity, quality and dates</li>
                                <li>&#10003; Reach buyers worldwide</li>
                            </ul>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <Link :href="route('seller.create')" class="rounded-full bg-[#0B3B2C] px-6 py-3 font-semibold text-white hover:bg-[#14503A]">
                                    {{ user ? 'Open your brand' : 'Start selling' }}
                                </Link>
                                <Link v-if="!user" :href="route('login')" class="px-3 py-3 font-semibold text-[#14241B] underline">Log in</Link>
                            </div>
                        </template>
                    </div>
                </div>

                <p class="mt-6 text-sm text-white/60">
                    One account works for both. You do not choose a role when you register: you become a seller when you open a brand.
                </p>
            </div>
        </section>

        <!-- Categories -->
        <section class="mx-auto max-w-6xl px-4 py-16">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Shop by category</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                <Link v-for="c in categories" :key="c.slug" :href="route('market.index', { category: c.slug })"
                    class="group flex flex-col items-center rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br transition group-hover:scale-110" :class="c.tone">
                        <CategoryArt :kind="c.slug" class="h-9 w-9 text-white/95" />
                    </span>
                    <span class="mt-3 text-sm font-semibold">{{ c.name }}</span>
                </Link>
            </div>
        </section>

        <!-- Featured -->
        <section v-if="featured.length" class="mx-auto max-w-6xl px-4 pb-16">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold sm:text-3xl">Fresh on the marketplace</h2>
                    <p class="mt-1 text-sm text-[#14241B]/60">{{ stats.products }} products from {{ stats.brands }} brands</p>
                </div>
                <Link :href="route('market.index')" class="text-sm font-semibold text-[#14503A] underline">See all products</Link>
            </div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="f in featured" :key="f.id" :product="f" />
            </div>
        </section>

        <!-- How it works -->
        <section class="bg-white py-16">
            <div class="mx-auto max-w-6xl px-4">
                <h2 class="text-2xl font-extrabold sm:text-3xl">How it works</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <div v-for="[n, title, text] in steps" :key="n" class="rounded-2xl bg-[#FFF9EC] p-6 ring-1 ring-[#EFE4C8]">
                        <span class="grid h-11 w-11 place-items-center rounded-full bg-[#0B3B2C] font-bold text-[#E3B04B]">{{ n }}</span>
                        <h3 class="mt-4 text-lg font-bold">{{ title }}</h3>
                        <p class="mt-2 text-sm text-[#14241B]/70">{{ text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quality facts -->
        <section class="mx-auto max-w-6xl px-4 py-16">
            <h2 class="text-2xl font-extrabold sm:text-3xl">Quality you can read</h2>
            <p class="mt-2 max-w-2xl text-[#14241B]/70">
                Cinnamon quality varies a lot. Every product page shows the facts below, so buyers can compare
                products instead of guessing. The facts are declared by the seller.
            </p>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="[title, text] in reasons" :key="title"
                    class="rounded-2xl border-l-4 border-[#E3B04B] bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <h3 class="font-bold text-[#0B3B2C]">{{ title }}</h3>
                    <p class="mt-1 text-sm text-[#14241B]/70">{{ text }}</p>
                </div>
            </div>
        </section>

        <!-- Seller call to action -->
        <section class="bg-[#0B3B2C] py-14 text-white">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-6 px-4">
                <div>
                    <h2 class="text-2xl font-extrabold">Run a cinnamon brand?</h2>
                    <p class="mt-1 max-w-xl text-white/75">
                        Register your brand, list your products with their quality facts, and reach buyers around the world.
                    </p>
                </div>
                <Link :href="route('seller.create')"
                    class="rounded-full bg-[#E3B04B] px-8 py-3.5 font-bold text-[#14241B] transition hover:-translate-y-0.5 hover:bg-[#f0c260]">
                    Open your brand
                </Link>
            </div>
        </section>

        <footer class="bg-[#082A20] py-5 text-center text-xs text-white/60">
            Cinnamon Trust is a student prototype. Sellers and products shown are sample data.
        </footer>
    </div>
</template>