<script setup>
import CategoryArt from '@/Components/Market/CategoryArt.vue';
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    videos: { type: Array, default: () => [] },
    featured: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({ products: 0, brands: 0 }) },
});

// Set to true only after you have replaced public/videos with videos you have the right to use.
const USE_VIDEO = false;

const user = computed(() => usePage().props.auth?.user ?? null);

const videoSrc = computed(() => {
    const v = props.videos[0];
    if (!v) return null;
    return typeof v === 'string' ? v : (v.url ?? v.src ?? v.path ?? null);
});

const categories = [
    { name: 'Cinnamon sticks', slug: 'cinnamon-sticks', tone: 'from-[#D79A5B] to-[#8A5427]' },
    { name: 'Powder', slug: 'cinnamon-powder', tone: 'from-[#E0AE74] to-[#A86A32]' },
    { name: 'Tea', slug: 'cinnamon-tea', tone: 'from-[#A9C486] to-[#4F7A4B]' },
    { name: 'Oil', slug: 'cinnamon-oil', tone: 'from-[#EBC066] to-[#B5832F]' },
    { name: 'Balm', slug: 'cinnamon-balm', tone: 'from-[#CDB892] to-[#8C7550]' },
    { name: 'Perfume', slug: 'cinnamon-perfume', tone: 'from-[#C29AB5] to-[#6F4A68]' },
];

const facts = [
    ['Grade', 'Alba'],
    ['Ingredients', '100% Ceylon cinnamon'],
    ['Net quantity', '100 g'],
    ['Expiry date', '2028-11-01'],
    ['Batch code', 'CGS-2611-A'],
    ['Growing method', 'Organic'],
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

    <div class="min-h-screen bg-[#FFF9EC] text-[#14241B]">
        <!-- Navigation -->
        <header class="fixed inset-x-0 top-0 z-30 border-b border-white/10 bg-[#0B3B2C]/85 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
                <Link href="/" class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white ring-2 ring-[#E3B04B]/60">
                        <img src="/images/welcome/logo2.png" alt="" class="h-full w-full object-cover mix-blend-multiply" />
                    </span>
                    <span class="font-bold text-white">Cinnamon Trust</span>
                </Link>

                <nav class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm font-medium text-white">
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

        <!-- Hero -->
        <section class="relative overflow-hidden bg-[#0B3B2C] pt-28 text-white">
            <video v-if="USE_VIDEO && videoSrc" :src="videoSrc" autoplay muted loop playsinline
                class="absolute inset-0 h-full w-full object-cover opacity-30"></video>
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-[#E3B04B]/25 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-emerald-400/10 blur-3xl"></div>

            <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 pb-24 pt-10 lg:grid-cols-2">
                <div>
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-[#E3B04B] ring-1 ring-white/15">
                        Sri Lankan cinnamon brands, worldwide buyers
                    </p>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                        Real Ceylon cinnamon.<br />
                        <span class="text-[#E3B04B]">Facts you can check.</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-lg text-white/80">
                        Buy sticks, powder, tea, oil, balm and perfume directly from Sri Lankan brands.
                        Every product shows its grade, ingredients, dates and batch code.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link :href="route('market.index')"
                            class="rounded-full bg-[#E3B04B] px-8 py-3.5 font-bold text-[#14241B] shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-[#f0c260]">
                            Browse the marketplace
                        </Link>
                        <Link :href="route('seller.create')"
                            class="rounded-full border border-white/40 px-8 py-3.5 font-semibold transition hover:bg-white/10">
                            Sell on Cinnamon Trust
                        </Link>
                    </div>

                    <dl class="mt-10 flex flex-wrap gap-x-10 gap-y-4">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-white/60">Products</dt>
                            <dd class="text-2xl font-extrabold">{{ stats.products }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-white/60">Brands</dt>
                            <dd class="text-2xl font-extrabold">{{ stats.brands }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-white/60">Categories</dt>
                            <dd class="text-2xl font-extrabold">6</dd>
                        </div>
                    </dl>
                </div>

                <!-- Example product facts card -->
                <div class="relative mx-auto w-full max-w-sm lg:ml-auto">
                    <div class="rotate-2 rounded-3xl bg-white p-5 text-[#14241B] shadow-2xl shadow-black/30">
                        <div class="flex items-center gap-4">
                            <div class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-[#D79A5B] to-[#8A5427]">
                                <CategoryArt kind="sticks" class="h-12 w-12 text-white/90" />
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-[#B5832F]">Cinnamon sticks</p>
                                <p class="font-bold leading-snug">Alba Cinnamon Sticks</p>
                                <p class="text-xs text-[#14241B]/60">Ceylon Gold Spice &middot; Galle</p>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800">High quality</span>
                            <span class="rounded-full bg-[#F3EBD8] px-3 py-1 text-xs font-medium text-[#6B4E16]">Grade Alba</span>
                        </div>

                        <dl class="mt-4 divide-y divide-[#F0E8D5] text-sm">
                            <div v-for="[k, v] in facts" :key="k" class="flex justify-between gap-4 py-2">
                                <dt class="text-[#14241B]/60">{{ k }}</dt>
                                <dd class="text-right font-medium">{{ v }}</dd>
                            </div>
                        </dl>
                        <p class="mt-3 text-[11px] text-[#14241B]/50">Example of the facts shown on every product page.</p>
                    </div>

                    <div class="absolute -bottom-5 -left-4 -rotate-3 rounded-2xl bg-[#E3B04B] px-4 py-2 text-sm font-bold text-[#14241B] shadow-xl">
                        &#9733; Rated by verified buyers
                    </div>
                </div>
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
                    <div v-for="[n, title, text] in steps" :key="n" class="relative rounded-2xl bg-[#FFF9EC] p-6 ring-1 ring-[#EFE4C8]">
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

        <!-- Join -->
        <section id="join" class="bg-white py-16">
            <div class="mx-auto max-w-6xl px-4">
                <template v-if="!user">
                    <h2 class="text-2xl font-extrabold sm:text-3xl">Join Cinnamon Trust</h2>
                    <p class="mt-2 max-w-2xl text-[#14241B]/70">
                        One account for everyone. Buy Ceylon cinnamon products today, and open your own brand whenever you are ready to sell.
                    </p>
                    <div class="mt-8 grid gap-5 md:grid-cols-2">
                        <div class="rounded-2xl bg-[#FFF9EC] p-6 ring-1 ring-[#EFE4C8]">
                            <h3 class="text-lg font-bold">New here?</h3>
                            <p class="mt-1 text-sm text-[#14241B]/70">Create a free account. It takes about a minute.</p>
                            <Link :href="route('register')" class="mt-5 inline-block rounded-full bg-[#0B3B2C] px-7 py-3 font-semibold text-white hover:bg-[#14503A]">Create an account</Link>
                        </div>
                        <div class="rounded-2xl bg-[#FFF9EC] p-6 ring-1 ring-[#EFE4C8]">
                            <h3 class="text-lg font-bold">Already have an account?</h3>
                            <p class="mt-1 text-sm text-[#14241B]/70">Log in to order, or to manage your brand and products.</p>
                            <Link :href="route('login')" class="mt-5 inline-block rounded-full border-2 border-[#0B3B2C] px-7 py-3 font-semibold text-[#0B3B2C] hover:bg-[#0B3B2C] hover:text-white">Log in</Link>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <h2 class="text-2xl font-extrabold sm:text-3xl">Welcome back, {{ user.name }}</h2>
                    <div class="mt-8 grid gap-5 md:grid-cols-2">
                        <div class="rounded-2xl bg-[#FFF9EC] p-6 ring-1 ring-[#EFE4C8]">
                            <h3 class="text-lg font-bold">Buy</h3>
                            <p class="mt-1 text-sm text-[#14241B]/70">Find Ceylon cinnamon products and check their quality facts.</p>
                            <Link :href="route('market.index')" class="mt-5 inline-block rounded-full bg-[#0B3B2C] px-7 py-3 font-semibold text-white hover:bg-[#14503A]">Browse the marketplace</Link>
                        </div>
                        <div class="rounded-2xl bg-[#FFF9EC] p-6 ring-1 ring-[#EFE4C8]">
                            <h3 class="text-lg font-bold">Sell</h3>
                            <p class="mt-1 text-sm text-[#14241B]/70">Open your brand or manage your products and orders.</p>
                            <Link :href="route('seller.create')" class="mt-5 inline-block rounded-full border-2 border-[#0B3B2C] px-7 py-3 font-semibold text-[#0B3B2C] hover:bg-[#0B3B2C] hover:text-white">Open or manage your brand</Link>
                        </div>
                    </div>
                </template>
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