<script setup>
import CategoryArt from '@/Components/Market/CategoryArt.vue';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import ProductCard from '@/Components/Market/ProductCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
    options: { type: Object, required: true },
});

const form = reactive({ ...props.filters });
const showFilters = ref(false);

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

const pickCategory = (slug) => {
    form.category = form.category === slug ? '' : slug;
    apply();
};

const activeCategory = computed(() => props.options.categories.find((c) => c.slug === form.category) ?? null);

const hasFilters = computed(
    () => !!(form.q || form.category || form.quality || form.grade || form.district || form.organic || form.min_price || form.max_price),
);

// your own wide photo goes in public/images/market/hero.jpg, otherwise the sticks photo is used
const heroStyle = {
    backgroundImage: "url('/images/market/hero.jpg'), url('/images/categories/cinnamon-sticks.jpg')",
    backgroundSize: 'cover',
    backgroundPosition: 'center',
};

const tones = {
    stick: 'from-[#D79A5B] to-[#8A5427]',
    powder: 'from-[#E0AE74] to-[#A86A32]',
    tea: 'from-[#A9C486] to-[#4F7A4B]',
    oil: 'from-[#EBC066] to-[#B5832F]',
    balm: 'from-[#CDB892] to-[#8C7550]',
    perfume: 'from-[#C29AB5] to-[#6F4A68]',
};
const toneFor = (slug) => tones[Object.keys(tones).find((k) => String(slug).includes(k)) ?? 'stick'];

const field = 'mt-1 block w-full rounded-lg border border-[#E8DFC9] bg-white px-3 py-2 text-sm focus:border-[#14503A] focus:outline-none';
</script>

<template>
    <Head title="Marketplace" />

    <MarketLayout>
        <!-- Hero -->
        <section class="relative overflow-hidden bg-[#0B3B2C] text-white" :style="heroStyle">
            <div class="absolute inset-0 bg-[#0B3B2C]/80 md:hidden"></div>
            <div class="absolute inset-0 hidden bg-gradient-to-r from-[#0B3B2C]/95 via-[#0B3B2C]/75 to-[#0B3B2C]/25 md:block"></div>

            <div class="relative mx-auto max-w-6xl px-4 py-14 sm:py-20 lg:py-28">
                <p class="text-sm font-semibold uppercase tracking-wider text-[#E3B04B]">Ceylon cinnamon marketplace</p>
                <h1 class="mt-3 max-w-2xl text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                    Find real Ceylon cinnamon, from the brands that make it
                </h1>
                <p class="mt-4 max-w-xl text-base text-white/85 sm:text-lg">
                    Sticks, powder, tea, oil, balm and perfume from Sri Lankan cinnamon brands. Every product shows its quality facts.
                </p>

                <form @submit.prevent="apply" class="mt-8 flex max-w-2xl flex-col gap-3 sm:flex-row">
                    <input v-model="form.q" type="search" placeholder="Search a product, ingredient or brand"
                        class="w-full flex-1 rounded-full border-0 bg-white px-6 py-4 text-base text-[#14241B] shadow-lg placeholder:text-[#14241B]/50 focus:ring-2 focus:ring-[#E3B04B]" />
                    <button type="submit"
                        class="rounded-full bg-[#E3B04B] px-8 py-4 font-bold text-[#14241B] shadow-lg transition hover:bg-[#f0c260]">
                        Search
                    </button>
                </form>

                <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-white/85">
                    <span>&#10003; Quality facts on every product</span>
                    <span>&#10003; Direct from Sri Lankan brands</span>
                    <span>&#10003; Ratings from verified buyers</span>
                </div>
            </div>
        </section>

        <!-- Category photo cards -->
        <section class="mx-auto max-w-6xl px-4 pt-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-[#B5832F]">Categories</p>
                    <h2 class="mt-1 text-2xl font-extrabold sm:text-3xl">Shop by category</h2>
                </div>
                <button v-if="form.category" type="button" @click="pickCategory(form.category)"
                    class="text-sm font-semibold text-[#14503A] underline">Show all categories</button>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-6">
                <button v-for="c in options.categories" :key="c.id" type="button" @click="pickCategory(c.slug)"
                    class="group relative aspect-[4/5] overflow-hidden rounded-2xl bg-gradient-to-br text-left shadow-md transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                    :class="[toneFor(c.slug), form.category === c.slug ? 'ring-4 ring-[#E3B04B]' : '']">
                    <CategoryArt :kind="c.slug" class="absolute left-1/2 top-1/2 h-14 w-14 -translate-x-1/2 -translate-y-1/2 text-white/90" />
                    <img :src="'/images/categories/' + c.slug + '.jpg'" :alt="c.name" loading="lazy"
                        class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110"
                        @error="$event.target.remove()" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>
                    <span v-if="form.category === c.slug"
                        class="absolute right-2 top-2 rounded-full bg-[#E3B04B] px-2.5 py-0.5 text-[11px] font-bold text-[#14241B]">Selected</span>
                    <span class="absolute inset-x-0 bottom-0 p-3 text-base font-bold text-white sm:text-lg">{{ c.name }}</span>
                </button>
            </div>
        </section>

        <div id="results" class="mx-auto grid max-w-6xl gap-6 px-4 py-10 lg:grid-cols-[16rem_1fr]">
            <!-- Filters -->
            <div class="h-fit lg:sticky lg:top-6">
                <button type="button" @click="showFilters = !showFilters"
                    class="mb-3 flex w-full items-center justify-between rounded-2xl bg-white px-5 py-3 text-sm font-semibold shadow-sm ring-1 ring-black/5 lg:hidden">
                    <span>Filters</span>
                    <span>{{ showFilters ? 'Hide' : 'Show' }}</span>
                </button>

                <form @submit.prevent="apply" class="space-y-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/5"
                    :class="showFilters ? 'block' : 'hidden lg:block'">
                    <h3 class="font-bold">Filters</h3>

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
            </div>

            <!-- Results -->
            <div>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-extrabold">{{ activeCategory ? activeCategory.name : 'All products' }}</h2>
                        <p class="text-sm text-[#14241B]/70">{{ products.total }} product<span v-if="products.total !== 1">s</span></p>
                    </div>
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

                <div v-if="products.data.length === 0" class="rounded-3xl bg-white p-10 text-center shadow-sm ring-1 ring-black/5">
                    <p class="text-lg font-semibold">No products here yet</p>
                    <p class="mt-1 text-[#14241B]/70">
                        {{ hasFilters ? 'No products match these filters.' : 'Sellers are still adding their products. Please check again soon.' }}
                    </p>
                    <div class="mt-5 flex flex-wrap justify-center gap-3">
                        <button v-if="hasFilters" type="button" @click="reset"
                            class="rounded-full bg-[#14503A] px-6 py-2.5 font-semibold text-white hover:bg-[#0B3B2C]">Reset filters</button>
                        <Link :href="route('seller.create')"
                            class="rounded-full border-2 border-[#14503A] px-6 py-2.5 font-semibold text-[#14503A] hover:bg-[#14503A] hover:text-white">Sell on Cinnamon Trust</Link>
                    </div>
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