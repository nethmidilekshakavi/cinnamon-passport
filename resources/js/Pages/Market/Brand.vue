<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    brand: { type: Object, required: true },
    products: { type: Array, default: () => [] },
});

const safeWebsite = /^https?:\/\//i.test(props.brand.website ?? '') ? props.brand.website : null;
</script>

<template>
    <Head :title="brand.name" />

    <MarketLayout>
        <section class="bg-[#14503A] py-10 text-white">
            <div class="mx-auto max-w-6xl px-4">
                <h1 class="text-3xl font-extrabold">{{ brand.name }}</h1>
                <p class="mt-1 text-white/80">{{ brand.district }}, {{ brand.country }}</p>
                <p v-if="brand.description" class="mt-3 max-w-2xl whitespace-pre-line text-white/90">{{ brand.description }}</p>
                <a v-if="safeWebsite" :href="safeWebsite" target="_blank" rel="noopener noreferrer"
                    class="mt-3 inline-block text-sm text-[#E3B04B] underline">Visit website</a>
            </div>
        </section>

        <div class="mx-auto max-w-6xl px-4 py-8">
            <Link :href="route('market.index')" class="text-sm text-[#14503A] underline">&larr; Back to marketplace</Link>

            <h2 class="mb-4 mt-4 text-xl font-bold">Products ({{ products.length }})</h2>

            <p v-if="products.length === 0" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-black/5">
                This brand has no products yet.
            </p>

            <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="p in products" :key="p.id" :product="p" />
            </div>
        </div>
    </MarketLayout>
</template>