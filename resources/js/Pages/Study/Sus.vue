<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ items: Array, participant: String, channel: String });

const form = useForm({
    participant: props.participant ?? '',
    channel: props.channel ?? 'sms',
    answers: Array(10).fill(null),
});

const submit = () => form.post(route('study.sus.store'));
const incomplete = () => !form.participant || form.answers.some((a) => a === null);
</script>

<template>
    <Head title="SUS questionnaire" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">SUS questionnaire</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="cin-card space-y-6 p-6">
                    <div class="flex flex-wrap items-end gap-6">
                        <div>
                            <label class="block text-sm font-medium">Participant</label>
                            <input
                                v-model="form.participant"
                                type="text"
                                maxlength="20"
                                class="mt-1 rounded-md border-gray-300 uppercase shadow-sm"
                            />
                        </div>
                        <div class="flex gap-4 pb-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input v-model="form.channel" type="radio" value="sms" /> SMS
                            </label>
                            <label class="flex items-center gap-2">
                                <input v-model="form.channel" type="radio" value="app" /> App form
                            </label>
                        </div>
                    </div>

                    <p class="cin-muted text-xs">1 = strongly disagree … 5 = strongly agree</p>

                    <div v-for="(item, i) in items" :key="i" class="border-t pt-4">
                        <p class="mb-2 text-sm">{{ i + 1 }}. {{ item }}</p>
                        <div class="flex gap-5 text-sm">
                            <label v-for="n in 5" :key="n" class="flex items-center gap-1">
                                <input v-model="form.answers[i]" type="radio" :name="`q${i}`" :value="n" /> {{ n }}
                            </label>
                        </div>
                    </div>

                    <InputError :message="form.errors.participant || form.errors.answers" />

                    <PrimaryButton :disabled="form.processing || incomplete()">Save SUS</PrimaryButton>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
