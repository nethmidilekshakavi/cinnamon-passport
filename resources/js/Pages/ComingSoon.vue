<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import HeroVideoReveal from '@/Components/HeroVideoReveal.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

defineProps({ titleKey: String, messageKey: String });

const { t } = useI18n();

// Put the video files in public/videos/
const videos = ['/videos/cinnamon-1.mp4', '/videos/cinnamon-2.mp4', '/videos/cinnamon-3.mp4'];
</script>

<template>
    <Head :title="t(titleKey)" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-xl font-bold">{{ t(titleKey) }}</h2>
        </template>

        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
            <!-- 3 videos slide in from the right, then "Welcome to Cinnamon Trust" -->
            <div class="rounded-3xl bg-[#0B3B2C] px-5 py-8 sm:px-8 sm:py-10">
                <HeroVideoReveal :videos="videos" />
            </div>

            <div class="cin-card mt-6 p-8">
                <p class="font-display text-lg font-semibold">{{ t('soon.title') }}</p>
                <p class="cin-muted mt-2">{{ t(messageKey) }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
