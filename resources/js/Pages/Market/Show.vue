<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: { type: Object, required: true },
    isExpired: { type: Boolean, default: false },
    isOwner: { type: Boolean, default: false },
    canOrder: { type: Boolean, default: false },
    more: { type: Array, default: () => [] },
    rating: { type: Object, default: () => ({ average: 0, count: 0 }) },
    reviews: { type: Array, default: () => [] },
});

const user = computed(() => usePage().props.auth?.user ?? null);

const cap = (s) => (s ? s.charAt(0).toUpperCase() + s.slice(1) : '');
const p = props.product;

const facts = computed(() => [
    ['Quality level', cap(p.quality_level)],
    ['Grade', p.grade ?? 'Not graded'],
    ['Ingredients', p.ingredients],
    ['Net quantity', p.net_quantity + ' ' + p.unit],
    ['Production date', p.production_date ?? 'Not stated'],
    ['Expiry date', p.expiry_date ?? 'Not stated'],
    ['Batch code', p.batch_code ?? 'Not stated'],
    ['Organic', p.is_organic ? 'Yes (stated by the seller)' : 'No'],
    ['Growing method', p.growing_method ? cap(p.growing_method) : 'Not stated'],
]);

const badge = {
    high: 'bg-green-100 text-green-800',
    medium: 'bg-amber-100 text-amber-800',
    low: 'bg-gray-200 text-gray-700',
};
</script>

<template>
    <Head :title="product.name" />

    <MarketLayout>
        <div class="mx-auto max-w-6xl px-4 py-8">
            <Link :href="route('market.index')" class="text-sm text-[#14503A] underline">&larr; Back to marketplace</Link>

            <div v-if="!product.is_published && isOwner"
                class="mt-4 rounded-lg bg-amber-100 px-4 py-2 text-sm text-amber-900">
                Preview: this product is hidden from buyers.
            </div>

            <div class="mt-6 grid gap-8 md:grid-cols-2">
                <div class="aspect-square overflow-hidden rounded-2xl bg-[#F3EBD8] ring-1 ring-black/5">
                    <img v-if="product.image_url" :src="product.image_url" :alt="product.name"
                        class="h-full w-full object-cover" />
                    <div v-else class="grid h-full w-full place-items-center text-7xl">ðŸŒ¿</div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-[#B5832F]">{{ product.category?.name }}</p>
                    <h1 class="mt-1 text-3xl font-extrabold">{{ product.name }}</h1>

                    <p class="mt-2 text-sm text-[#14241B]/70">
                        Sold by
                        <Link :href="route('market.brand', product.brand.slug)" class="font-semibold text-[#14503A] underline">
                            {{ product.brand.name }}
                        </Link>
                        Â· {{ product.brand.district }}, {{ product.brand.country }}
                    </p>
                    <p class="mt-1 text-sm">
                        <template v-if="rating.count">
                            <span class="text-[#B5832F]">â˜…</span>
                            <strong>{{ rating.average }}</strong> / 5
                            <span class="text-[#14241B]/60">({{ rating.count }} seller {{ rating.count === 1 ? 'rating' : 'ratings' }})</span>
                        </template>
                        <span v-else class="text-[#14241B]/60">No seller ratings yet</span>
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize"
                            :class="badge[product.quality_level]">{{ product.quality_level }} quality</span>
                        <span v-if="product.grade" class="rounded-full bg-[#E4EFD6] px-3 py-1 text-xs font-medium text-[#14503A]">
                            Grade {{ product.grade }}
                        </span>
                        <span v-if="product.is_organic" class="rounded-full bg-[#E4EFD6] px-3 py-1 text-xs font-medium text-[#14503A]">
                            Organic
                        </span>
                        <span v-if="isExpired" class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                            Expired
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-bold text-[#14503A]">${{ product.price }}
                        <span class="text-sm font-normal text-[#14241B]/60">USD</span>
                    </p>
                    <p class="mt-1 text-sm" :class="product.stock > 0 ? 'text-[#14503A]' : 'text-red-700'">
                        {{ product.stock > 0 ? product.stock + ' in stock' : 'Out of stock' }}
                    </p>

                    <p v-if="product.description" class="mt-5 whitespace-pre-line text-[#14241B]/80">{{ product.description }}</p>

                    <!-- Order -->
                    <div class="mt-6">
                        <Link v-if="canOrder && user" :href="route('checkout.create', product.slug)"
                            class="inline-block rounded-full bg-[#E3B04B] px-8 py-3 font-bold text-[#14241B] shadow transition hover:bg-[#d49f3c]">
                            Order this product
                        </Link>
                        <Link v-else-if="canOrder" :href="route('login')"
                            class="inline-block rounded-full bg-[#E3B04B] px-8 py-3 font-bold text-[#14241B] shadow transition hover:bg-[#d49f3c]">
                            Log in to order
                        </Link>
                        <p v-else-if="isOwner" class="text-sm text-[#14241B]/60">This is your own product.</p>
                        <p v-else class="text-sm text-red-700">This product cannot be ordered right now.</p>
                    </div>
                </div>
            </div>

            <!-- Quality facts -->
            <section class="mt-10 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h2 class="text-xl font-bold">Quality facts</h2>
                <p class="mt-1 text-sm text-[#14241B]/60">
                    These facts are declared by the seller. Grades are mainly set by quill thickness:
                    thinner quills are a higher grade.
                </p>

                <dl class="mt-4 divide-y divide-[#F0E8D5]">
                    <div v-for="[label, value] in facts" :key="label" class="grid gap-1 py-3 sm:grid-cols-3">
                        <dt class="text-sm font-semibold text-[#14241B]/70">{{ label }}</dt>
                        <dd class="sm:col-span-2" :class="label === 'Expiry date' && isExpired ? 'font-semibold text-red-700' : ''">
                            {{ value }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section v-if="reviews.length" class="mt-10 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h2 class="text-xl font-bold">What buyers say about {{ product.brand.name }}</h2>
                <p class="mt-1 text-sm text-[#14241B]/60">Only buyers with a delivered order can rate a seller.</p>
                <div v-for="r in reviews" :key="r.id" class="mt-4 border-t border-[#F0E8D5] pt-4">
                    <p class="text-[#B5832F]">{{ 'â˜…'.repeat(r.rating) }}<span class="text-gray-300">{{ 'â˜…'.repeat(5 - r.rating) }}</span></p>
                    <p v-if="r.comment" class="mt-1">{{ r.comment }}</p>
                    <p class="mt-1 text-xs text-[#14241B]/50">{{ r.buyer?.name }}</p>
                </div>
            </section>

            <section v-if="more.length" class="mt-10">
                <h2 class="mb-4 text-xl font-bold">More from {{ product.brand.name }}</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductCard v-for="m in more" :key="m.id" :product="m" />
                </div>
            </section>
        </div>
    </MarketLayout>
</template>