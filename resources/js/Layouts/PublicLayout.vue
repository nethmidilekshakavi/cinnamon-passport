<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import CinnamonMark from '@/Components/CinnamonMark.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-[#FAF6F2] text-[#261611]">
        <header class="flex items-center justify-between px-4 py-4 sm:px-8 print:hidden">
            <Link href="/" class="flex items-center gap-2 font-display text-lg font-bold">
                <CinnamonMark class="h-9 w-9" />
                <span>Cinnamon Passport</span>
            </Link>

            <div class="flex items-center gap-4">
                <template v-if="page.props.auth?.user">
                    <Link :href="route('dashboard')" class="text-sm underline">
                        {{ page.props.auth.user.name }}
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="rounded-md border border-[#5A2D17] px-3 py-1 text-sm font-medium text-[#5A2D17] hover:bg-[#5A2D17] hover:text-white"
                    >
                        Log out
                    </Link>
                </template>

                <div class="rounded-full bg-[#2E170C] px-2 py-1">
                    <LanguageSwitcher />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-xl px-4 pb-16 sm:px-6">
            <slot />
        </main>
    </div>
</template>
