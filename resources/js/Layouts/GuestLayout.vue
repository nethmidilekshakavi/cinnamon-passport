<script setup>
import { Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
</script>

<template>
    <div class="cin-guest flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <LanguageSwitcher class="mb-8" />

        <Link href="/" class="flex flex-col items-center gap-3 text-white">
            <span class="grid h-20 w-20 place-items-center overflow-hidden rounded-full bg-white shadow-lg ring-4 ring-[#E3B04B]/50">
                <img src="/images/welcome/logo2.png" alt="" width="80" height="80" class="h-20 w-20 object-cover mix-blend-multiply" />
            </span>
            <span class="font-display text-2xl font-bold">Cinnamon Trust</span>
        </Link>

        <div class="cin-card mt-6 w-full overflow-hidden px-6 py-6 sm:max-w-md">
            <slot />
        </div>
    </div>
</template>
