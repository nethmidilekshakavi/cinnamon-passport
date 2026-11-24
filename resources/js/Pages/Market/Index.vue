<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
    options: { type: Object, required: true },
});

const form = reactive({ ...props.filters });

const apply = () => {
    const params = {};
    for (const [key, value] of Object.entries(form)) {
        if (value === '' || value === false || value === null) continue;
        if (key === 'sort' && value === 'newest') continue;
        params[key] = value === true ? 1 : value;
    }
    router.get(route('market.index'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const reset = () => {
    Object.assign(form, {
        q: '', category: '', quality: '', grade: '', district: '',
        organic: false, min_price: '', max_price: '', sort: 'newest',
    });
    apply();
};

const field = 'mt-1 block w-full rounded-lg border border-[#E8DFC9] bg-white px-3 py-2 text-sm focus:border-[#14503A] focus:outline-none';
</script>

<template>
    <Head title="Marketplace" />

    <MarketLayout>
        <section class="bg-[#14503A] py-10 text-white">
            <div class="mx-auto max-w-6xl px-4">
                <h1 class="text-3xl font-extrabold">Ceylon cinnamon marketplace</h1>
                <p class="mt-2 max-w-2xl text-white/80">
                    Sticks, powder, tea, oil, balm and perfume from Sri Lankan cinnamon brands.
                    Every product shows its quality facts.
                </p>
            </div>
        </section>

        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 lg:grid-cols-[16rem_1fr]">
            <!-- Filters -->
            <form @submit.prevent="apply" class="h-fit space-y-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5">
                <div>
                    <label class="text-sm font-semibold" for="q">Search</label>
                    <input id="q" v-model="form.q" type="search" placeholder="Product, ingredient or brand" :class="field" />
                </div>

                <div>
                    <label class="text-sm font-semibold" for="category">Category</label>
                    <select id="category" v-model="form.category" @change="apply" :class="field">
                        <option value="">All categories</option>
                        <option v-for="c in options.categories" :key="c.id" :value="c.slug">{{ c.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold" for="quality">Quality level</label>
                    <select id="quality" v-model="form.quality" @change="apply" :class="[field, 'capitalize']">
                        <option value="">Any</option>
                        <option v-for="q in options.qualityLevels" :key="q" :value="q">{{ q }}</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold" for="grade">Grade</label>
                    <select id="grade" v-model="form.grade" @change="apply" :class="field">
                        <option value="">Any</option>
                        <option v-for="g in options.grades" :key="g" :value="g">{{ g }}</option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-semibold" for="district">Seller location</label>
                    <select id="district" v-model="form.district" @change="apply" :class="field">
                        <option value="">Anywhere in Sri Lanka</option>
                        <option v-for="d in options.districts" :key="d" :value="d">{{ d }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-semibold" for="min_price">Min $</label>
                        <input id="min_price" v-model="form.min_price" type="number" min="0" step="0.01" :class="field" />
                    </div>
                    <div>
                        <label class="text-sm font-semibold" for="max_price">Max $</label>
                        <input id="max_price" v-model="form.max_price" type="number" min="0" step="0.01" :class="field" />
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" v-model="form.organic" @change="apply" class="rounded border-gray-300" />
                    Organic only
                </label>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 rounded-full bg-[#14503A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0B3B2C]">
                        Apply
                    </button>
                    <button type="button" @click="reset"
                        class="rounded-full border border-[#E8DFC9] px-4 py-2 text-sm font-semibold hover:bg-[#FFF9EC]">
                        Reset
                    </button>
                </div>
            </form>

            <!-- Results -->
            <div>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-[#14241B]/70">{{ products.total }} product<span v-if="products.total !== 1">s</span></p>
                    <div class="flex items-center gap-2 text-sm">
                        <label for="sort">Sort by</label>
                        <select id="sort" v-model="form.sort" @change="apply"
                            class="rounded-lg border border-[#E8DFC9] bg-white px-3 py-1.5 text-sm">
                            <option value="newest">Newest</option>
                            <option value="price_asc">Price: low to high</option>
                            <option value="price_desc">Price: high to low</option>
                        </select>
                    </div>
                </div>

                <div v-if="products.data.length === 0" class="rounded-2xl bg-white p-10 text-center shadow-sm ring-1 ring-black/5">
                    <p class="text-[#14241B]/70">No products match these filters.</p>
                </div>

                <div v-else class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <ProductCard v-for="p in products.data" :key="p.id" :product="p" />
                </div>

                <nav v-if="products.links && products.last_page > 1" class="mt-8 flex flex-wrap justify-center gap-1">
                    <template v-for="(l, i) in products.links" :key="i">
                        <Link v-if="l.url" :href="l.url" preserve-scroll
                            class="rounded-lg px-3 py-1.5 text-sm"
                            :class="l.active ? 'bg-[#14503A] text-white' : 'bg-white ring-1 ring-black/10 hover:bg-[#FFF9EC]'"
                            v-html="l.label" />
                        <span v-else class="rounded-lg px-3 py-1.5 text-sm text-gray-400" v-html="l.label" />
                    </template>
                </nav>
            </div>
        </div>
    </MarketLayout>
</template>