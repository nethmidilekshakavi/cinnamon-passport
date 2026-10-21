<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ status: String });

const form = useForm({});
const submit = () => form.post(route('verification.send'));
const sent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <p class="mb-4 text-sm text-gray-600">
            Please verify your email address by clicking the link we just emailed you.
        </p>

        <div v-if="sent" class="mb-4 text-sm font-medium text-green-600">
            A new verification link has been sent to your email address.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :disabled="form.processing">Resend verification email</PrimaryButton>
                <Link :href="route('logout')" method="post" as="button" class="text-sm text-gray-600 underline">
                    Log Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>