<script setup>
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// minimal = only the logo and the basic links (used before a brand exists)
defineProps({ minimal: { type: Boolean, default: false } });

const user = computed(() => usePage().props.auth?.user ?? null);
const open = ref(false);

const links = [
    { label: 'Dashboard', href: () => route('seller.dashboard'), active: () => route().current('seller.dashboard') },
    { label: 'Products', href: () => route('seller.products.index'), active: () => route().current('seller.products.*') },
    { label: 'Orders', href: () => route('seller.orders.index'), active: () => route().current('seller.orders.*') },
];
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#FFF9EC] text-[#14241B]">
        <header class="sticky top-0 z-30 bg-[#0B3B2C] text-white shadow">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
                <Link href="/" class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white ring-2 ring-[#E3B04B]/60">
                        <img src="/images/welcome/logo2.png" alt="" class="h-full w-full object-cover mix-blend-multiply" />
                    </span>
                    <span class="leading-tight">
                        <span class="block font-bold">Cinnamon Trust</span>
                        <span class="block text-xs text-[#E3B04B]">Seller area</span>
                    </span>
                </Link>

                <nav class="hidden items-center gap-1 text-sm font-medium md:flex">
                    <template v-if="!minimal">
                        <Link v-for="l in links" :key="l.label" :href="l.href()"
                            class="rounded-full px-4 py-2 transition hover:bg-white/10"
                            :class="l.active() ? 'bg-white/15 text-[#E3B04B]' : ''">{{ l.label }}</Link>
                    </template>
                    <Link :href="route('market.index')" class="rounded-full px-4 py-2 transition hover:bg-white/10">Marketplace</Link>
                </nav>

                <div class="flex items-center gap-3 text-sm">
                    <ThemeToggle />
                    <Link v-if="user" :href="route('profile.edit')" class="hidden items-center gap-2 sm:flex">
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#E3B04B] font-bold text-[#14241B]">{{ user.name?.charAt(0)?.toUpperCase() }}</span>
                        <span class="font-medium">{{ user.name }}</span>
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="hidden rounded-full border border-white/40 px-4 py-1.5 hover:bg-white/10 sm:block">Log out</Link>
                    <button type="button" @click="open = !open" aria-label="Menu"
                        class="grid h-9 w-9 place-items-center rounded-full bg-white/15 md:hidden">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path v-if="!open" d="M4 7h16M4 12h16M4 17h16" />
                            <path v-else d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div v-if="open" class="border-t border-white/10 px-4 pb-4 pt-2 md:hidden">
                <template v-if="!minimal">
                    <Link v-for="l in links" :key="l.label" :href="l.href()" class="block rounded-lg px-3 py-2.5 hover:bg-white/10"
                        :class="l.active() ? 'text-[#E3B04B]' : ''">{{ l.label }}</Link>
                </template>
                <Link :href="route('market.index')" class="block rounded-lg px-3 py-2.5 hover:bg-white/10">Marketplace</Link>
                <Link :href="route('profile.edit')" class="block rounded-lg px-3 py-2.5 hover:bg-white/10">Profile</Link>
                <Link :href="route('logout')" method="post" as="button" class="block w-full rounded-lg px-3 py-2.5 text-left hover:bg-white/10">Log out</Link>
            </div>
        </header>

        <header v-if="$slots.header" class="border-b border-[#F0E8D5] bg-white">
            <div class="mx-auto max-w-6xl px-4 py-5">
                <slot name="header" />
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="bg-[#0B3B2C] py-5 text-center text-xs text-white/70">
            Cinnamon Trust is a student prototype. Sellers and products shown are sample data.
        </footer>
    </div>
</template>