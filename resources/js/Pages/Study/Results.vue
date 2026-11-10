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

const panel = 'overflow-x-auto rounded-2xl border border-[#EADFC8] bg-white shadow-sm';
const th = 'px-4 py-3 text-xs font-semibold uppercase tracking-wide text-[#4B2106]';
const td = 'px-4 py-3';
</script>

<template>
    <Head title="Study results" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">Study results: SMS vs app</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold">
                    <Link :href="route('study.index')" class="text-[#2D5715] hover:text-[#4B2106]">← Back to study</Link>
                    <a :href="route('study.export')" class="text-[#B5651D] hover:text-[#4B2106]">Download trials (CSV)</a>
                    <a :href="route('study.export', { type: 'sus' })" class="text-[#B5651D] hover:text-[#4B2106]">Download SUS (CSV)</a>
                </div>

                <!-- Summary -->
                <div :class="panel">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#4B2106] text-white">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">Measure</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">SMS</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide">App form</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1E8D3]">
                            <tr v-for="[label, key] in rows" :key="key" class="hover:bg-[#FDF7E7]/60">
                                <td :class="[td, 'text-[#3A1B0B]/80']">{{ label }}</td>
                                <td :class="[td, 'font-semibold text-[#2D5715]']">{{ show(summary.sms[key]) }}</td>
                                <td :class="[td, 'font-semibold text-[#B5651D]']">{{ show(summary.app[key]) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Trials -->
                <div :class="panel">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#FDF7E7]">
                            <tr>
                                <th :class="th">Participant</th>
                                <th :class="th">Channel</th>
                                <th :class="th">Success</th>
                                <th :class="th">Time (s)</th>
                                <th :class="th">Attempts</th>
                                <th :class="th">Failed</th>
                                <th :class="th">Data errors</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1E8D3]">
                            <tr v-for="t in trials" :key="t.id" class="hover:bg-[#FDF7E7]/60">
                                <td :class="[td, 'font-semibold text-[#4B2106]']">{{ t.participant }}</td>
                                <td :class="[td, 'uppercase']">{{ t.channel }}</td>
                                <td :class="td">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="t.success ? 'bg-[#E4EFD6] text-[#2D5715]' : 'bg-[#FDECEA] text-[#A12B1F]'"
                                    >
                                        {{ t.success ? 'yes' : 'gave up' }}
                                    </span>
                                </td>
                                <td :class="td">{{ t.duration_ms !== null ? (t.duration_ms / 1000).toFixed(1) : '–' }}</td>
                                <td :class="td">{{ t.attempts }}</td>
                                <td :class="td">{{ t.errors }}</td>
                                <td :class="td">{{ t.data_errors ?? '–' }}</td>
                            </tr>
                            <tr v-if="trials.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-[#3A1B0B]/55">No finished trials yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- SUS -->
                <div :class="panel">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#FDF7E7]">
                            <tr>
                                <th :class="th">Participant</th>
                                <th :class="th">Channel</th>
                                <th :class="th">SUS score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1E8D3]">
                            <tr v-for="s in sus" :key="s.participant + s.channel" class="hover:bg-[#FDF7E7]/60">
                                <td :class="[td, 'font-semibold text-[#4B2106]']">{{ s.participant }}</td>
                                <td :class="[td, 'uppercase']">{{ s.channel }}</td>
                                <td :class="[td, 'font-semibold']">{{ s.score }}</td>
                            </tr>
                            <tr v-if="sus.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-[#3A1B0B]/55">No SUS responses yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
