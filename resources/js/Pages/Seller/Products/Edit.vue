<script setup>
import SellerLayout from '@/Layouts/SellerLayout.vue';
import ProductForm from '@/Components/Seller/ProductForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ product: Object, options: Object });
</script>

<template>
    <Head title="Edit product" />

    <SellerLayout>
        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <h1 class="mb-6 text-2xl font-semibold">Edit product</h1>
                <ProductForm :product="product" :options="options" />
            </div>
        </div>
    </SellerLayout>
</template>