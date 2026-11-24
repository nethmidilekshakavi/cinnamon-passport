<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ product: { type: Object, required: true } });

const badge = {
    high: 'bg-green-100 text-green-800',
    medium: 'bg-amber-100 text-amber-800',
    low: 'bg-gray-200 text-gray-700',
};
</script>

<template>
    <Link
        :href="route('market.show', product.slug)"
        class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition hover:shadow-md"
    >
        <div class="aspect-square bg-[#F3EBD8]">
            <img v-if="product.image_url" :src="product.image_url" :alt="product.name"
                class="h-full w-full object-cover transition group-hover:scale-105" />
            <div v-else class="grid h-full w-full place-items-center text-4xl">ðŸŒ¿</div>
        </div>

        <div class="flex flex-1 flex-col gap-2 p-4">
            <p class="text-xs uppercase tracking-wide text-[#B5832F]">{{ product.category?.name }}</p>
            <h3 class="font-semibold leading-snug">{{ product.name }}</h3>
            <p class="text-xs text-[#14241B]/60">
                {{ product.brand?.name }} Â· {{ product.brand?.district }}
            </p>

            <div class="flex flex-wrap gap-1.5">
                <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                    :class="badge[product.quality_level]">{{ product.quality_level }} quality</span>
                <span v-if="product.grade" class="rounded-full bg-[#E4EFD6] px-2 py-0.5 text-xs font-medium text-[#14503A]">
                    {{ product.grade }}
                </span>
                <span v-if="product.is_organic" class="rounded-full bg-[#E4EFD6] px-2 py-0.5 text-xs font-medium text-[#14503A]">
                    Organic
                </span>
            </div>

            <div class="mt-auto flex items-end justify-between pt-2">
                <span class="text-lg font-bold text-[#14503A]">${{ product.price }}</span>
                <span class="text-xs text-[#14241B]/60">
                    {{ product.net_quantity }} {{ product.unit }}
                    <template v-if="product.stock === 0"> Â· Out of stock</template>
                </span>
            </div>
        </div>
    </Link>
</template>