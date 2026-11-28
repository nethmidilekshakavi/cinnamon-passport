<script setup>
import SellerLayout from '@/Layouts/SellerLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ districts: Array });

const form = useForm({
    name: '',
    description: '',
    district: '',
    address: '',
    contact_phone: '',
    website: '',
});

const submit = () => form.post(route('seller.store'));

const heroStyle = {
    backgroundImage: "url('/images/market/hero.jpg'), url('/images/categories/cinnamon-sticks.jpg')",
    backgroundSize: 'cover',
    backgroundPosition: 'center',
};

const benefits = [
    ['1', 'Reach buyers worldwide', 'Foreign buyers browse and order directly from your brand.'],
    ['2', 'Show your quality facts', 'Grade, ingredients, quantity, dates and batch code on every product.'],
    ['3', 'Build your rating', 'Verified buyers rate you after their order is delivered.'],
];

const field = 'mt-1 block w-full rounded-xl border border-[#E8DFC9] bg-white px-4 py-3 text-sm shadow-sm focus:border-[#14503A] focus:ring-[#14503A]';
</script>

<template>
    <Head title="Open your brand" />

    <SellerLayout minimal>
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-8 sm:py-12 lg:grid-cols-5">
            <!-- Why sell here -->
            <aside class="relative overflow-hidden rounded-3xl text-white shadow-xl lg:col-span-2" :style="heroStyle">
                <div class="absolute inset-0 bg-gradient-to-b from-[#0B3B2C]/85 via-[#0B3B2C]/80 to-[#0B3B2C]/95"></div>
                <div class="relative flex h-full flex-col p-7 sm:p-9">
                    <p class="text-sm font-semibold uppercase tracking-wider text-[#E3B04B]">Sell on Cinnamon Trust</p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">Open your brand and sell your cinnamon to the world</h1>
                    <p class="mt-3 text-white/80">It takes about two minutes. You can add your products right after.</p>

                    <ul class="mt-8 space-y-5">
                        <li v-for="[n, title, text] in benefits" :key="n" class="flex gap-4">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#E3B04B] font-bold text-[#14241B]">{{ n }}</span>
                            <div>
                                <p class="font-bold">{{ title }}</p>
                                <p class="text-sm text-white/75">{{ text }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </aside>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:p-9 lg:col-span-3">
                <section>
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#0B3B2C] text-sm font-bold text-[#E3B04B]">1</span>
                        <h2 class="text-lg font-bold">Your brand</h2>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="name" class="text-sm font-semibold">Brand name</label>
                            <input id="name" v-model="form.name" type="text" required placeholder="For example: Ceylon Gold Spice" :class="field" />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div>
                            <label for="description" class="text-sm font-semibold">About your brand</label>
                            <textarea id="description" v-model="form.description" rows="4"
                                placeholder="Tell buyers who you are, how long you have grown cinnamon and what makes your products special."
                                :class="field"></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#0B3B2C] text-sm font-bold text-[#E3B04B]">2</span>
                        <h2 class="text-lg font-bold">Where you are</h2>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="district" class="text-sm font-semibold">District</label>
                            <select id="district" v-model="form.district" required :class="field">
                                <option value="" disabled>Select a district</option>
                                <option v-for="d in districts" :key="d" :value="d">{{ d }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.district" />
                        </div>
                        <div>
                            <label for="address" class="text-sm font-semibold">Address <span class="font-normal text-[#14241B]/50">(optional)</span></label>
                            <input id="address" v-model="form.address" type="text" :class="field" />
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>
                    </div>
                </section>

                <section>
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#0B3B2C] text-sm font-bold text-[#E3B04B]">3</span>
                        <h2 class="text-lg font-bold">How buyers can reach you</h2>
                    </div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="contact_phone" class="text-sm font-semibold">Contact phone <span class="font-normal text-[#14241B]/50">(optional)</span></label>
                            <input id="contact_phone" v-model="form.contact_phone" type="text" placeholder="+94 77 123 4567" :class="field" />
                            <InputError class="mt-2" :message="form.errors.contact_phone" />
                        </div>
                        <div>
                            <label for="website" class="text-sm font-semibold">Website <span class="font-normal text-[#14241B]/50">(optional)</span></label>
                            <input id="website" v-model="form.website" type="url" placeholder="https://" :class="field" />
                            <InputError class="mt-2" :message="form.errors.website" />
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center gap-4">
                    <button type="submit" :disabled="form.processing"
                        class="w-full rounded-full bg-[#14503A] px-10 py-4 text-base font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-[#0B3B2C] disabled:opacity-60 sm:w-auto">
                        {{ form.processing ? 'Creating...' : 'Create my brand' }}
                    </button>
                    <p class="text-sm text-[#14241B]/60">Next: add your first product.</p>
                </div>
            </form>
        </div>
    </SellerLayout>
</template>