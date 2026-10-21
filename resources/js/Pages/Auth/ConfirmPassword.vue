<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ password: '' });

const submit = () =>
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <p class="mb-4 text-sm text-gray-600">
            This is a secure area. Please confirm your password before continuing.
        </p>

        <form @submit.prevent="submit">
            <InputLabel for="password" value="Password" />
            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" autofocus />
            <InputError class="mt-2" :message="form.errors.password" />

            <div class="mt-4 flex justify-end">
                <PrimaryButton :disabled="form.processing">Confirm</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>