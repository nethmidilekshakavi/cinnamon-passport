<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

defineProps({ grades: Array });

const { t } = useI18n();

const form = useForm({
    plot: '',
    weight_kg: '',
    harvest_date: '',
    drying_days: '',
    grade: '',
    notes: '',
});

const submit = () => form.post(route('batches.store'));

const field =
    'mt-1.5 block w-full rounded-lg border border-[#D9CBA9] bg-white px-3 py-2.5 text-[#3A1B0B] shadow-none placeholder:text-[#3A1B0B]/35';
const label = 'block text-sm font-semibold text-[#4B2106]';
</script>

<template>
    <Head :title="t('batch.newTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold text-[#4B2106]">{{ t('batch.newTitle') }}</h2>
        </template>

        <div class="py-8 sm:py-10">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <Link :href="route('batches.index')" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-[#2D5715] hover:text-[#4B2106]">
                    <span aria-hidden="true">←</span> {{ t('batch.listTitle') }}
                </Link>

                <form @submit.prevent="submit" class="overflow-hidden rounded-2xl border border-[#EADFC8] bg-white shadow-sm">
                    <div class="h-1 bg-[#4A8A24]"></div>

                    <div class="space-y-5 p-6 sm:p-8">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="plot" :class="label">{{ t('batch.plot') }}</label>
                                <input id="plot" v-model="form.plot" type="text" :class="field" required />
                                <InputError class="mt-2" :message="form.errors.plot" />
                            </div>
                            <div>
                                <label for="weight_kg" :class="label">{{ t('batch.weight') }}</label>
                                <input id="weight_kg" v-model="form.weight_kg" type="number" step="0.01" :class="field" required />
                                <InputError class="mt-2" :message="form.errors.weight_kg" />
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="harvest_date" :class="label">{{ t('batch.harvestDate') }}</label>
                                <input id="harvest_date" v-model="form.harvest_date" type="date" :class="field" required />
                                <InputError class="mt-2" :message="form.errors.harvest_date" />
                            </div>
                            <div>
                                <label for="drying_days" :class="label">{{ t('batch.dryingDays') }}</label>
                                <input id="drying_days" v-model="form.drying_days" type="number" :class="field" required />
                                <InputError class="mt-2" :message="form.errors.drying_days" />
                            </div>
                        </div>

                        <div>
                            <label for="grade" :class="label">{{ t('batch.grade') }}</label>
                            <select id="grade" v-model="form.grade" :class="field">
                                <option value="">{{ t('batch.gradeNone') }}</option>
                                <option v-for="g in grades" :key="g" :value="g">{{ g }}</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.grade" />
                        </div>

                        <div>
                            <label for="notes" :class="label">{{ t('batch.notes') }}</label>
                            <textarea id="notes" v-model="form.notes" rows="3" :class="field"></textarea>
                            <InputError class="mt-2" :message="form.errors.notes" />
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-[#EADFC8] bg-[#FDF7E7] px-6 py-4 sm:px-8">
                        <button
                            type="submit"
                            class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-[#2D5715] px-8 text-sm font-bold text-white transition hover:bg-[#1F3F0F] active:scale-[.98] disabled:opacity-60"
                            :disabled="form.processing"
                        >
                            {{ t('batch.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
