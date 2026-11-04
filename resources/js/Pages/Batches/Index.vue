<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import { useI18n } from '@/i18n';

const props = defineProps({ batches: Array, collectors: Array });

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

// Two quick counts, reusing the existing handover labels.
const doneCount = computed(() => props.batches.filter((b) => b.handed_over_at).length);
const pendingCount = computed(() => props.batches.filter((b) => !b.handed_over_at && b.handover_requested_at).length);

const fields = (b) => [
    { label: t('batch.col.plot'), value: b.plot },
    { label: t('batch.col.kg'), value: b.weight_kg },
    { label: t('batch.col.harvest'), value: String(b.harvest_date).slice(0, 10) },
    { label: t('batch.col.drying'), value: `${b.drying_days} ${t('batch.days')}` },
    { label: t('batch.col.grade'), value: b.grade ?? '-' },
];
</script>

<template>
    <Head :title="t('batch.listTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t('batch.listTitle') }}</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <!-- Actions and counts -->
                <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap gap-3">
                        <Link
                            :href="route('batches.create')"
                            class="inline-flex min-h-[46px] items-center justify-center rounded-full bg-[#14503A] px-6 font-bold text-white shadow-lg shadow-[#14503A]/25 transition hover:bg-[#0B3B2C] active:scale-[.98]"
                        >
                            + {{ t('nav.newBatch') }}
                        </Link>
                        <Link
                            :href="route('sms.index')"
                            class="inline-flex min-h-[46px] items-center justify-center rounded-full bg-[#E3B04B] px-6 font-bold text-[#14241B] transition hover:bg-[#F0C566] active:scale-[.98]"
                        >
                            {{ t('sms.title') }}
                        </Link>
                    </div>

                    <div class="flex flex-wrap gap-2 text-sm font-semibold">
                        <span class="rounded-full bg-[#DDEFE3] px-4 py-1.5 text-[#14503A]">
                            {{ t('handover.done') }} · {{ doneCount }}
                        </span>
                        <span class="rounded-full bg-[#FBEBC8] px-4 py-1.5 text-[#8A5A0B]">
                            {{ t('handover.pending') }} · {{ pendingCount }}
                        </span>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="batches.length === 0" class="rounded-3xl bg-white px-6 py-16 text-center text-[#14241B]/60 ring-1 ring-[#E8DFC9]">
                    {{ t('batch.empty') }}
                </div>

                <!-- Batch cards -->
                <div v-else class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="b in batches"
                        :key="b.id"
                        class="flex flex-col rounded-3xl bg-white p-5 text-[#14241B] shadow-sm ring-1 ring-[#E8DFC9] transition hover:-translate-y-0.5 hover:shadow-xl"
                    >
                        <header class="flex items-start justify-between gap-3">
                            <p class="font-display text-2xl font-extrabold">{{ b.code }}</p>
                            <span class="shrink-0 rounded-full bg-[#DDEFE3] px-3 py-1 text-xs font-bold text-[#14503A]">
                                {{ statusLabel(b.status) }}
                            </span>
                        </header>

                        <div v-if="b.flags && b.flags.length" class="mt-3 flex flex-wrap gap-1.5">
                            <span
                                v-for="f in b.flags"
                                :key="f"
                                class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700"
                            >
                                {{ t('flag.' + f) }}
                            </span>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-2 text-sm">
                            <div v-for="(f, i) in fields(b)" :key="i" class="rounded-xl bg-[#FFF9EE] px-3 py-2 ring-1 ring-[#E8DFC9]">
                                <dt class="text-xs text-[#14241B]/55">{{ f.label }}</dt>
                                <dd class="mt-0.5 font-semibold">{{ f.value }}</dd>
                            </div>
                        </dl>

                        <!-- Handover -->
                        <div class="mt-4 border-t border-dashed border-[#E8DFC9] pt-4">
                            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-[#14241B]/50">
                                {{ t('handover.column') }}
                            </p>
                            <span v-if="b.handed_over_at" class="text-sm font-semibold text-[#14503A]">
                                ✓ {{ t('handover.done') }}: {{ b.collector?.name }}
                            </span>
                            <span v-else-if="b.handover_requested_at" class="text-sm font-semibold text-[#8A5A0B]">
                                {{ t('handover.pending') }}: {{ b.collector?.name }}
                            </span>
                            <form v-else-if="collectors.length" class="flex gap-2" @submit.prevent="handOver(b)">
                                <select v-model="pick[b.id]" class="cw-select min-w-0 flex-1">
                                    <option :value="undefined" disabled>{{ t('handover.choose') }}</option>
                                    <option v-for="c in collectors" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </option>
                                </select>
                                <button
                                    class="shrink-0 rounded-full bg-[#14503A] px-4 text-sm font-bold text-white transition hover:bg-[#0B3B2C] disabled:cursor-not-allowed disabled:opacity-40"
                                    :disabled="!pick[b.id]"
                                >
                                    {{ t('handover.send') }}
                                </button>
                            </form>
                        </div>

                        <Link
                            :href="route('verify.show', b.code)"
                            class="mt-auto inline-flex items-center gap-1 pt-5 text-sm font-bold text-[#B5602A] hover:text-[#8E4A1F]"
                        >
                            {{ t('verify.passport') }} <span aria-hidden="true">→</span>
                        </Link>
                    </article>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cw-select {
    padding: 0.5rem 0.75rem;
    border: 1.5px solid #e8dfc9;
    border-radius: 0.75rem;
    background: #fffdf8;
    font-size: 0.85rem;
}
/* Beats the global brown focus ring in app.css */
.cw-select:focus {
    border-color: #14503a !important;
    box-shadow: 0 0 0 3px rgba(30, 107, 76, 0.2) !important;
    outline: none !important;
}
</style>
