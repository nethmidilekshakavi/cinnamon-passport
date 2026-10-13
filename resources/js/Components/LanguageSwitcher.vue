<script setup>
import { useI18n } from '@/i18n';

const { t, locale, setLocale, locales } = useI18n();
</script>

<template>
    <div class="cin-lang" role="group" :aria-label="t('lang.label')">
        <button
            v-for="l in locales"
            :key="l.code"
            type="button"
            class="cin-lang-btn"
            :class="{ 'cin-lang-active': locale === l.code }"
            :lang="l.code"
            :aria-pressed="locale === l.code"
            @click="setLocale(l.code)"
        >
            {{ l.label }}
        </button>
    </div>
</template>
