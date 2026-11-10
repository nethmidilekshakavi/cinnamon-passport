<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-[#FDF7E7] text-[#3A1B0B]">
        <header class="bg-[#4B2106] print:hidden">
            <div class="mx-auto flex max-w-3xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <Link href="/" class="flex items-center gap-2.5 text-white">
                    <span class="grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-full bg-white">
                        <img src="/images/welcome/logo2.png" alt="" width="36" height="36" class="h-9 w-9 object-cover mix-blend-multiply" />
                    </span>
                    <span class="font-display text-lg font-bold">Cinnamon Trust</span>
                </Link>

                <div class="flex items-center gap-4">
                    <template v-if="page.props.auth?.user">
                        <Link :href="route('dashboard')" class="text-sm text-white/85 hover:text-white">
                            {{ page.props.auth.user.name }}
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-full border border-white/40 px-3 py-1 text-sm font-medium text-white hover:bg-white hover:text-[#4B2106]"
                        >
                            Log out
                        </Link>
                    </template>

                    <LanguageSwitcher />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-xl px-4 py-8 pb-16 sm:px-6">
            <slot />
        </main>
    </div>
</template>
