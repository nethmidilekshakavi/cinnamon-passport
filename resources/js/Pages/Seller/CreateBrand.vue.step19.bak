<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ districts: Array });

const form = useForm({
    name: '',
    description: '',
    district: '',
    address: '',
    contact_phone: '',
    website: '',
});

const submit = () => form.post(route('seller.store'));
</script>

<template>
    <Head title="Sell with us" />

    <AuthenticatedLayout>
        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold">Open your brand</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Register your cinnamon brand to start selling to buyers around the world.
                </p>

                <form @submit.prevent="submit" class="mt-6 space-y-5 rounded-lg bg-white p-6 shadow">
                    <div>
                        <InputLabel for="name" value="Brand name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="description" value="About your brand" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>

                    <div>
                        <InputLabel for="district" value="District" />
                        <select
                            id="district"
                            v-model="form.district"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                            <option value="" disabled>Select a district</option>
                            <option v-for="d in districts" :key="d" :value="d">{{ d }}</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.district" />
                    </div>

                    <div>
                        <InputLabel for="address" value="Address (optional)" />
                        <TextInput id="address" v-model="form.address" type="text" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>

                    <div>
                        <InputLabel for="contact_phone" value="Contact phone (optional)" />
                        <TextInput id="contact_phone" v-model="form.contact_phone" type="text" class="mt-1 block w-full" />
                        <InputError class="mt-2" :message="form.errors.contact_phone" />
                    </div>

                    <div>
                        <InputLabel for="website" value="Website (optional)" />
                        <TextInput id="website" v-model="form.website" type="url" class="mt-1 block w-full" placeholder="https://" />
                        <InputError class="mt-2" :message="form.errors.website" />
                    </div>

                    <PrimaryButton :disabled="form.processing">Create brand</PrimaryButton>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>