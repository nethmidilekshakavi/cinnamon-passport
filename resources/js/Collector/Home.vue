<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

defineProps({ pending: Array, received: Array });

const { t } = useI18n();

const confirm = (b) => router.post(route('collector.confirm', b.id), {}, { preserveScroll: true });
const day = (value) => String(value).slice(0, 10);
</script>

<template>
    <Head :title="t('collector.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t('collector.title') }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-8 px-4 sm:px-6 lg:px-8">
                <Link :href="route('sms.index')" class="cin-btn">{{ t('collector.sms') }}</Link>

                <section>
                    <h3 class="font-display mb-2 text-lg font-bold">{{ t('collector.pendingTitle') }}</h3>
                    <div class="cin-card overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">{{ t('batch.col.code') }}</th>
                                <th class="px-4 py-2">{{ t('collector.grower') }}</th>
                                <th class="px-4 py-2">{{ t('batch.col.plot') }}</th>
                                <th class="px-4 py-2">{{ t('batch.col.kg') }}</th>
                                <th class="px-4 py-2">{{ t('batch.col.harvest') }}</th>
                                <th class="px-4 py-2">{{ t('batch.col.status') }}</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="b in pending" :key="b.id" class="border-t">
                                <td class="px-4 py-2 font-medium">{{ b.code }}</td>
                                <td class="px-4 py-2">{{ b.grower?.name }}</td>
                                <td class="px-4 py-2">{{ b.plot }}</td>
                                <td class="px-4 py-2">{{ b.weight_kg }}</td>
                                <td class="px-4 py-2">{{ day(b.harvest_date) }}</td>
                                <td class="px-4 py-2">
                                        <span
                                            v-for="f in b.flags ?? []"
                                            :key="f"
                                            class="mr-1 rounded bg-red-50 px-2 py-0.5 text-xs text-red-700"
                                        >
                                            {{ t('flag.' + f) }}
                                        </span>
                                </td>
                                <td class="px-4 py-2">
                                    <button class="cin-btn" @click="confirm(b)">{{ t('collector.confirm') }}</button>
                                </td>
                            </tr>
                            <tr v-if="pending.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">{{ t('collector.noPending') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section>
                    <h3 class="font-display mb-2 text-lg font-bold">{{ t('collector.receivedTitle') }}</h3>
                    <div class="cin-card overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-2">{{ t('batch.col.code') }}</th>
                                <th class="px-4 py-2">{{ t('collector.grower') }}</th>
                                <th class="px-4 py-2">{{ t('batch.col.plot') }}</th>
                                <th class="px-4 py-2">{{ t('batch.col.kg') }}</th>
                                <th class="px-4 py-2">{{ t('collector.receivedOn') }}</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="b in received" :key="b.id" class="border-t">
                                <td class="px-4 py-2 font-medium">{{ b.code }}</td>
                                <td class="px-4 py-2">{{ b.grower?.name }}</td>
                                <td class="px-4 py-2">{{ b.plot }}</td>
                                <td class="px-4 py-2">{{ b.weight_kg }}</td>
                                <td class="px-4 py-2">{{ day(b.handed_over_at) }}</td>
                                <td class="px-4 py-2">
                                    <Link :href="route('verify.show', b.code)" class="text-sm underline">
                                        {{ t('verify.passport') }}
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="received.length === 0">
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">{{ t('collector.noReceived') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
