<script setup>
defineProps({
    size: { type: Number, default: 40 },
    animate: { type: Boolean, default: false },
});

// A cinnamon quill seen end-on: a spiral of rolled bark inside a ring.
const spiralPath = (() => {
    const steps = 160;
    const turns = 2.5;
    const points = [];
    for (let i = 0; i <= steps; i++) {
        const t = (i / steps) * turns * 2 * Math.PI;
        const r = 6 + (i / steps) * 40;
        points.push(`${(60 + r * Math.cos(t)).toFixed(2)} ${(60 + r * Math.sin(t)).toFixed(2)}`);
    }
    return `M${points.join(' L')}`;
})();
</script>

<template>
    <svg :width="size" :height="size" viewBox="0 0 120 120" fill="none" aria-hidden="true">
        <circle cx="60" cy="60" r="54" stroke="currentColor" stroke-width="5" />
        <path
            :d="spiralPath"
            pathLength="1"
            stroke="currentColor"
            stroke-width="5"
            stroke-linecap="round"
            stroke-linejoin="round"
            :class="{ 'cin-draw': animate }"
        />
    </svg>
</template>
