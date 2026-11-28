<script setup>
import SellerLayout from '@/Layouts/SellerLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({ products: Array });

const badge = {
    high: 'bg-green-100 text-green-800',
    medium: 'bg-amber-100 text-amber-800',
    low: 'bg-gray-200 text-gray-700',
};

const remove = (product) => {
    if (confirm('Delete "' + product.name + '"?')) {
        router.delete(route('seller.products.destroy', product.id));
    }
};
</script>

<template>
    <Head title="My products" />

    <SellerLayout>
        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">My products</h1>
                    <Link :href="route('seller.products.create')"
                        class="rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-700">
                        Add product
                    </Link>
                </div>

                <div v-if="products.length === 0" class="rounded-lg bg-white p-8 text-center shadow">
                    <p class="text-gray-600">You have no products yet. Add your first product to start selling.</p>
                </div>

                <div v-else class="overflow-x-auto rounded-lg bg-white shadow">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Quality</th>
                                <th class="px-4 py-3">Price</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="p in products" :key="p.id">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img v-if="p.image_url" :src="p.image_url" alt=""
                                            class="h-10 w-10 rounded object-cover" />
                                        <div v-else class="h-10 w-10 rounded bg-gray-100"></div>
                                        <div>
                                            <div class="font-medium">{{ p.name }}</div>
                                            <div class="text-xs text-gray-500">{{ p.net_quantity }} {{ p.unit }}<span v-if="p.grade"> · {{ p.grade }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ p.category?.name }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                        :class="badge[p.quality_level]">{{ p.quality_level }}</span>
                                </td>
                                <td class="px-4 py-3">${{ p.price }}</td>
                                <td class="px-4 py-3">{{ p.stock }}</td>
                                <td class="px-4 py-3">{{ p.is_published ? 'Published' : 'Hidden' }}</td>
                                <td class="space-x-3 px-4 py-3 text-right">
                                    <Link :href="route('seller.products.edit', p.id)" class="text-green-800 underline">Edit</Link>
                                    <button type="button" class="text-red-600 underline" @click="remove(p)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </SellerLayout>
</template>