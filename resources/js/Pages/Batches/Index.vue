<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { useI18n } from '@/i18n';

defineProps({ batches: Array, collectors: Array });

const { t } = useI18n();

// Chosen collector for each batch row.
const pick = reactive({});

const handOver = (b) => {
    router.post(
        route('batches.handover', b.id),
        { collector_id: pick[b.id] },
        { preserveScroll: true },
    );
};

// Show the translated status when one exists, otherwise the raw value.
const statusLabel = (status) => {
    const key = `status.${status}`;
    const label = t(key);
    return label === key ? status : label;
};
</script>

<template>
    <Head :title="t('batch.listTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t('batch.listTitle') }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="mb-4 flex gap-2">
                    <Link :href="route('batches.create')" class="cin-btn">
                        {{ t('nav.newBatch') }}
                    </Link>
                    <Link :href="route('sms.index')" class="cin-btn cin-btn-light">
                        {{ t('sms.title') }}
                    </Link>
                </div>

                <div class="cin-card overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-2">{{ t('batch.col.code') }}</th>
                            <th class="px-4 py-2">{{ t('batch.col.plot') }}</th>
                            <th class="px-4 py-2">{{ t('batch.col.kg') }}</th>
                            <th class="px-4 py-2">{{ t('batch.col.harvest') }}</th>
                            <th class="px-4 py-2">{{ t('batch.col.drying') }}</th>
                            <th class="px-4 py-2">{{ t('batch.col.grade') }}</th>
                            <th class="px-4 py-2">{{ t('batch.col.status') }}</th>
                            <th class="px-4 py-2">{{ t('handover.column') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="b in batches" :key="b.id" class="border-t">
                            <td class="px-4 py-2 font-medium">{{ b.code }}</td>
                            <td class="px-4 py-2">{{ b.plot }}</td>
                            <td class="px-4 py-2">{{ b.weight_kg }}</td>
                            <td class="px-4 py-2">{{ String(b.harvest_date).slice(0, 10) }}</td>
                            <td class="px-4 py-2">{{ b.drying_days }} {{ t('batch.days') }}</td>
                            <td class="px-4 py-2">{{ b.grade ?? '-' }}</td>
                            <td class="px-4 py-2">
                                {{ statusLabel(b.status) }}
                                <div v-if="b.flags && b.flags.length" class="mt-1 flex flex-wrap gap-1">
                                        <span
                                            v-for="f in b.flags"
                                            :key="f"
                                            class="rounded bg-red-50 px-2 py-0.5 text-xs text-red-700"
                                        >
                                            {{ t('flag.' + f) }}
                                        </span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                    <span v-if="b.handed_over_at" class="text-xs text-green-700">
                                        {{ t('handover.done') }}: {{ b.collector?.name }}
                                    </span>
                                <span v-else-if="b.handover_requested_at" class="text-xs text-amber-700">
                                        {{ t('handover.pending') }}: {{ b.collector?.name }}
                                    </span>
                                <form
                                    v-else-if="collectors.length"
                                    class="flex gap-1"
                                    @submit.prevent="handOver(b)"
                                >
                                    <select
                                        v-model="pick[b.id]"
                                        class="rounded-md border-gray-300 py-1 text-xs"
                                    >
                                        <option :value="undefined" disabled>{{ t('handover.choose') }}</option>
                                        <option v-for="c in collectors" :key="c.id" :value="c.id">
                                            {{ c.name }}
                                        </option>
                                    </select>
                                    <button class="cin-btn !px-2 !py-1 text-xs" :disabled="!pick[b.id]">
                                        {{ t('handover.send') }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-2">
                                <Link :href="route('verify.show', b.code)" class="text-sm underline">
                                    {{ t('verify.passport') }}
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="batches.length === 0">
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">{{ t('batch.empty') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
