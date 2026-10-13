<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
</script>

<template>
    <Head :title="t('batch.newTitle')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t('batch.newTitle') }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="cin-card space-y-4 p-6">
                    <div>
                        <InputLabel for="plot" :value="t('batch.plot')" />
                        <TextInput id="plot" v-model="form.plot" type="text" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.plot" />
                    </div>

                    <div>
                        <InputLabel for="weight_kg" :value="t('batch.weight')" />
                        <TextInput id="weight_kg" v-model="form.weight_kg" type="number" step="0.01" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.weight_kg" />
                    </div>

                    <div>
                        <InputLabel for="harvest_date" :value="t('batch.harvestDate')" />
                        <TextInput id="harvest_date" v-model="form.harvest_date" type="date" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.harvest_date" />
                    </div>

                    <div>
                        <InputLabel for="drying_days" :value="t('batch.dryingDays')" />
                        <TextInput id="drying_days" v-model="form.drying_days" type="number" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.drying_days" />
                    </div>

                    <div>
                        <InputLabel for="grade" :value="t('batch.grade')" />
                        <select id="grade" v-model="form.grade" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">{{ t('batch.gradeNone') }}</option>
                            <option v-for="g in grades" :key="g" :value="g">{{ g }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.grade" />
                    </div>

                    <div>
                        <InputLabel for="notes" :value="t('batch.notes')" />
                        <textarea id="notes" v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        <InputError class="mt-2" :message="form.errors.notes" />
                    </div>

                    <PrimaryButton :disabled="form.processing">{{ t('batch.save') }}</PrimaryButton>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
