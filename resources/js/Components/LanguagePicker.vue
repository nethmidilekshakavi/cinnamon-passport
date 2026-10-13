<script setup>
import { onMounted, ref } from 'vue';
import CinnamonMark from '@/Components/CinnamonMark.vue';
import { useI18n } from '@/i18n';

const { setLocale, locales } = useI18n();
const root = ref(null);

onMounted(() => {
    root.value?.querySelector('button')?.focus();
});
</script>

<template>
    <div ref="root" class="cin-splash cin-picker" role="dialog" aria-modal="true" aria-labelledby="cin-pick-title">
        <CinnamonMark :size="64" />

        <div id="cin-pick-title" class="cin-picker-title">
            <p lang="si">ඔබේ භාෂාව තෝරන්න</p>
            <p lang="ta">உங்கள் மொழியைத் தேர்ந்தெடுக்கவும்</p>
            <p lang="en">Choose your language</p>
        </div>

        <div class="cin-picker-list">
            <button
                v-for="l in locales"
                :key="l.code"
                type="button"
                class="cin-btn cin-btn-light cin-picker-btn"
                :lang="l.code"
                @click="setLocale(l.code)"
            >
                {{ l.label }}
            </button>
        </div>
    </div>
</template>
