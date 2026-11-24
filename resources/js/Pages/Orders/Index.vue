<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ orders: { type: Array, default: () => [] } });

const badge = {
    placed: 'bg-blue-100 text-blue-800',
    shipped: 'bg-amber-100 text-amber-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-200 text-gray-700',
};

const fmt = (d) => (d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '');
</script>

<template>
    <Head title="My orders" />

    <MarketLayout>
        <div class="mx-auto max-w-4xl px-4 py-8">
            <h1 class="mb-6 text-3xl font-extrabold">My orders</h1>

            <div v-if="orders.length === 0" class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-black/5">
                <p class="text-[#14241B]/70">You have not ordered anything yet.</p>
                <Link :href="route('market.index')" class="mt-4 inline-block rounded-full bg-[#14503A] px-6 py-2 font-semibold text-white">
                    Browse the marketplace
                </Link>
            </div>

            <div v-else class="space-y-4">
                <Link v-for="o in orders" :key="o.id" :href="route('orders.show', o.id)"
                    class="block rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5 transition hover:shadow-md">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-bold">{{ o.order_number }}</p>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge[o.status]">{{ o.status }}</span>
                    </div>
                    <p class="mt-2">{{ o.product_name }} <span class="text-sm text-[#14241B]/60">Ã— {{ o.quantity }}</span></p>
                    <p class="mt-1 text-sm text-[#14241B]/60">
                        {{ o.brand?.name }} Â· {{ fmt(o.placed_at) }} Â· Total ${{ o.total }}
                    </p>
                </Link>
            </div>
        </div>
    </MarketLayout>
</template>