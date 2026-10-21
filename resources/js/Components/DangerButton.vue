<script setup>
defineProps({ type: { type: String, default: 'submit' } });
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-red-500 disabled:opacity-25"
    >
        <slot />
    </button>
</template>