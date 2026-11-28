<script setup>
import { onMounted, ref } from 'vue';

const dark = ref(false);

const apply = (value) => {
    document.documentElement.setAttribute('data-theme', value ? 'dark' : 'light');
};

onMounted(() => {
    let saved = null;
    try {
        saved = localStorage.getItem('ct-theme');
    } catch (e) {
        saved = null;
    }
    dark.value = saved === 'dark';
    apply(dark.value);
});

const toggle = () => {
    dark.value = !dark.value;
    apply(dark.value);
    try {
        localStorage.setItem('ct-theme', dark.value ? 'dark' : 'light');
    } catch (e) {
        // storage not available, the choice just lasts for this visit
    }
};
</script>

<template>
    <button type="button" @click="toggle" :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
        :title="dark ? 'Light mode' : 'Dark mode'"
        class="grid h-9 w-9 place-items-center rounded-full bg-white/15 text-white transition hover:bg-white/25">
        <svg v-if="dark" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
        </svg>
        <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" />
        </svg>
    </button>
</template>

<style>
/* Dark theme for the public pages */
html[data-theme="dark"] { color-scheme: dark; }
[data-theme="dark"] body, [data-theme="dark"] #app { background-color: #0E1A14 !important; }
[data-theme="dark"] .bg-\[\#FFF9EC\] { background-color: #0E1A14 !important; }
[data-theme="dark"] .bg-white:not(.rounded-full) { background-color: #15261E !important; }
[data-theme="dark"] .bg-\[\#F3EBD8\], [data-theme="dark"] .bg-\[\#E4EFD6\] { background-color: #1D3329 !important; }
[data-theme="dark"] .text-\[\#14241B\] { color: #EDE7D6 !important; }
[data-theme="dark"] .text-\[\#14241B\]\/50 { color: rgba(237, 231, 214, 0.68) !important; }
[data-theme="dark"] .text-\[\#14241B\]\/60 { color: rgba(237, 231, 214, 0.68) !important; }
[data-theme="dark"] .text-\[\#14241B\]\/70 { color: rgba(237, 231, 214, 0.68) !important; }
[data-theme="dark"] .text-\[\#14241B\]\/75 { color: rgba(237, 231, 214, 0.68) !important; }
[data-theme="dark"] .text-\[\#14241B\]\/80 { color: rgba(237, 231, 214, 0.68) !important; }
[data-theme="dark"] .text-\[\#0B3B2C\], [data-theme="dark"] .text-\[\#14503A\] { color: #7FD1A8 !important; }
[data-theme="dark"] .text-\[\#6B4E16\] { color: #E3B04B !important; }
[data-theme="dark"] .border-\[\#0B3B2C\] { border-color: #7FD1A8 !important; }
[data-theme="dark"] .rounded-full.bg-\[\#0B3B2C\] { background-color: #1F7A58 !important; color: #ffffff !important; }
[data-theme="dark"] .bg-\[\#E3B04B\].text-\[\#14241B\], [data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#14241B\], [data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#14241B\]\/50, [data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#14241B\]\/60, [data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#14241B\]\/70, [data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#14241B\]\/75, [data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#14241B\]\/80 { color: #14241B !important; }
[data-theme="dark"] .bg-\[\#E3B04B\] .text-\[\#0B3B2C\] { color: #0B3B2C !important; }
[data-theme="dark"] .bg-\[\#E3B04B\] .border-\[\#0B3B2C\] { border-color: #0B3B2C !important; }
[data-theme="dark"] .bg-\[\#E3B04B\] .rounded-full.bg-\[\#0B3B2C\] { background-color: #0B3B2C !important; color: #ffffff !important; }
[data-theme="dark"] .ring-black\/5 { --tw-ring-color: rgba(255, 255, 255, 0.1) !important; }
[data-theme="dark"] .border-\[\#F0E8D5\], [data-theme="dark"] .border-\[\#E8DFC9\], [data-theme="dark"] .ring-\[\#EFE4C8\] { border-color: rgba(255, 255, 255, 0.12) !important; --tw-ring-color: rgba(255, 255, 255, 0.12) !important; }
[data-theme="dark"] .divide-\[\#F0E8D5\] > * + * { border-color: rgba(255, 255, 255, 0.1) !important; }
[data-theme="dark"] input:not([type="checkbox"]):not([type="radio"]), [data-theme="dark"] select, [data-theme="dark"] textarea { background-color: #0F1D17 !important; color: #EDE7D6 !important; border-color: rgba(255, 255, 255, 0.18) !important; }
[data-theme="dark"] .bg-amber-50, [data-theme="dark"] .bg-amber-100 { background-color: #3A2F14 !important; }
[data-theme="dark"] .text-amber-900 { color: #F3D58A !important; }
[data-theme="dark"] .bg-green-100 { background-color: #173A2A !important; }
[data-theme="dark"] .text-green-900 { color: #9BE3BE !important; }
[data-theme="dark"] .bg-red-100 { background-color: #4A1F1F !important; }
[data-theme="dark"] .text-red-800 { color: #F2A7A7 !important; }
</style>