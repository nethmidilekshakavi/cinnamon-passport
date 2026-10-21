<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ trials: Array });

const form = useForm({ participant: '', channel: 'sms' });
const submit = () => form.post(route('study.store'));

const state = (t) => {
    if (!t.started_at) return 'not started';
    if (!t.finished_at) return 'running';
    return t.success ? 'done' : 'gave up';
};
</script>

<template>
    <Head title="Usability study" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">Usability study (RQ1)</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="cin-card space-y-4 p-6">
                    <h3 class="font-display text-lg font-bold">New task</h3>

                    <div>
                        <label class="block text-sm font-medium">Participant code (e.g. P01)</label>
                        <input
                            v-model="form.participant"
                            type="text"
                            maxlength="20"
                            class="mt-1 block w-full rounded-md border-gray-300 uppercase shadow-sm focus:border-[#5A2D17] focus:ring-[#5A2D17]"
                        />
                        <InputError class="mt-2" :message="form.errors.participant" />
                    </div>

                    <div class="flex gap-6 text-sm">
                        <label class="flex items-center gap-2">
                            <input v-model="form.channel" type="radio" value="sms" /> SMS
                        </label>
                        <label class="flex items-center gap-2">
                            <input v-model="form.channel" type="radio" value="app" /> App form
                        </label>
                    </div>

                    <PrimaryButton :disabled="form.processing || !form.participant">Create task</PrimaryButton>
                </form>

                <div class="flex gap-4 text-sm">
                    <Link :href="route('study.sus')" class="underline">SUS questionnaire</Link>
                    <Link :href="route('study.results')" class="underline">Results</Link>
                </div>

                <div class="cin-card overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Participant</th>
                                <th class="px-4 py-2">Channel</th>
                                <th class="px-4 py-2">State</th>
                                <th class="px-4 py-2">Time (s)</th>
                                <th class="px-4 py-2">Failed</th>
                                <th class="px-4 py-2">Data errors</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="t in trials" :key="t.id" class="border-t">
                                <td class="px-4 py-2 font-medium">{{ t.participant }}</td>
                                <td class="px-4 py-2 uppercase">{{ t.channel }}</td>
                                <td class="px-4 py-2">
                                    <Link v-if="!t.started_at" :href="route('study.show', t.id)" class="underline">
                                        not started
                                    </Link>
                                    <span v-else>{{ state(t) }}</span>
                                </td>
                                <td class="px-4 py-2">{{ t.duration_ms !== null ? (t.duration_ms / 1000).toFixed(1) : '–' }}</td>
                                <td class="px-4 py-2">{{ t.errors }}</td>
                                <td class="px-4 py-2">{{ t.data_errors ?? '–' }}</td>
                                <td class="px-4 py-2">
                                    <Link
                                        v-if="t.finished_at"
                                        :href="route('study.sus', { participant: t.participant, channel: t.channel })"
                                        class="underline"
                                    >
                                        SUS
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="trials.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">No trials yet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
