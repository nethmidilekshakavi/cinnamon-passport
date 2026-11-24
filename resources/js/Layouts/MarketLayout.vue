<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const user = computed(() => usePage().props.auth?.user ?? null);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#FFF9EC] text-[#14241B]">
        <header class="bg-[#0B3B2C] text-white">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
                <Link href="/" class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white ring-2 ring-[#E3B04B]/60">
                        <img src="/images/welcome/logo2.png" alt="" class="h-full w-full object-cover mix-blend-multiply" />
                    </span>
                    <span class="leading-tight">
                        <span class="block font-bold">Cinnamon Trust</span>
                        <span class="block text-xs text-white/70">Ceylon cinnamon marketplace</span>
                    </span>
                </Link>

                <nav class="flex flex-wrap items-center gap-4 text-sm font-medium">
                    <Link :href="route('market.index')" class="hover:text-[#E3B04B]">Marketplace</Link>
                    <Link v-if="user" :href="route('orders.index')" class="hover:text-[#E3B04B]">My orders</Link>
                    <Link :href="route('seller.create')" class="hover:text-[#E3B04B]">Sell on Cinnamon Trust</Link>
                    <template v-if="user">
                        <Link :href="route('profile.edit')" class="hover:text-[#E3B04B]">{{ user.name }}</Link>
                        <Link :href="route('logout')" method="post" as="button" class="hover:text-[#E3B04B]">Log out</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="hover:text-[#E3B04B]">Log in</Link>
                        <Link :href="route('register')" class="rounded-full bg-[#E3B04B] px-4 py-1.5 font-semibold text-[#14241B]">Register</Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="bg-[#0B3B2C] py-6 text-center text-xs text-white/70">
            Cinnamon Trust is a student prototype. Sellers and products shown are sample data.
        </footer>
    </div>
</template>