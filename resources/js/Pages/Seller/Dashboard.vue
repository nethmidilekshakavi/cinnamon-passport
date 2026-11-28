<script setup>
import SellerLayout from '@/Layouts/SellerLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({ brand: Object });

const logoForm = useForm({ logo: null });
const uploadLogo = () =>
    logoForm.post(route('seller.logo.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => logoForm.reset(),
    });
const removeLogo = () => {
    if (confirm('Remove your logo?')) router.delete(route('seller.logo.destroy'), { preserveScroll: true });
};

const stats = ref(null);
onMounted(async () => {
    try {
        const r = await fetch(route('seller.stats'), { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        if (r.ok) stats.value = await r.json();
    } catch (e) {
        stats.value = null;
    }
});

const heroStyle = {
    backgroundImage: "url('/images/market/hero.jpg'), url('/images/categories/cinnamon-sticks.jpg')",
    backgroundSize: 'cover',
    backgroundPosition: 'center',
};

const badge = {
    placed: 'bg-blue-100 text-blue-800',
    shipped: 'bg-amber-100 text-amber-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-200 text-gray-700',
};

const fmt = (d) => (d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' }) : '');
const money = (v) => '$' + Number(v ?? 0).toFixed(2);

const cards = computed(() => [
    { label: 'Products', value: props.brand.products_count, hint: 'listed in your brand', href: route('seller.products.index') },
    { label: 'Orders to ship', value: stats.value ? stats.value.orders.to_ship : '-', hint: 'waiting for you', href: route('seller.orders.index'), highlight: (stats.value?.orders.to_ship ?? 0) > 0 },
    { label: 'Revenue', value: stats.value ? money(stats.value.revenue) : '-', hint: 'from delivered orders (simulated)', href: route('seller.orders.index') },
    { label: 'Seller rating', value: stats.value ? (stats.value.rating.count ? stats.value.rating.average + ' / 5' : 'No ratings') : '-', hint: stats.value ? stats.value.rating.count + ' rating' + (stats.value.rating.count === 1 ? '' : 's') : '', href: route('market.brand', props.brand.slug) },
]);

const checklist = [
    ['1', 'Add your first product', 'Name it, choose a category and fill in the quality facts.', 'seller.products.create'],
    ['2', 'Add a clear photo', 'Products with photos look much better to buyers.', 'seller.products.index'],
    ['3', 'Check your public page', 'See your brand the way buyers see it.', null],
];
</script>

<template>
    <Head title="Seller dashboard" />

    <SellerLayout>
        <div class="mx-auto max-w-6xl space-y-8 px-4 py-8">
            <!-- Banner -->
            <section class="relative overflow-hidden rounded-3xl text-white shadow-xl" :style="heroStyle">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0B3B2C]/95 via-[#0B3B2C]/85 to-[#0B3B2C]/60"></div>
                <div class="relative flex flex-wrap items-center justify-between gap-6 p-6 sm:p-9">
                    <div class="flex items-center gap-4">
                        <span class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-2xl bg-[#E3B04B] text-2xl font-extrabold text-[#14241B]">
                            <img v-if="brand.logo_path" :src="'/storage/' + brand.logo_path" alt="" class="h-full w-full object-cover" /><template v-else>{{ brand.name.charAt(0).toUpperCase() }}</template>
                        </span>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-[#E3B04B]">Welcome back</p>
                            <h1 class="text-2xl font-extrabold sm:text-4xl">{{ brand.name }}</h1>
                            <p class="mt-1 text-sm text-white/80">{{ brand.district }}, {{ brand.country }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <Link :href="route('seller.products.create')"
                            class="rounded-full bg-[#E3B04B] px-6 py-3 font-bold text-[#14241B] shadow transition hover:-translate-y-0.5 hover:bg-[#f0c260]">Add product</Link>
                        <Link :href="route('market.brand', brand.slug)"
                            class="rounded-full border border-white/60 px-6 py-3 font-semibold transition hover:bg-white/15">View my public page</Link>
                    </div>
                </div>
            </section>

            <!-- Stats -->
            <section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <Link v-for="c in cards" :key="c.label" :href="c.href"
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 transition hover:-translate-y-1 hover:shadow-lg"
                    :class="c.highlight ? 'ring-2 ring-[#E3B04B]' : ''">
                    <p class="text-sm font-semibold text-[#14241B]/60">{{ c.label }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#0B3B2C] sm:text-3xl">{{ c.value }}</p>
                    <p class="mt-1 text-xs text-[#14241B]/50">{{ c.hint }}</p>
                </Link>
            </section>

            <!-- Getting started -->
            <section v-if="brand.products_count === 0" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h2 class="text-xl font-extrabold">Getting started</h2>
                <p class="mt-1 text-sm text-[#14241B]/60">Three quick steps to get your first sale.</p>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div v-for="[n, title, text, r] in checklist" :key="n" class="rounded-2xl bg-[#FFF9EC] p-5 ring-1 ring-[#EFE4C8]">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0B3B2C] font-bold text-[#E3B04B]">{{ n }}</span>
                        <p class="mt-3 font-bold">{{ title }}</p>
                        <p class="mt-1 text-sm text-[#14241B]/70">{{ text }}</p>
                        <Link v-if="r" :href="route(r)" class="mt-3 inline-block text-sm font-semibold text-[#14503A] underline">Open</Link>
                        <Link v-else :href="route('market.brand', brand.slug)" class="mt-3 inline-block text-sm font-semibold text-[#14503A] underline">Open</Link>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Recent orders -->
                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 lg:col-span-2">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold">Recent orders</h2>
                        <Link :href="route('seller.orders.index')" class="text-sm font-semibold text-[#14503A] underline">See all</Link>
                    </div>

                    <p v-if="!stats" class="mt-6 text-sm text-[#14241B]/60">Loading...</p>
                    <p v-else-if="stats.recent.length === 0" class="mt-6 text-sm text-[#14241B]/60">No orders yet. When a buyer orders, it shows up here.</p>
                    <ul v-else class="mt-4 divide-y divide-[#F0E8D5]">
                        <li v-for="o in stats.recent" :key="o.id" class="flex flex-wrap items-center justify-between gap-3 py-3">
                            <div>
                                <p class="font-semibold">{{ o.product_name }} <span class="text-sm font-normal text-[#14241B]/60">&times; {{ o.quantity }}</span></p>
                                <p class="text-xs text-[#14241B]/60">{{ o.order_number }} &middot; {{ fmt(o.placed_at) }} &middot; {{ money(o.total) }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge[o.status]">{{ o.status }}</span>
                        </li>
                    </ul>
                </section>

                <!-- Side column -->
                <div class="space-y-6">
                    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <h2 class="text-xl font-extrabold">Low stock</h2>
                        <p v-if="!stats" class="mt-4 text-sm text-[#14241B]/60">Loading...</p>
                        <p v-else-if="stats.low_stock.length === 0" class="mt-4 text-sm text-[#14241B]/60">All products have enough stock.</p>
                        <ul v-else class="mt-4 space-y-3">
                            <li v-for="p in stats.low_stock" :key="p.id" class="flex items-center justify-between gap-3 text-sm">
                                <Link :href="route('seller.products.edit', p.id)" class="font-semibold text-[#14503A] underline">{{ p.name }}</Link>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold" :class="p.stock === 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'">
                                    {{ p.stock === 0 ? 'Sold out' : p.stock + ' left' }}
                                </span>
                            </li>
                        </ul>
                    </section>

                    <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <h2 class="text-xl font-extrabold">Quick actions</h2>
                        <div class="mt-4 grid gap-2 text-sm font-semibold">
                            <Link :href="route('seller.products.create')" class="rounded-xl bg-[#FFF9EC] px-4 py-3 ring-1 ring-[#EFE4C8] hover:bg-[#F3EBD8]">+ Add a product</Link>
                            <Link :href="route('seller.products.index')" class="rounded-xl bg-[#FFF9EC] px-4 py-3 ring-1 ring-[#EFE4C8] hover:bg-[#F3EBD8]">Manage products</Link>
                            <Link :href="route('seller.orders.index')" class="rounded-xl bg-[#FFF9EC] px-4 py-3 ring-1 ring-[#EFE4C8] hover:bg-[#F3EBD8]">View orders</Link>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Logo -->
            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h2 class="text-xl font-extrabold">Brand logo</h2>
                <p class="mt-1 text-sm text-[#14241B]/60">Your logo shows on your products and your public page. JPG, PNG or WebP, up to 2 MB.</p>
                <div class="mt-5 flex flex-wrap items-center gap-5">
                    <span class="grid h-24 w-24 place-items-center overflow-hidden rounded-2xl bg-[#F3EBD8] text-3xl font-extrabold text-[#6B4E16] ring-1 ring-black/5">
                        <img v-if="brand.logo_path" :src="'/storage/' + brand.logo_path" alt="" class="h-full w-full object-cover" />
                        <template v-else>{{ brand.name.charAt(0).toUpperCase() }}</template>
                    </span>
                    <form @submit.prevent="uploadLogo" class="flex flex-wrap items-center gap-3">
                        <input type="file" accept="image/png,image/jpeg,image/webp" @change="logoForm.logo = $event.target.files[0]"
                            class="text-sm file:mr-3 file:rounded-full file:border-0 file:bg-[#0B3B2C] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white" />
                        <button type="submit" :disabled="!logoForm.logo || logoForm.processing"
                            class="rounded-full bg-[#E3B04B] px-5 py-2 text-sm font-bold text-[#14241B] disabled:opacity-50">
                            {{ brand.logo_path ? 'Replace logo' : 'Upload logo' }}
                        </button>
                        <button v-if="brand.logo_path" type="button" @click="removeLogo"
                            class="rounded-full border border-red-300 px-5 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Remove</button>
                    </form>
                </div>
                <p v-if="logoForm.errors.logo" class="mt-3 text-sm text-red-600">{{ logoForm.errors.logo }}</p>
            </section>

            <!-- About -->
            <section v-if="brand.description" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-8">
                <h2 class="text-xl font-extrabold">About your brand</h2>
                <p class="mt-2 whitespace-pre-line text-[#14241B]/80">{{ brand.description }}</p>
            </section>
        </div>
    </SellerLayout>
</template>