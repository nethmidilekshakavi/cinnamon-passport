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
            <h2 class="font-display text-xl font-bold">
                {{ t('study.taskFor') }} {{ trial.participant }} ·
                {{ trial.channel === 'sms' ? t('sus.channelSms') : t('sus.channelApp') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                <p class="cin-muted mb-3 text-sm">{{ t('study.readAloud') }}</p>

                <div class="cin-card p-6 text-lg leading-relaxed">
                    {{ trial.text[locale] ?? trial.text.en }}
                </div>

                <div class="mt-6">
                    <button v-if="!trial.started" class="cin-btn" @click="start">{{ t('study.startTimer') }}</button>
                    <p v-else class="cin-muted text-sm">{{ t('study.alreadyStarted') }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
