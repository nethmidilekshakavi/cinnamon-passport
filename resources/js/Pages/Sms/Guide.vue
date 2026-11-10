<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { useI18n } from '@/i18n';

defineProps({ role: String });

const { t } = useI18n();

const MONTHS = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
const COMMANDS = ['BATCH', 'THOGAYA', 'තොගය', 'தொகுதி'];
const GRADES = ['ALBA', 'C5', 'C4', 'H1'];

const pad = (n) => String(n).padStart(2, '0');
const now = new Date();
const todayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

const form = reactive({ command: 'BATCH', plot: '', kg: '', date: '', drying: '', grade: '' });
const copied = ref(false);

// Same limits as the SMS parser and the app form.
const problems = computed(() => {
    const list = [];
    const kg = Number(form.kg);
    const days = Number(form.drying);

    if (form.plot !== '' && !/^[A-Za-z0-9-]{1,20}$/.test(form.plot.trim())) list.push('guide.err.plot');
    if (form.kg !== '' && !(kg >= 0.1 && kg <= 500)) list.push('guide.err.kg');
    if (form.date !== '' && form.date > todayStr) list.push('guide.err.date');
    if (form.drying !== '' && !(Number.isInteger(days) && days >= 1 && days <= 30)) list.push('guide.err.drying');

    return list;
});

const complete = computed(
    () =>
        form.plot.trim() !== '' &&
        form.kg !== '' &&
        form.date !== '' &&
        form.drying !== '' &&
        problems.value.length === 0
);

const message = computed(() => {
    if (!complete.value) return '';

    const [, month, day] = form.date.split('-');
    const parts = [
        form.command,
        form.plot.trim().toUpperCase(),
        `${Number(form.kg)}KG`,
        `${Number(day)}${MONTHS[Number(month) - 1]}`,
        `DRY${Number(form.drying)}`,
    ];
    if (form.grade) parts.push(form.grade);

    return parts.join(' ');
});

const otherYear = computed(() => form.date !== '' && Number(form.date.slice(0, 4)) !== now.getFullYear());

// "sms:?&body=" opens the phone's Messages app with the text filled in (Android and iPhone).
const smsLink = computed(() => `sms:?&body=${encodeURIComponent(message.value)}`);

const copy = async () => {
    try {
        await navigator.clipboard.writeText(message.value);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        // Clipboard blocked: the message above can still be selected by hand.
    }
};

const parts = [
    { code: 'BATCH', key: 'guide.m.command' },
    { code: 'P1', key: 'guide.m.plot' },
    { code: '40KG', key: 'guide.m.weight' },
    { code: '12OCT', key: 'guide.m.date' },
    { code: 'DRY3', key: 'guide.m.drying' },
    { code: 'ALBA', key: 'guide.m.grade' },
];

const mistakes = [
    { bad: 'BATCH P1 40K 12OCT DRY3', good: 'BATCH P1 40KG 12OCT DRY3', key: 'guide.w.unit' },
    { bad: 'BATC P1 40KG 12OCT DRY3', good: 'BATCH P1 40KG 12OCT DRY3', key: 'guide.w.command' },
    { bad: 'BATCH P1 40KG DRY3', good: 'BATCH P1 40KG 12OCT DRY3', key: 'guide.w.date' },
    { bad: 'BATCH P1 40KG 12OCT', good: 'BATCH P1 40KG 12OCT DRY3', key: 'guide.w.drying' },
];

const printCard = () => {
    document.body.classList.add('printing-card');
    window.addEventListener('afterprint', () => document.body.classList.remove('printing-card'), { once: true });
    window.print();
};

const input =
    'mt-1.5 block w-full rounded-lg border border-[#D9CBA9] bg-white px-3 py-2.5 text-[#3A1B0B] shadow-none';
const panel = 'overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm';
</script>

<template>
    <Head :title="t('guide.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">{{ t('guide.title') }}</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-2xl space-y-6 px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-[#3A1B0B]/65">{{ t('guide.intro') }}</p>

                <!-- Message builder -->
                <section :class="panel">
                    <div class="h-1 bg-[#4A8A24]"></div>
                    <div class="space-y-4 p-5">
                        <h3 class="font-display text-lg font-bold text-[#4B2106]">{{ t('guide.b.title') }}</h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-semibold text-[#4B2106]">
                                {{ t('guide.b.command') }}
                                <select v-model="form.command" :class="input">
                                    <option v-for="c in COMMANDS" :key="c" :value="c">{{ c }}</option>
                                </select>
                            </label>
                            <label class="block text-sm font-semibold text-[#4B2106]">
                                {{ t('guide.b.plot') }}
                                <input v-model="form.plot" type="text" maxlength="20" :placeholder="t('guide.b.plotHint')" :class="input" />
                            </label>
                            <label class="block text-sm font-semibold text-[#4B2106]">
                                {{ t('guide.b.kg') }}
                                <input v-model="form.kg" type="number" step="0.1" min="0.1" max="500" inputmode="decimal" :class="input" />
                            </label>
                            <label class="block text-sm font-semibold text-[#4B2106]">
                                {{ t('guide.b.date') }}
                                <input v-model="form.date" type="date" :max="todayStr" :class="input" />
                            </label>
                            <label class="block text-sm font-semibold text-[#4B2106]">
                                {{ t('guide.b.drying') }}
                                <input v-model="form.drying" type="number" step="1" min="1" max="30" inputmode="numeric" :class="input" />
                            </label>
                            <label class="block text-sm font-semibold text-[#4B2106]">
                                {{ t('guide.b.grade') }}
                                <select v-model="form.grade" :class="input">
                                    <option value="">{{ t('guide.b.noGrade') }}</option>
                                    <option v-for="g in GRADES" :key="g" :value="g">{{ g }}</option>
                                </select>
                            </label>
                        </div>

                        <ul v-if="problems.length" class="space-y-1 rounded-lg bg-[#FDECEA] px-3 py-2 text-sm text-[#A12B1F]">
                            <li v-for="p in problems" :key="p">{{ t(p) }}</li>
                        </ul>

                        <div>
                            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-[#3A1B0B]/50">{{ t('guide.b.preview') }}</p>
                            <div v-if="message" class="rounded-xl bg-[#FDF7E7] p-3">
                                <div class="flex justify-end">
                                    <div class="max-w-full select-all break-words rounded-2xl rounded-br-sm bg-[#4B2106] px-4 py-2 text-sm text-white">
                                        {{ message }}
                                    </div>
                                </div>
                                <p class="mt-1 text-right text-xs text-[#3A1B0B]/55">{{ message.length }} {{ t('guide.b.chars') }}</p>
                            </div>
                            <p v-else class="rounded-xl border border-dashed border-[#D9CBA9] px-3 py-5 text-center text-sm text-[#3A1B0B]/55">
                                {{ t('guide.b.fill') }}
                            </p>
                        </div>

                        <p v-if="message && otherYear" class="text-xs text-[#3A1B0B]/60">{{ t('guide.b.yearNote') }}</p>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="cin-btn" :disabled="!message" @click="copy">
                                {{ copied ? t('guide.b.copied') : t('guide.b.copy') }}
                            </button>
                            <a
                                :href="message ? smsLink : undefined"
                                class="cin-btn !border-[#4B2106] !bg-white !text-[#4B2106] hover:!bg-[#FDF7E7]"
                                :class="{ 'pointer-events-none opacity-50': !message }"
                            >
                                {{ t('guide.b.open') }}
                            </a>
                        </div>
                    </div>
                </section>

                <!-- What each part means -->
                <section :class="[panel, 'space-y-3 p-5']">
                    <h3 class="font-display text-lg font-bold text-[#4B2106]">{{ t('guide.m.title') }}</h3>
                    <p class="rounded-lg bg-[#FDF7E7] px-3 py-2 font-mono text-sm">BATCH P1 40KG 12OCT DRY3 ALBA</p>
                    <ul class="divide-y divide-[#F1E8D3] text-sm">
                        <li v-for="p in parts" :key="p.code" class="flex gap-3 py-2">
                            <span class="w-16 shrink-0 font-mono font-semibold text-[#2D5715]">{{ p.code }}</span>
                            <span class="text-[#3A1B0B]/80">{{ t(p.key) }}</span>
                        </li>
                    </ul>
                </section>

                <!-- Common mistakes -->
                <section :class="[panel, 'space-y-3 p-5']">
                    <h3 class="font-display text-lg font-bold text-[#4B2106]">{{ t('guide.w.title') }}</h3>
                    <ul class="divide-y divide-[#F1E8D3] text-sm">
                        <li v-for="m in mistakes" :key="m.key" class="space-y-0.5 py-3 first:pt-0 last:pb-0">
                            <p class="break-words font-mono text-[#A12B1F]">✗ {{ m.bad }}</p>
                            <p class="break-words font-mono text-[#2D5715]">✓ {{ m.good }}</p>
                            <p class="pt-1 text-[#3A1B0B]/65">{{ t(m.key) }}</p>
                        </li>
                    </ul>
                </section>

                <!-- Other messages -->
                <section :class="[panel, 'space-y-2 p-5 text-sm']">
                    <h3 class="font-display text-lg font-bold text-[#4B2106]">{{ t('guide.o.title') }}</h3>
                    <p>{{ t('guide.o.help') }}</p>
                    <p>{{ t('guide.o.bare') }}</p>
                    <p>{{ t('guide.o.hand') }}</p>
                    <p>{{ t('guide.o.ok') }}</p>
                    <p class="text-[#3A1B0B]/60">{{ t('guide.o.lang') }}</p>
                </section>

                <!-- Pocket card (printable) -->
                <section class="space-y-3">
                    <h3 class="font-display text-lg font-bold text-[#4B2106]">{{ t('guide.p.title') }}</h3>
                    <p class="text-sm text-[#3A1B0B]/65">{{ t('guide.p.hint') }}</p>

                    <div id="sms-card" class="mx-auto w-full max-w-sm space-y-2 rounded-2xl border-2 border-[#4B2106] bg-white p-4 text-sm">
                        <p class="font-display text-base font-bold text-[#4B2106]">{{ t('guide.title') }}</p>
                        <p class="rounded-lg bg-[#FDF7E7] px-3 py-2 font-mono">BATCH P1 40KG 12OCT DRY3 ALBA</p>
                        <ul class="space-y-1">
                            <li v-for="p in parts" :key="p.code" class="flex gap-2">
                                <span class="w-14 shrink-0 font-mono font-semibold text-[#2D5715]">{{ p.code }}</span>
                                <span>{{ t(p.key) }}</span>
                            </li>
                        </ul>
                        <p class="pt-1 font-semibold">{{ t('guide.p.footer') }}</p>
                    </div>

                    <button type="button" class="cin-btn" @click="printCard">{{ t('guide.p.print') }}</button>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body.printing-card * {
        visibility: hidden;
    }
    body.printing-card #sms-card,
    body.printing-card #sms-card * {
        visibility: visible;
    }
    body.printing-card #sms-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 105mm;
    }
}
</style>
