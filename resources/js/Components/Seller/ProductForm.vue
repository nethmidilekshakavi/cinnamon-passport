<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    product: { type: Object, default: null },
    options: { type: Object, required: true },
});

const isEdit = props.product !== null;
const p = props.product ?? {};

const form = useForm({
    category_id: p.category_id ?? '',
    name: p.name ?? '',
    description: p.description ?? '',
    quality_level: p.quality_level ?? 'high',
    grade: p.grade ?? '',
    ingredients: p.ingredients ?? '',
    net_quantity: p.net_quantity ?? '',
    unit: p.unit ?? 'g',
    production_date: p.production_date ?? '',
    expiry_date: p.expiry_date ?? '',
    batch_code: p.batch_code ?? '',
    is_organic: p.is_organic ?? false,
    growing_method: p.growing_method ?? '',
    price: p.price ?? '',
    stock: p.stock ?? 0,
    image: null,
    is_published: p.is_published ?? true,
});

const submit = () => {
    form
        .transform((data) => ({
            ...data,
            is_organic: data.is_organic ? 1 : 0,
            is_published: data.is_published ? 1 : 0,
            ...(isEdit ? { _method: 'put' } : {}),
        }))
        .post(
            isEdit
                ? route('seller.products.update', props.product.id)
                : route('seller.products.store'),
            { forceFormData: true },
        );
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-8">
        <!-- Basics -->
        <section class="space-y-5 rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold">Product basics</h2>

            <div>
                <InputLabel for="category_id" value="Category" />
                <select id="category_id" v-model="form.category_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="" disabled>Select a category</option>
                    <option v-for="c in options.categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.category_id" />
            </div>

            <div>
                <InputLabel for="name" value="Product name" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full"
                    placeholder="e.g. Alba Cinnamon Sticks 100 g" required />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="description" value="Description (optional)" />
                <textarea id="description" v-model="form.description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>
        </section>

        <!-- Quality facts -->
        <section class="space-y-5 rounded-lg bg-white p-6 shadow">
            <div>
                <h2 class="text-lg font-semibold">Quality facts</h2>
                <p class="text-sm text-gray-600">Buyers see these facts on your product page.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <InputLabel for="quality_level" value="Quality level" />
                    <select id="quality_level" v-model="form.quality_level" required
                        class="mt-1 block w-full rounded-md border-gray-300 capitalize shadow-sm">
                        <option v-for="q in options.qualityLevels" :key="q" :value="q">{{ q }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.quality_level" />
                </div>

                <div>
                    <InputLabel for="grade" value="Grade (for sticks)" />
                    <select id="grade" v-model="form.grade"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">No grade</option>
                        <option v-for="g in options.grades" :key="g" :value="g">{{ g }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.grade" />
                </div>
            </div>

            <div>
                <InputLabel for="ingredients" value="Ingredients" />
                <textarea id="ingredients" v-model="form.ingredients" rows="2" required
                    placeholder="e.g. 100% Ceylon cinnamon (Cinnamomum verum)"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                <InputError class="mt-2" :message="form.errors.ingredients" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <InputLabel for="net_quantity" value="Net quantity" />
                    <TextInput id="net_quantity" v-model="form.net_quantity" type="number" min="1"
                        class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.net_quantity" />
                </div>

                <div>
                    <InputLabel for="unit" value="Unit" />
                    <select id="unit" v-model="form.unit"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option v-for="u in options.units" :key="u" :value="u">{{ u }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.unit" />
                </div>

                <div>
                    <InputLabel for="production_date" value="Production date (optional)" />
                    <TextInput id="production_date" v-model="form.production_date" type="date"
                        class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.production_date" />
                </div>

                <div>
                    <InputLabel for="expiry_date" value="Expiry date (optional)" />
                    <TextInput id="expiry_date" v-model="form.expiry_date" type="date"
                        class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.expiry_date" />
                </div>

                <div>
                    <InputLabel for="batch_code" value="Batch code (optional)" />
                    <TextInput id="batch_code" v-model="form.batch_code" type="text"
                        class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.batch_code" />
                </div>

                <div>
                    <InputLabel for="growing_method" value="Growing method (optional)" />
                    <select id="growing_method" v-model="form.growing_method"
                        class="mt-1 block w-full rounded-md border-gray-300 capitalize shadow-sm">
                        <option value="">Not stated</option>
                        <option v-for="m in options.growingMethods" :key="m" :value="m">{{ m }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.growing_method" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.is_organic" class="rounded border-gray-300" />
                Certified organic
            </label>
        </section>

        <!-- Selling -->
        <section class="space-y-5 rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold">Selling</h2>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <InputLabel for="price" value="Price (USD)" />
                    <TextInput id="price" v-model="form.price" type="number" step="0.01" min="0.01"
                        class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.price" />
                </div>

                <div>
                    <InputLabel for="stock" value="Stock (units available)" />
                    <TextInput id="stock" v-model="form.stock" type="number" min="0"
                        class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.stock" />
                </div>
            </div>

            <div>
                <InputLabel for="image" value="Product photo (optional, max 2 MB)" />
                <img v-if="isEdit && product.image_url" :src="product.image_url" alt=""
                    class="mt-2 h-24 w-24 rounded object-cover" />
                <input id="image" type="file" accept="image/*" class="mt-2 block w-full text-sm"
                    @change="form.image = $event.target.files[0] ?? null" />
                <InputError class="mt-2" :message="form.errors.image" />
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" v-model="form.is_published" class="rounded border-gray-300" />
                Show this product in the marketplace
            </label>
        </section>

        <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">{{ isEdit ? 'Save changes' : 'Add product' }}</PrimaryButton>
            <Link :href="route('seller.products.index')" class="text-sm text-gray-600 underline">Cancel</Link>
        </div>
    </form>
</template>