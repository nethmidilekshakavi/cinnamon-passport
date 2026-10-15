<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const { t } = useI18n();
const form = useForm({ code: '' });

const submit = () => form.post(route('verify.lookup'));
</script>

<template>
    <Head :title="t('verify.title')" />

    <PublicLayout>
        <h1 class="cin-h1 mt-6">{{ t('verify.title') }}</h1>
        <p class="cin-muted mt-2 text-sm">{{ t('verify.hint') }}</p>

        <form @submit.prevent="submit" class="cin-card mt-6 p-4">
            <div class="flex gap-2">
                <input
                    v-model="form.code"
                    type="text"
                    maxlength="20"
                    autofocus
                    :placeholder="t('verify.placeholder')"
                    class="w-full rounded-md border-gray-300 uppercase shadow-sm focus:border-[#5A2D17] focus:ring-[#5A2D17]"
                />
                <PrimaryButton :disabled="form.processing || !form.code">
                    {{ t('verify.check') }}
                </PrimaryButton>
            </div>
            <p v-if="form.errors.code" class="mt-2 text-sm text-red-600">
                {{ t('verify.notFound') }}
            </p>
        </form>
    </PublicLayout>
</template>
