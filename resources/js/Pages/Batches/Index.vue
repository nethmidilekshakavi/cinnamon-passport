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

const isFlagged = (b) => (b.flags && b.flags.length > 0) || b.status === 'flagged';

const doneCount = computed(() => props.batches.filter((b) => b.handed_over_at).length);
const pendingCount = computed(() => props.batches.filter((b) => !b.handed_over_at && b.handover_requested_at).length);

const fields = (b) => [
    { label: t('batch.col.plot'), value: b.plot },
    { label: t('batch.col.kg'), value: `${Number(b.weight_kg)} kg` },
    { label: t('batch.col.harvest'), value: String(b.harvest_date).slice(0, 10) },
    { label: t('batch.col.drying'), value: `${b.drying_days} ${t('batch.days')}` },
    { label: t('batch.col.grade'), value: b.grade ?? '-' },
];
</script>

<template>
    <Head :title="t('batch.listTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">{{ t('batch.listTitle') }}</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <!-- Actions and counts -->
                <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap gap-3">
                        <Link
                            :href="route('batches.create')"
                            class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-[#2D5715] px-6 text-sm font-bold text-white transition hover:bg-[#1F3F0F] active:scale-[.98]"
                        >
                            + {{ t('nav.newBatch') }}
                        </Link>
                        <Link
                            :href="route('sms.index')"
                            class="inline-flex min-h-[44px] items-center justify-center rounded-full border border-[#4B2106]/25 bg-white px-6 text-sm font-bold text-[#4B2106] transition hover:border-[#4B2106] active:scale-[.98]"
                        >
                            {{ t('sms.title') }}
                        </Link>
                    </div>

                    <div class="flex flex-wrap gap-2 text-sm font-semibold">
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#E4EFD6] px-4 py-1.5 text-[#2D5715]">
                            <span class="h-2 w-2 rounded-full bg-[#4A8A24]"></span>
                            {{ t('handover.done') }} · {{ doneCount }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#FBEBC8] px-4 py-1.5 text-[#8A5A0B]">
                            <span class="h-2 w-2 rounded-full bg-[#E3B04B]"></span>
                            {{ t('handover.pending') }} · {{ pendingCount }}
                        </span>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="batches.length === 0" class="rounded-2xl border border-dashed border-[#D9CBA9] bg-white px-6 py-16 text-center text-[#3A1B0B]/60">
                    {{ t('batch.empty') }}
                </div>

                <!-- Batch cards -->
                <div v-else class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="b in batches"
                        :key="b.id"
                        class="flex flex-col overflow-hidden rounded-2xl border border-[#EADFC8] bg-white text-[#3A1B0B] shadow-sm transition hover:shadow-md"
                    >
                        <!-- Colour strip: green when clean, amber when flagged -->
                        <div class="h-1" :class="isFlagged(b) ? 'bg-[#E3B04B]' : 'bg-[#4A8A24]'"></div>

                        <div class="flex flex-1 flex-col p-5">
                            <header class="flex items-start justify-between gap-3">
                                <p class="font-display text-xl font-bold text-[#4B2106]">{{ b.code }}</p>
                                <span
                                    class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="isFlagged(b) ? 'bg-[#FBEBC8] text-[#8A5A0B]' : 'bg-[#E4EFD6] text-[#2D5715]'"
                                >
                                    {{ statusLabel(b.status) }}
                                </span>
                            </header>

                            <div v-if="b.flags && b.flags.length" class="mt-3 flex flex-wrap gap-1.5">
                                <span
                                    v-for="f in b.flags"
                                    :key="f"
                                    class="rounded-md bg-[#FDECEA] px-2 py-0.5 text-xs font-medium text-[#A12B1F]"
                                >
                                    {{ t('flag.' + f) }}
                                </span>
                            </div>

                            <dl class="mt-4 divide-y divide-[#F1E8D3] text-sm">
                                <div v-for="(f, i) in fields(b)" :key="i" class="flex items-center justify-between gap-4 py-2">
                                    <dt class="text-[#3A1B0B]/55">{{ f.label }}</dt>
                                    <dd class="font-semibold">{{ f.value }}</dd>
                                </div>
                            </dl>

                            <!-- Handover -->
                            <div class="mt-4 rounded-xl bg-[#FDF7E7] p-3">
                                <p class="mb-2 text-[11px] font-semibold uppercase tracking-wider text-[#3A1B0B]/50">
                                    {{ t('handover.column') }}
                                </p>
                                <p v-if="b.handed_over_at" class="text-sm font-semibold text-[#2D5715]">
                                    ✓ {{ t('handover.done') }}: {{ b.collector?.name }}
                                </p>
                                <p v-else-if="b.handover_requested_at" class="text-sm font-semibold text-[#8A5A0B]">
                                    {{ t('handover.pending') }}: {{ b.collector?.name }}
                                </p>
                                <form v-else-if="collectors.length" class="flex gap-2" @submit.prevent="handOver(b)">
                                    <select v-model="pick[b.id]" class="cw-select min-w-0 flex-1">
                                        <option :value="undefined" disabled>{{ t('handover.choose') }}</option>
                                        <option v-for="c in collectors" :key="c.id" :value="c.id">{{ c.name }}</option>
                                    </select>
                                    <button
                                        class="shrink-0 rounded-lg bg-[#2D5715] px-4 text-sm font-bold text-white transition hover:bg-[#1F3F0F] disabled:cursor-not-allowed disabled:bg-[#2D5715]/35"
                                        :disabled="!pick[b.id]"
                                    >
                                        {{ t('handover.send') }}
                                    </button>
                                </form>
                            </div>

                            <Link
                                :href="route('verify.show', b.code)"
                                class="mt-auto inline-flex items-center gap-1 pt-5 text-sm font-bold text-[#B5651D] hover:text-[#4B2106]"
                            >
                                {{ t('verify.passport') }} <span aria-hidden="true">→</span>
                            </Link>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cw-select {
    padding: 0.55rem 0.75rem;
    border: 1px solid #d9cba9;
    border-radius: 0.5rem;
    background: #fff;
    font-size: 0.85rem;
    color: #3a1b0b;
}
.cw-select:focus {
    border-color: #2d5715 !important;
    box-shadow: 0 0 0 3px rgba(45, 87, 21, 0.2) !important;
    outline: none !important;
}
</style>
