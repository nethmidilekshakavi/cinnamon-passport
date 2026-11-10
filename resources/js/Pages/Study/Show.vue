<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({ trial: Object });
const { t, locale } = useI18n();

const start = () => router.post(route('study.start', props.trial.id));
</script>

<template>
    <Head :title="t('study.taskTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">
                {{ t('study.taskFor') }} {{ trial.participant }} ·
                {{ trial.channel === 'sms' ? t('sus.channelSms') : t('sus.channelApp') }}
            </h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                <p class="mb-3 text-sm text-[#3A1B0B]/65">{{ t('study.readAloud') }}</p>

                <div class="overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
                    <div class="h-1 bg-[#4A8A24]"></div>
                    <div class="p-6 text-lg leading-relaxed text-[#3A1B0B]">
                        {{ trial.text[locale] ?? trial.text.en }}
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        v-if="!trial.started"
                        class="inline-flex min-h-[48px] items-center justify-center rounded-full bg-[#2D5715] px-8 text-sm font-bold text-white transition hover:bg-[#1F3F0F] active:scale-[.98]"
                        @click="start"
                    >
                        {{ t('study.startTimer') }}
                    </button>
                    <p v-else class="rounded-lg bg-[#FBEBC8] px-4 py-3 text-sm font-medium text-[#8A5A0B]">
                        {{ t('study.alreadyStarted') }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
