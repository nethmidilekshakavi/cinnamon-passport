<script setup>
import { useI18n } from '@/i18n';

defineProps({ show: Boolean });

const { t } = useI18n();
const logo = '/images/welcome/logo2.png';
</script>

<template>
    <Transition name="cin-fade">
        <div v-if="show" class="cin-loading" role="status" aria-live="polite">
            <span class="cin-loading-badge">
                <img :src="logo" alt="" width="152" height="152" />
            </span>
            <p class="cin-loading-title font-display">Cinnamon Trust</p>
            <p class="cin-loading-tag">{{ t('welcome.tagline') }}</p>
            <div class="cin-loading-bar"><span></span></div>
            <span class="sr-only">Loading</span>
        </div>
    </Transition>
</template>
