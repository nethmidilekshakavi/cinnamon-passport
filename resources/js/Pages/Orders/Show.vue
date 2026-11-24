<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    order: { type: Object, required: true },
    justPlaced: { type: Boolean, default: false },
    canCancel: { type: Boolean, default: false },
    canMarkReceived: { type: Boolean, default: false },
});

const error = computed(() => usePage().props.errors?.order ?? null);

const badge = {
    placed: 'bg-blue-100 text-blue-800',
    shipped: 'bg-amber-100 text-amber-800',
    delivered: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-200 text-gray-700',
};

const fmt = (d) => (d ? new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : '');

const steps = computed(() => [
    { label: 'Placed', date: props.order.placed_at, done: true },
    { label: 'Shipped', date: props.order.shipped_at, done: ['shipped', 'delivered'].includes(props.order.status) },
    { label: 'Delivered', date: props.order.delivered_at, done: props.order.status === 'delivered' },
]);

const cancel = () => {
    if (confirm('Cancel this order?')) {
        router.post(route('orders.cancel', props.order.id));
    }
};

const received = () => router.post(route('orders.received', props.order.id));
</script>

<template>
    <Head :title="order.order_number" />

    <MarketLayout>
        <div class="mx-auto max-w-3xl px-4 py-8">
            <Link :href="route('orders.index')" class="text-sm text-[#14503A] underline">&larr; My orders</Link>

            <div v-if="justPlaced" class="mt-4 rounded-xl bg-green-100 px-4 py-3 text-green-900">
                Thank you! Your order was placed. No real payment was taken (prototype).
            </div>
            <div v-if="error" class="mt-4 rounded-xl bg-red-100 px-4 py-3 text-red-800">{{ error }}</div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-3xl font-extrabold">{{ order.order_number }}</h1>
                <span class="rounded-full px-3 py-1 text-sm font-medium capitalize" :class="badge[order.status]">{{ order.status }}</span>
            </div>

            <!-- Progress -->
            <ol v-if="order.status !== 'cancelled'" class="mt-6 grid grid-cols-3 gap-2 text-center text-sm">
                <li v-for="s in steps" :key="s.label" class="rounded-xl p-3"
                    :class="s.done ? 'bg-[#E4EFD6] text-[#14503A]' : 'bg-white text-[#14241B]/50 ring-1 ring-black/5'">
                    <p class="font-semibold">{{ s.label }}</p>
                    <p class="text-xs">{{ s.done ? fmt(s.date) : 'Waiting' }}</p>
                </li>
            </ol>
            <p v-else class="mt-6 rounded-xl bg-gray-100 px-4 py-3 text-sm text-gray-700">
                This order was cancelled on {{ fmt(order.cancelled_at) }}.
            </p>

            <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h2 class="text-lg font-bold">Items</h2>
                <div class="mt-3 flex items-start justify-between gap-4">
                    <div>
                        <Link v-if="order.product" :href="route('market.show', order.product.slug)" class="font-semibold text-[#14503A] underline">
                            {{ order.product_name }}
                        </Link>
                        <p v-else class="font-semibold">{{ order.product_name }}</p>
                        <p class="text-sm text-[#14241B]/60">
                            {{ order.product_size }} Â· Sold by
                            <Link v-if="order.brand" :href="route('market.brand', order.brand.slug)" class="underline">{{ order.brand.name }}</Link>
                        </p>
                    </div>
                    <p class="text-right text-sm">${{ order.unit_price }} Ã— {{ order.quantity }}</p>
                </div>
                <p class="mt-4 flex justify-between border-t border-[#F0E8D5] pt-3 text-lg font-bold">
                    <span>Total (USD)</span><span>${{ order.total }}</span>
                </p>
                <p class="mt-1 text-xs text-[#14241B]/60">Payment: simulated (no real charge)</p>
            </section>

            <section class="mt-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                <h2 class="text-lg font-bold">Shipping to</h2>
                <p class="mt-2">{{ order.shipping_name }}</p>
                <p class="text-[#14241B]/80">
                    {{ order.shipping_address }}<br />
                    {{ order.shipping_city }}<span v-if="order.shipping_postal_code"> {{ order.shipping_postal_code }}</span><br />
                    {{ order.shipping_country }}
                </p>
            </section>

            <div class="mt-6 flex flex-wrap gap-3">
                <button v-if="canMarkReceived" type="button" @click="received"
                    class="rounded-full bg-[#14503A] px-6 py-2.5 font-semibold text-white hover:bg-[#0B3B2C]">
                    I received it
                </button>
                <button v-if="canCancel" type="button" @click="cancel"
                    class="rounded-full border-2 border-red-300 px-6 py-2.5 font-semibold text-red-700 hover:bg-red-50">
                    Cancel order
                </button>
            </div>
        </div>
    </MarketLayout>
</template>