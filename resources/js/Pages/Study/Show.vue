<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({ trial: Object });
const { locale } = useI18n();

const start = () => router.post(route('study.start', props.trial.id));
</script>

<template>
    <Head title="Study task" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">
                Task for {{ trial.participant }} ·
                {{ trial.channel === 'sms' ? 'SMS' : 'App form' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                <p class="cin-muted mb-3 text-sm">
                    Read this to the participant (use the language switcher for Sinhala / Tamil).
                    The timer starts only when you press the button.
                </p>

                <div class="cin-card p-6 text-lg leading-relaxed">
                    {{ trial.text[locale] ?? trial.text.en }}
                </div>

                <div class="mt-6">
                    <button v-if="!trial.started" class="cin-btn" @click="start">Start timer</button>
                    <p v-else class="cin-muted text-sm">This trial has already started.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
