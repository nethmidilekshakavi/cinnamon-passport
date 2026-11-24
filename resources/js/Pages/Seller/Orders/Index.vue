<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({ orders: { type: Array, default: () => [] } });

const error = computed(() => usePage().props.errors?.order ?? null);

const badge = {
    placed: 'bg-blue-100 text-blue-800',
    shipped: 'bg-amber-100 text-amber-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-200 text-gray-700',
};

const fmt = (d) => (d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '');

const ship = (order) => router.post(route('seller.orders.ship', order.id), {}, { preserveScroll: true });
</script>

<template>
    <Head title="Orders for my brand" />

    <MarketLayout>
        <div class="mx-auto max-w-4xl px-4 py-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-3xl font-extrabold">Orders for my brand</h1>
                <Link :href="route('seller.dashboard')" class="text-sm text-[#14503A] underline">Seller dashboard</Link>
            </div>

            <div v-if="error" class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-800">{{ error }}</div>

            <div v-if="orders.length === 0" class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-black/5">
                <p class="text-[#14241B]/70">No orders yet.</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="o in orders" :key="o.id" class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-bold">{{ o.order_number }} <span class="text-sm font-normal text-[#14241B]/60">Â· {{ fmt(o.placed_at) }}</span></p>
                        <span class="rounded-full px-3 py-1 text-xs font-medium capitalize" :class="badge[o.status]">{{ o.status }}</span>
                    </div>

                    <p class="mt-2">{{ o.product_name }} <span class="text-sm text-[#14241B]/60">({{ o.product_size }}) Ã— {{ o.quantity }}</span></p>
                    <p class="text-sm text-[#14241B]/60">Total ${{ o.total }} Â· simulated payment</p>

                    <div class="mt-3 rounded-xl bg-[#FFF9EC] p-3 text-sm">
                        <p class="font-semibold">Ship to</p>
                        <p>{{ o.shipping_name }}, {{ o.shipping_address }}, {{ o.shipping_city }}
                            <span v-if="o.shipping_postal_code">{{ o.shipping_postal_code }}</span>, {{ o.shipping_country }}</p>
                    </div>

                    <button v-if="o.status === 'placed'" type="button" @click="ship(o)"
                        class="mt-3 rounded-full bg-[#14503A] px-5 py-2 text-sm font-semibold text-white hover:bg-[#0B3B2C]">
                        Mark as shipped
                    </button>
                </div>
            </div>
        </div>
    </MarketLayout>
</template>