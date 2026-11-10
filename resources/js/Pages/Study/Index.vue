<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
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

// Badge colours: waiting = grey-brown, running = gold, done = green, gave up = red.
const stateClass = (row) => {
    if (!row.started_at) return 'bg-[#F1E8D3] text-[#6F5A4E]';
    if (!row.finished_at) return 'bg-[#FBEBC8] text-[#8A5A0B]';
    return row.success ? 'bg-[#E4EFD6] text-[#2D5715]' : 'bg-[#FDECEA] text-[#A12B1F]';
};

const th = 'px-4 py-3 text-xs font-semibold uppercase tracking-wide text-[#4B2106]';
const td = 'px-4 py-3';
</script>

<template>
    <Head :title="t('study.pageTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">{{ t('study.heading') }}</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <!-- New task -->
                <form @submit.prevent="submit" class="overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
                    <div class="h-1 bg-[#4A8A24]"></div>
                    <div class="space-y-4 p-5 sm:p-6">
                        <h3 class="font-display text-lg font-bold text-[#4B2106]">{{ t('study.newTask') }}</h3>

                        <div>
                            <label class="block text-sm font-semibold text-[#4B2106]">{{ t('study.participantCode') }}</label>
                            <input
                                v-model="form.participant"
                                type="text"
                                maxlength="20"
                                class="mt-1.5 block w-full rounded-lg border border-[#D9CBA9] bg-white px-3 py-2.5 uppercase text-[#3A1B0B] shadow-none sm:max-w-xs"
                            />
                            <InputError class="mt-2" :message="form.errors.participant" />
                        </div>

                        <div class="flex flex-wrap gap-3 text-sm">
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 transition"
                                :class="form.channel === 'sms' ? 'border-[#2D5715] bg-[#E4EFD6] text-[#2D5715]' : 'border-[#D9CBA9] text-[#3A1B0B]'"
                            >
                                <input v-model="form.channel" type="radio" value="sms" class="accent-[#2D5715]" />
                                {{ t('sus.channelSms') }}
                            </label>
                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 transition"
                                :class="form.channel === 'app' ? 'border-[#2D5715] bg-[#E4EFD6] text-[#2D5715]' : 'border-[#D9CBA9] text-[#3A1B0B]'"
                            >
                                <input v-model="form.channel" type="radio" value="app" class="accent-[#2D5715]" />
                                {{ t('sus.channelApp') }}
                            </label>
                        </div>

                        <button
                            class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-[#2D5715] px-7 text-sm font-bold text-white transition hover:bg-[#1F3F0F] active:scale-[.98] disabled:opacity-50"
                            :disabled="form.processing || !form.participant"
                        >
                            {{ t('study.createTask') }}
                        </button>
                    </div>
                </form>

                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold">
                    <Link :href="route('study.sus')" class="text-[#2D5715] hover:text-[#4B2106]">{{ t('sus.title') }} →</Link>
                    <Link :href="route('study.results')" class="text-[#B5651D] hover:text-[#4B2106]">{{ t('study.resultsLink') }} →</Link>
                </div>

                <!-- Trials table -->
                <div class="overflow-x-auto rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#FDF7E7]">
                            <tr>
                                <th :class="th">{{ t('study.col.participant') }}</th>
                                <th :class="th">{{ t('study.col.channel') }}</th>
                                <th :class="th">{{ t('study.col.state') }}</th>
                                <th :class="th">{{ t('study.col.time') }}</th>
                                <th :class="th">{{ t('study.col.failed') }}</th>
                                <th :class="th">{{ t('study.col.dataErrors') }}</th>
                                <th :class="th"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1E8D3]">
                            <tr v-for="row in trials" :key="row.id" class="hover:bg-[#FDF7E7]/60">
                                <td :class="[td, 'font-semibold text-[#4B2106]']">{{ row.participant }}</td>
                                <td :class="[td, 'uppercase']">{{ row.channel }}</td>
                                <td :class="td">
                                    <Link
                                        v-if="!row.started_at"
                                        :href="route('study.show', row.id)"
                                        class="rounded-full px-3 py-1 text-xs font-semibold underline-offset-2 hover:underline"
                                        :class="stateClass(row)"
                                    >
                                        {{ t('study.state.notStarted') }}
                                    </Link>
                                    <span v-else class="rounded-full px-3 py-1 text-xs font-semibold" :class="stateClass(row)">
                                        {{ state(row) }}
                                    </span>
                                </td>
                                <td :class="td">{{ row.duration_ms !== null ? (row.duration_ms / 1000).toFixed(1) : '–' }}</td>
                                <td :class="td">{{ row.errors }}</td>
                                <td :class="td">{{ row.data_errors ?? '–' }}</td>
                                <td :class="td">
                                    <Link
                                        v-if="row.finished_at"
                                        :href="route('study.sus', { participant: row.participant, channel: row.channel })"
                                        class="font-semibold text-[#B5651D] hover:text-[#4B2106]"
                                    >
                                        SUS
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="trials.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-[#3A1B0B]/55">{{ t('study.noTrials') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
