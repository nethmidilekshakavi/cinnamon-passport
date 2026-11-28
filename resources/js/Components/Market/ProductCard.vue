<script setup>
import CategoryArt from '@/Components/Market/CategoryArt.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ product: { type: Object, required: true } });
const p = computed(() => props.product);

const tones = {
    sticks: 'from-[#D79A5B] to-[#8A5427]',
    powder: 'from-[#E0AE74] to-[#A86A32]',
    tea: 'from-[#A9C486] to-[#4F7A4B]',
    oil: 'from-[#EBC066] to-[#B5832F]',
    balm: 'from-[#CDB892] to-[#8C7550]',
    perfume: 'from-[#C29AB5] to-[#6F4A68]',
};

const kind = computed(() => p.value.category?.slug || p.value.category?.name || '');
const tone = computed(() => {
    const k = String(kind.value).toLowerCase();
    return tones[Object.keys(tones).find((t) => k.includes(t === 'sticks' ? 'stick' : t)) ?? 'sticks'];
});

const quality = {
    high: 'bg-emerald-100 text-emerald-800',
    medium: 'bg-amber-100 text-amber-800',
    low: 'bg-stone-200 text-stone-700',
};
</script>

<template>
    <Link :href="route('market.show', p.slug)"
        class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="relative aspect-[4/3] overflow-hidden">
            <img v-if="p.image_url" :src="p.image_url" :alt="p.name"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
            <div v-else class="grid h-full w-full place-items-center bg-gradient-to-br" :class="tone">
                <CategoryArt :kind="kind" class="h-20 w-20 text-white/90 drop-shadow transition duration-500 group-hover:scale-110" />
            </div>
            <span v-if="p.is_organic"
                class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-emerald-800 shadow">
                Organic
            </span>
        </div>

        <div class="flex flex-1 flex-col p-4">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#B5832F]">{{ p.category?.name }}</p>
            <h3 class="mt-1 font-bold leading-snug text-[#14241B]">{{ p.name }}</h3>
            <p class="mt-1 flex items-center gap-1.5 text-xs text-[#14241B]/60"><img v-if="p.brand?.logo_path" :src="'/storage/' + p.brand.logo_path" alt="" class="h-5 w-5 rounded-full object-cover" />{{ p.brand?.name }} &middot; {{ p.brand?.district }}</p>

            <div class="mt-2 flex flex-wrap gap-1.5">
                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-medium capitalize" :class="quality[p.quality_level]">
                    {{ p.quality_level }} quality
                </span>
                <span v-if="p.grade" class="rounded-full bg-[#F3EBD8] px-2.5 py-0.5 text-[11px] font-medium text-[#6B4E16]">
                    Grade {{ p.grade }}
                </span>
            </div>

            <div class="mt-auto flex items-end justify-between pt-4">
                <p class="text-xl font-extrabold text-[#0B3B2C]">${{ Number(p.price).toFixed(2) }}</p>
                <p class="text-xs text-[#14241B]/50">{{ p.net_quantity }} {{ p.unit }}</p>
            </div>
        </div>
    </Link>
</template>