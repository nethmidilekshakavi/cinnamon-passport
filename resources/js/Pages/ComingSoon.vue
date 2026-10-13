<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

defineProps({ titleKey: String, messageKey: String });

const { t } = useI18n();
</script>

<template>
    <Head :title="t(titleKey)" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t(titleKey) }}</h2>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            <div class="cin-card p-8">
                <p class="font-display text-lg font-semibold">{{ t('soon.title') }}</p>
                <p class="cin-muted mt-2">{{ t(messageKey) }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
