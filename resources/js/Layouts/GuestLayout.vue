<script setup>
import { Link } from '@inertiajs/vue3';
import CinnamonMark from '@/Components/CinnamonMark.vue';
</script>

<template>
    <div class="cin-guest flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <Link href="/" class="flex items-center gap-3 text-white">
            <CinnamonMark :size="48" />
            <span class="font-display text-2xl font-bold">Cinnamon Passport</span>
        </Link>

        <div class="cin-card mt-6 w-full overflow-hidden px-6 py-6 sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
