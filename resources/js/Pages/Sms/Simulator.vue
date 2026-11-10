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
            <h2 class="font-display text-xl font-bold text-[#4B2106]">{{ t('sms.title') }}</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                <p class="mb-4 text-sm text-[#3A1B0B]/65">{{ t('sms.hint') }}</p>

                <!-- Phone-style conversation -->
                <div class="overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
                    <div class="flex items-center gap-2 bg-[#4B2106] px-4 py-2.5 text-sm font-semibold text-white">
                        <span class="h-2 w-2 rounded-full bg-[#7DB84A]"></span>
                        Messages
                    </div>

                    <div class="min-h-[180px] space-y-3 bg-[#FDF7E7] p-4">
                        <div v-if="!result" class="py-10 text-center text-sm text-[#3A1B0B]/55">
                            {{ t('sms.empty') }}
                        </div>

                        <template v-else>
                            <div class="flex justify-end">
                                <div class="max-w-[85%] break-words rounded-2xl rounded-br-sm bg-[#4B2106] px-4 py-2 text-sm text-white">
                                    {{ result.sent }}
                                </div>
                            </div>
                            <div class="flex justify-start">
                                <div
                                    class="max-w-[85%] break-words rounded-2xl rounded-bl-sm px-4 py-2 text-sm"
                                    :class="result.ok ? 'bg-[#E4EFD6] text-[#1F3F0F]' : 'bg-[#FDECEA] text-[#A12B1F]'"
                                >
                                    {{ result.reply }}
                                </div>
                            </div>
                        </template>
                    </div>

                    <form @submit.prevent="send" class="flex gap-2 border-t border-[#EADFC8] bg-white p-3">
                        <input
                            v-model="form.message"
                            type="text"
                            maxlength="160"
                            :placeholder="t('sms.placeholder')"
                            class="min-w-0 flex-1 rounded-full border border-[#D9CBA9] bg-white px-4 py-2.5 text-sm text-[#3A1B0B] shadow-none"
                        />
                        <button class="cin-btn shrink-0 !py-2.5" :disabled="form.processing || !form.message">
                            {{ t('sms.send') }}
                        </button>
                    </form>
                </div>
                <p v-if="form.errors.message" class="mt-2 text-sm text-[#A12B1F]">{{ form.errors.message }}</p>

                <!-- Examples -->
                <div class="mt-6">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-[#3A1B0B]/50">{{ t('sms.examples') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="e in role === 'collector' ? collectorExamples : growerExamples"
                            :key="e"
                            type="button"
                            class="rounded-full border border-[#2D5715]/30 bg-white px-3 py-1.5 font-mono text-xs text-[#2D5715] transition hover:bg-[#E4EFD6]"
                            @click="form.message = e"
                        >
                            {{ e }}
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm font-semibold">
                    <Link :href="route('sms.guide')" class="text-[#B5651D] hover:text-[#4B2106]">
                        {{ t('sms.guide') }} →
                    </Link>
                    <Link
                        :href="role === 'collector' ? route('collector.home') : route('batches.index')"
                        class="text-[#2D5715] hover:text-[#4B2106]"
                    >
                        ← {{ role === 'collector' ? t('collector.title') : t('nav.myBatches') }}
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
