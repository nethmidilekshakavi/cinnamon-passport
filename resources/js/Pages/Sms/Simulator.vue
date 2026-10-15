<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

defineProps({ result: Object, role: String });

const { t } = useI18n();
const form = useForm({ message: '' });

const growerExamples = [
    'BATCH P1 40KG 12OCT DRY3 ALBA',
    'තොගය P2 30KG 11OCT DRY2 C5',
    'தொகுதி P3 25KG 10OCT DRY4',
    'HAND CIN-0001 0771234567',
];
const collectorExamples = ['OK CIN-0001', 'තහවුරු CIN-0001', 'உறுதி CIN-0001'];

const send = () => {
    form.post(route('sms.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('message'),
    });
};
</script>

<template>
    <Head :title="t('sms.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t('sms.title') }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                <p class="cin-muted mb-4 text-sm">{{ t('sms.hint') }}</p>

                <div class="cin-card space-y-3 p-4">
                    <div v-if="!result" class="cin-muted py-6 text-center text-sm">
                        {{ t('sms.empty') }}
                    </div>

                    <template v-else>
                        <div class="flex justify-end">
                            <div class="max-w-[85%] rounded-2xl rounded-br-sm bg-[#5A2D17] px-4 py-2 text-sm text-white">
                                {{ result.sent }}
                            </div>
                        </div>
                        <div class="flex justify-start">
                            <div
                                class="max-w-[85%] rounded-2xl rounded-bl-sm border px-4 py-2 text-sm"
                                :class="result.ok ? 'border-green-700 bg-green-50' : 'border-red-600 bg-red-50'"
                            >
                                {{ result.reply }}
                            </div>
                        </div>
                    </template>
                </div>

                <form @submit.prevent="send" class="mt-4 flex gap-2">
                    <input
                        v-model="form.message"
                        type="text"
                        maxlength="160"
                        :placeholder="t('sms.placeholder')"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#5A2D17] focus:ring-[#5A2D17]"
                    />
                    <button class="cin-btn" :disabled="form.processing || !form.message">
                        {{ t('sms.send') }}
                    </button>
                </form>
                <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>

                <div class="mt-4">
                    <p class="cin-muted mb-2 text-xs">{{ t('sms.examples') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="e in role === 'collector' ? collectorExamples : growerExamples"
                            :key="e"
                            type="button"
                            class="rounded-full border border-[#E4D6C8] bg-white px-3 py-1 text-xs hover:bg-[#FAF6F2]"
                            @click="form.message = e"
                        >
                            {{ e }}
                        </button>
                    </div>
                </div>

                <Link
                    :href="role === 'collector' ? route('collector.home') : route('batches.index')"
                    class="cin-muted mt-6 inline-block text-sm underline"
                >
                    {{ role === 'collector' ? t('collector.title') : t('nav.myBatches') }}
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
