<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({ items: Array, participant: String, channel: String });
const { t } = useI18n();

const form = useForm({
    participant: props.participant ?? '',
    channel: props.channel ?? 'sms',
    answers: Array(10).fill(null),
});

const submit = () => form.post(route('study.sus.store'));
const incomplete = () => !form.participant || form.answers.some((a) => a === null);

// Translated item text; falls back to the server's English text if a key is missing.
const itemText = (i) => {
    const key = `sus.q${i + 1}`;
    const translated = t(key);
    return translated === key ? props.items[i] : translated;
};

const answered = () => form.answers.filter((a) => a !== null).length;
</script>

<template>
    <Head :title="t('sus.title')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">{{ t('sus.title') }}</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
                    <div class="h-1 bg-[#4A8A24]"></div>

                    <div class="space-y-6 p-5 sm:p-6">
                        <div class="flex flex-wrap items-end gap-5">
                            <div>
                                <label class="block text-sm font-semibold text-[#4B2106]">{{ t('sus.participant') }}</label>
                                <input
                                    v-model="form.participant"
                                    type="text"
                                    maxlength="20"
                                    class="mt-1.5 rounded-lg border border-[#D9CBA9] bg-white px-3 py-2.5 uppercase text-[#3A1B0B] shadow-none"
                                />
                            </div>
                            <div class="flex flex-wrap gap-2 text-sm">
                                <label
                                    class="flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 transition"
                                    :class="form.channel === 'sms' ? 'border-[#2D5715] bg-[#E4EFD6] text-[#2D5715]' : 'border-[#D9CBA9]'"
                                >
                                    <input v-model="form.channel" type="radio" value="sms" class="accent-[#2D5715]" />
                                    {{ t('sus.channelSms') }}
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2 transition"
                                    :class="form.channel === 'app' ? 'border-[#2D5715] bg-[#E4EFD6] text-[#2D5715]' : 'border-[#D9CBA9]'"
                                >
                                    <input v-model="form.channel" type="radio" value="app" class="accent-[#2D5715]" />
                                    {{ t('sus.channelApp') }}
                                </label>
                            </div>
                        </div>

                        <div class="rounded-lg bg-[#FDF7E7] px-3 py-2 text-xs text-[#3A1B0B]/70">{{ t('sus.scale') }}</div>

                        <div v-for="(item, i) in items" :key="i" class="border-t border-[#F1E8D3] pt-5">
                            <p class="mb-3 text-sm text-[#3A1B0B]">
                                <span class="mr-1 font-bold text-[#B5651D]">{{ i + 1 }}.</span>
                                {{ itemText(i) }}
                            </p>
                            <div class="flex gap-2">
                                <label
                                    v-for="n in 5"
                                    :key="n"
                                    class="grid h-11 w-11 cursor-pointer place-items-center rounded-full border text-sm font-semibold transition"
                                    :class="
                                        form.answers[i] === n
                                            ? 'border-[#2D5715] bg-[#2D5715] text-white'
                                            : 'border-[#D9CBA9] bg-white text-[#3A1B0B] hover:border-[#2D5715]'
                                    "
                                >
                                    <input v-model="form.answers[i]" type="radio" :name="`q${i}`" :value="n" class="sr-only" />
                                    {{ n }}
                                </label>
                            </div>
                        </div>

                        <InputError :message="form.errors.participant || form.errors.answers" />
                    </div>

                    <div class="flex items-center justify-between gap-3 border-t border-[#EADFC8] bg-[#FDF7E7] px-5 py-4 sm:px-6">
                        <span class="text-sm font-semibold text-[#4B2106]">{{ answered() }} / 10</span>
                        <button
                            class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-[#2D5715] px-8 text-sm font-bold text-white transition hover:bg-[#1F3F0F] active:scale-[.98] disabled:opacity-50"
                            :disabled="form.processing || incomplete()"
                        >
                            {{ t('sus.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
