<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ brand: Object });
</script>

<template>
    <Head title="Seller dashboard" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold">{{ brand.name }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ brand.district }}, {{ brand.country }}</p>
                <p v-if="brand.description" class="mt-3">{{ brand.description }}</p>

                <div class="mt-6 rounded-lg bg-white p-6 shadow">
                    <h2 class="font-semibold">Products ({{ brand.products_count }})</h2>
                    <div class="mt-4 flex gap-3">
                        <Link :href="route('seller.products.index')"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium hover:bg-gray-50">
                            Manage products
                        </Link>
                        <Link :href="route('seller.products.create')"
                            class="rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-700">
                            Add product
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>