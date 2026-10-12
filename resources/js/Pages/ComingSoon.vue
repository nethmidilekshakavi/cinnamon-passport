<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ title: String, message: String });
</script>

<template>
    <Head :title="title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ title }}</h2>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            <div class="cin-card p-8">
                <p class="font-display text-lg font-semibold">Nothing here yet</p>
                <p class="cin-muted mt-2">{{ message }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
