<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: { type: Object, required: true },
    quantity: { type: Number, default: 1 },
    maxQuantity: { type: Number, default: 1 },
});

const user = usePage().props.auth?.user ?? null;

const form = useForm({
    quantity: props.quantity,
    shipping_name: user?.name ?? '',
    shipping_address: '',
    shipping_city: '',
    shipping_postal_code: '',
    shipping_country: '',
    accept_prototype: false,
});

const total = computed(() => {
    const q = Number(form.quantity) || 0;
    return (Number(props.product.price) * q).toFixed(2);
});

const submit = () => form.post(route('checkout.store', props.product.slug));

const field = 'mt-1 block w-full rounded-lg border border-[#E8DFC9] bg-white px-3 py-2 text-sm focus:border-[#14503A] focus:outline-none';
</script>

<template>
    <Head title="Checkout" />

    <MarketLayout>
        <div class="mx-auto max-w-5xl px-4 py-8">
            <Link :href="route('market.show', product.slug)" class="text-sm text-[#14503A] underline">&larr; Back to product</Link>
            <h1 class="mb-6 mt-3 text-3xl font-extrabold">Checkout</h1>

            <div class="grid gap-8 lg:grid-cols-[1fr_20rem]">
                <form @submit.prevent="submit" class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <div>
                        <h2 class="text-lg font-bold">Quantity</h2>
                        <input v-model="form.quantity" type="number" min="1" :max="maxQuantity" :class="[field, 'max-w-[8rem]']" required />
                        <p class="mt-1 text-xs text-[#14241B]/60">Up to {{ maxQuantity }} available.</p>
                        <p v-if="form.errors.quantity" class="mt-1 text-sm text-red-600">{{ form.errors.quantity }}</p>
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-lg font-bold">Shipping details</h2>

                        <div>
                            <label class="text-sm font-semibold" for="shipping_name">Full name</label>
                            <input id="shipping_name" v-model="form.shipping_name" type="text" :class="field" required />
                            <p v-if="form.errors.shipping_name" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold" for="shipping_address">Address</label>
                            <input id="shipping_address" v-model="form.shipping_address" type="text" :class="field" required />
                            <p v-if="form.errors.shipping_address" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_address }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold" for="shipping_city">City</label>
                                <input id="shipping_city" v-model="form.shipping_city" type="text" :class="field" required />
                                <p v-if="form.errors.shipping_city" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_city }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold" for="shipping_postal_code">Postal code (optional)</label>
                                <input id="shipping_postal_code" v-model="form.shipping_postal_code" type="text" :class="field" />
                                <p v-if="form.errors.shipping_postal_code" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_postal_code }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold" for="shipping_country">Country</label>
                            <input id="shipping_country" v-model="form.shipping_country" type="text" :class="field" required />
                            <p v-if="form.errors.shipping_country" class="mt-1 text-sm text-red-600">{{ form.errors.shipping_country }}</p>
                        </div>
                    </div>

                    <div class="rounded-xl bg-amber-50 p-4 ring-1 ring-amber-200">
                        <h2 class="font-bold">Payment (simulated)</h2>
                        <p class="mt-1 text-sm text-[#14241B]/80">
                            This is a student prototype. No card details are asked for and no real payment is taken.
                        </p>
                        <label class="mt-3 flex items-start gap-2 text-sm">
                            <input type="checkbox" v-model="form.accept_prototype" class="mt-0.5 rounded border-gray-300" />
                            <span>I understand that no real payment is taken and nothing will really be shipped.</span>
                        </label>
                        <p v-if="form.errors.accept_prototype" class="mt-1 text-sm text-red-600">{{ form.errors.accept_prototype }}</p>
                    </div>

                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-full bg-[#14503A] px-8 py-3 font-bold text-white transition hover:bg-[#0B3B2C] disabled:opacity-60">
                        Place order (simulated payment)
                    </button>
                </form>

                <!-- Summary -->
                <aside class="h-fit rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <h2 class="text-lg font-bold">Order summary</h2>
                    <div class="mt-4 flex gap-3">
                        <img v-if="product.image_url" :src="product.image_url" alt="" class="h-16 w-16 rounded-lg object-cover" />
                        <div v-else class="grid h-16 w-16 place-items-center rounded-lg bg-[#F3EBD8] text-2xl">ðŸŒ¿</div>
                        <div>
                            <p class="font-semibold leading-snug">{{ product.name }}</p>
                            <p class="text-xs text-[#14241B]/60">{{ product.net_quantity }} {{ product.unit }} Â· {{ product.brand.name }}</p>
                        </div>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt>Price</dt><dd>${{ product.price }}</dd></div>
                        <div class="flex justify-between"><dt>Quantity</dt><dd>{{ form.quantity }}</dd></div>
                        <div class="flex justify-between border-t border-[#F0E8D5] pt-2 text-base font-bold">
                            <dt>Total (USD)</dt><dd>${{ total }}</dd>
                        </div>
                    </dl>
                </aside>
            </div>
        </div>
    </MarketLayout>
</template>