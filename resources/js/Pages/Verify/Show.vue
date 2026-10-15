<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import QRCode from 'qrcode';
import { useI18n } from '@/i18n';

const props = defineProps({ batch: Object });
const { t } = useI18n();

const qr = ref('');

onMounted(async () => {
    try {
        qr.value = await QRCode.toDataURL(route('verify.show', props.batch.code), {
            margin: 1,
            width: 220,
            color: { dark: '#2E170C', light: '#FFFFFF' },
        });
    } catch (e) {
        qr.value = '';
    }
});

const flagged = computed(() => (props.batch.flags ?? []).length > 0);

const statusText = computed(() => {
    const key = `status.${props.batch.status}`;
    const label = t(key);
    return label === key ? props.batch.status : label;
});

const rows = computed(() => {
    const list = [
        [t('batch.col.plot'), props.batch.plot],
        [t('batch.col.kg'), `${Number(props.batch.weight_kg)} kg`],
        [t('batch.col.harvest'), props.batch.harvest_date],
        [t('batch.col.drying'), `${props.batch.drying_days} ${t('batch.days')}`],
        [t('batch.col.grade'), props.batch.grade ?? '-'],
        [t('batch.col.status'), statusText.value],
        [t('verify.source'), t('verify.source.' + props.batch.source)],
    ];

    if (props.batch.handed_over_at) {
        list.push([t('verify.custody'), props.batch.handed_over_at]);
    }

    return list;
});

const printPage = () => window.print();
</script>

<template>
    <Head :title="`${batch.code} · ${t('verify.passportTitle')}`" />

    <PublicLayout>
        <div class="mt-6 overflow-hidden rounded-xl border-2 border-[#2E170C] bg-white shadow-[6px_6px_0_#2E170C]">
            <div class="flex items-center justify-between bg-[#2E170C] px-5 py-3 text-[#E2BC8F]">
                <span class="font-display text-sm font-bold uppercase tracking-wide">
                    {{ t('verify.passportTitle') }}
                </span>
                <span class="font-display text-lg font-bold">{{ batch.code }}</span>
            </div>

            <div class="p-5">
                <div
                    class="mb-4 rounded-lg px-4 py-3 text-sm font-medium"
                    :class="flagged ? 'bg-red-50 text-red-800' : 'bg-green-50 text-green-800'"
                >
                    {{ flagged ? t('verify.review') : t('verify.clean') }}
                    <div v-if="flagged" class="mt-2 flex flex-wrap gap-1">
                        <span
                            v-for="f in batch.flags"
                            :key="f"
                            class="rounded bg-white px-2 py-0.5 text-xs text-red-700"
                        >
                            {{ t('flag.' + f) }}
                        </span>
                    </div>
                </div>

                <dl class="divide-y divide-[#E4D6C8] text-sm">
                    <div v-for="[label, value] in rows" :key="label" class="flex justify-between gap-4 py-2">
                        <dt class="cin-muted">{{ label }}</dt>
                        <dd class="text-right font-medium">{{ value }}</dd>
                    </div>
                </dl>

                <div class="mt-5 flex flex-col items-center border-t border-dashed border-[#E4D6C8] pt-5">
                    <img v-if="qr" :src="qr" alt="QR" class="h-44 w-44" />
                    <p class="cin-muted mt-2 text-xs">{{ t('verify.scan') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between print:hidden">
            <Link :href="route('verify.index')" class="cin-muted text-sm underline">
                {{ t('verify.back') }}
            </Link>
            <button type="button" class="cin-btn" @click="printPage">
                {{ t('verify.print') }}
            </button>
        </div>

        <p class="cin-muted mt-4 text-xs">{{ t('verify.disclaimer') }}</p>
    </PublicLayout>
</template>
