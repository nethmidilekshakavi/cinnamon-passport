<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ batches: Array });
</script>

<template>
    <Head title="My batches" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">My batches</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <Link :href="route('batches.create')" class="mb-4 inline-block rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
                    New batch
                </Link>

                <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">Code</th>
                            <th class="px-4 py-2">Plot</th>
                            <th class="px-4 py-2">Kg</th>
                            <th class="px-4 py-2">Harvest</th>
                            <th class="px-4 py-2">Drying</th>
                            <th class="px-4 py-2">Grade</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="b in batches" :key="b.id" class="border-t">
                            <td class="px-4 py-2 font-medium">{{ b.code }}</td>
                            <td class="px-4 py-2">{{ b.plot }}</td>
                            <td class="px-4 py-2">{{ b.weight_kg }}</td>
                            <td class="px-4 py-2">{{ String(b.harvest_date).slice(0, 10) }}</td>
                            <td class="px-4 py-2">{{ b.drying_days }} d</td>
                            <td class="px-4 py-2">{{ b.grade ?? '-' }}</td>
                            <td class="px-4 py-2">{{ b.status }}</td>
                        </tr>
                        <tr v-if="batches.length === 0">
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No batches yet.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
