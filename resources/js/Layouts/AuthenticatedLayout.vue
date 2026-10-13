<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import CinnamonMark from '@/Components/CinnamonMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { useI18n } from '@/i18n';

const { t } = useI18n();
const page = usePage();
const user = computed(() => page.props.auth.user);
const menuOpen = ref(false);

const links = computed(() => {
    void page.url; // recompute on every navigation
    if (user.value.role === 'collector') {
        return [{ label: t('nav.collectorArea'), href: route('collector.home'), active: route().current('collector.home') }];
    }
    if (user.value.role === 'buyer') {
        return [{ label: t('nav.buyerArea'), href: route('buyer.home'), active: route().current('buyer.home') }];
    }
    return [
        { label: t('nav.myBatches'), href: route('batches.index'), active: route().current('batches.index') },
        { label: t('nav.newBatch'), href: route('batches.create'), active: route().current('batches.create') },
    ];
});
</script>

<template>
    <div class="min-h-screen">
        <nav class="cin-topbar">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                <div class="flex items-center gap-8">
                    <Link :href="route('dashboard')" class="flex items-center gap-2.5 text-white">
                        <CinnamonMark :size="30" />
                        <span class="font-display text-lg font-bold">Cinnamon Passport</span>
                    </Link>

                    <div class="hidden gap-6 sm:flex">
                        <Link
                            v-for="(l, i) in links"
                            :key="i"
                            :href="l.href"
                            class="cin-navlink"
                            :class="{ 'cin-navlink-active': l.active }"
                        >
                            {{ l.label }}
                        </Link>
                    </div>
                </div>

                <div class="hidden items-center gap-5 sm:flex">
                    <LanguageSwitcher />

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button" class="cin-navlink">{{ user.name }}</button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">{{ t('nav.profile') }}</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">{{ t('nav.logout') }}</DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                <button type="button" class="cin-navlink sm:hidden" @click="menuOpen = !menuOpen">
                    {{ menuOpen ? t('nav.close') : t('nav.menu') }}
                </button>
            </div>

            <div v-show="menuOpen" class="flex flex-col items-start gap-3 border-t border-white/15 px-4 pb-4 pt-3 sm:hidden">
                <LanguageSwitcher />
                <Link
                    v-for="(l, i) in links"
                    :key="i"
                    :href="l.href"
                    class="cin-navlink"
                    :class="{ 'cin-navlink-active': l.active }"
                >
                    {{ l.label }}
                </Link>
                <Link :href="route('profile.edit')" class="cin-navlink">{{ t('nav.profile') }}</Link>
                <Link :href="route('logout')" method="post" as="button" class="cin-navlink">{{ t('nav.logout') }}</Link>
            </div>
        </nav>

        <header v-if="$slots.header" class="cin-pagehead">
            <div class="mx-auto max-w-6xl px-4 py-5 sm:px-6">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
