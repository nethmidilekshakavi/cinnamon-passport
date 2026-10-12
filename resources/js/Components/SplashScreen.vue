<script setup>
import CinnamonMark from '@/Components/CinnamonMark.vue';

defineProps({ show: Boolean });
</script>

<template>
    <Transition name="cin-fade">
        <div v-if="show" class="cin-splash" role="status" aria-live="polite">
            <CinnamonMark :size="96" animate />
            <p class="cin-splash-title font-display">Cinnamon Passport</p>
            <div class="cin-splash-bar" aria-hidden="true"><span></span></div>
            <span class="sr-only">Loading</span>
        </div>
    </Transition>
</template>
