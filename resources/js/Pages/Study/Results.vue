<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ summary: Object, trials: Array, sus: Array });

const rows = [
    ['Finished trials', 'trials'],
    ['Successful', 'success'],
    ['Success rate (%)', 'success_rate'],
    ['Median time, successful trials (s)', 'median_seconds'],
    ['Mean attempts', 'mean_attempts'],
    ['Mean failed attempts', 'mean_errors'],
    ['Mean data errors (0-5)', 'mean_data_errors'],
    ['SUS mean (0-100)', 'sus_mean'],
    ['SUS responses', 'sus_n'],
];

const show = (v) => (v === null || v === undefined ? '–' : v);
</script>

<template>
    <Head title="Study results" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">Study results: SMS vs app</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl space-y-8 px-4 sm:px-6 lg:px-8">
                <div class="flex gap-4 text-sm">
                    <Link :href="route('study.index')" class="underline">Back to study</Link>
                    <a :href="route('study.export')" class="underline">Download trials (CSV)</a>
                    <a :href="route('study.export', { type: 'sus' })" class="underline">Download SUS (CSV)</a>
                </div>

                <div class="cin-card overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Measure</th>
                                <th class="px-4 py-2">SMS</th>
                                <th class="px-4 py-2">App form</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="[label, key] in rows" :key="key" class="border-t">
                                <td class="px-4 py-2">{{ label }}</td>
                                <td class="px-4 py-2 font-medium">{{ show(summary.sms[key]) }}</td>
                                <td class="px-4 py-2 font-medium">{{ show(summary.app[key]) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cin-card overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Participant</th>
                                <th class="px-4 py-2">Channel</th>
                                <th class="px-4 py-2">Success</th>
                                <th class="px-4 py-2">Time (s)</th>
                                <th class="px-4 py-2">Attempts</th>
                                <th class="px-4 py-2">Failed</th>
                                <th class="px-4 py-2">Data errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="t in trials" :key="t.id" class="border-t">
                                <td class="px-4 py-2 font-medium">{{ t.participant }}</td>
                                <td class="px-4 py-2 uppercase">{{ t.channel }}</td>
                                <td class="px-4 py-2">{{ t.success ? 'yes' : 'gave up' }}</td>
                                <td class="px-4 py-2">{{ t.duration_ms !== null ? (t.duration_ms / 1000).toFixed(1) : '–' }}</td>
                                <td class="px-4 py-2">{{ t.attempts }}</td>
                                <td class="px-4 py-2">{{ t.errors }}</td>
                                <td class="px-4 py-2">{{ t.data_errors ?? '–' }}</td>
                            </tr>
                            <tr v-if="trials.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">No finished trials yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="cin-card overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Participant</th>
                                <th class="px-4 py-2">Channel</th>
                                <th class="px-4 py-2">SUS score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in sus" :key="s.participant + s.channel" class="border-t">
                                <td class="px-4 py-2 font-medium">{{ s.participant }}</td>
                                <td class="px-4 py-2 uppercase">{{ s.channel }}</td>
                                <td class="px-4 py-2">{{ s.score }}</td>
                            </tr>
                            <tr v-if="sus.length === 0">
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">No SUS responses yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
