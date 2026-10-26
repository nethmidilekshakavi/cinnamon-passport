<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

defineProps({ trials: Array });
const { t } = useI18n();

const form = useForm({ participant: '', channel: 'sms' });
const submit = () => form.post(route('study.store'));

const state = (row) => {
    if (!row.started_at) return t('study.state.notStarted');
    if (!row.finished_at) return t('study.state.running');
    return row.success ? t('study.state.done') : t('study.state.gaveUp');
};
</script>

<template>
    <Head :title="t('study.pageTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t('study.heading') }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="cin-card space-y-4 p-6">
                    <h3 class="font-display text-lg font-bold">{{ t('study.newTask') }}</h3>

                    <div>
                        <label class="block text-sm font-medium">{{ t('study.participantCode') }}</label>
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
                            <input v-model="form.channel" type="radio" value="sms" /> {{ t('sus.channelSms') }}
                        </label>
                        <label class="flex items-center gap-2">
                            <input v-model="form.channel" type="radio" value="app" /> {{ t('sus.channelApp') }}
                        </label>
                    </div>

                    <PrimaryButton :disabled="form.processing || !form.participant">{{ t('study.createTask') }}</PrimaryButton>
                </form>

                <div class="flex gap-4 text-sm">
                    <Link :href="route('study.sus')" class="underline">{{ t('sus.title') }}</Link>
                    <Link :href="route('study.results')" class="underline">{{ t('study.resultsLink') }}</Link>
                </div>

                <div class="cin-card overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">{{ t('study.col.participant') }}</th>
                                <th class="px-4 py-2">{{ t('study.col.channel') }}</th>
                                <th class="px-4 py-2">{{ t('study.col.state') }}</th>
                                <th class="px-4 py-2">{{ t('study.col.time') }}</th>
                                <th class="px-4 py-2">{{ t('study.col.failed') }}</th>
                                <th class="px-4 py-2">{{ t('study.col.dataErrors') }}</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in trials" :key="row.id" class="border-t">
                                <td class="px-4 py-2 font-medium">{{ row.participant }}</td>
                                <td class="px-4 py-2 uppercase">{{ row.channel }}</td>
                                <td class="px-4 py-2">
                                    <Link v-if="!row.started_at" :href="route('study.show', row.id)" class="underline">
                                        {{ t('study.state.notStarted') }}
                                    </Link>
                                    <span v-else>{{ state(row) }}</span>
                                </td>
                                <td class="px-4 py-2">{{ row.duration_ms !== null ? (row.duration_ms / 1000).toFixed(1) : '–' }}</td>
                                <td class="px-4 py-2">{{ row.errors }}</td>
                                <td class="px-4 py-2">{{ row.data_errors ?? '–' }}</td>
                                <td class="px-4 py-2">
                                    <Link
                                        v-if="row.finished_at"
                                        :href="route('study.sus', { participant: row.participant, channel: row.channel })"
                                        class="underline"
                                    >
                                        SUS
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="trials.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">{{ t('study.noTrials') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
