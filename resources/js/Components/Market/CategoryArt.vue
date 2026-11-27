<script setup>
import { computed } from 'vue';

const props = defineProps({ kind: { type: String, default: '' } });

const key = computed(() => {
    const k = (props.kind || '').toLowerCase();
    if (k.includes('powder')) return 'powder';
    if (k.includes('tea')) return 'tea';
    if (k.includes('oil')) return 'oil';
    if (k.includes('balm')) return 'balm';
    if (k.includes('perfume')) return 'perfume';
    return 'sticks';
});
</script>

<template>
    <svg viewBox="0 0 64 64" fill="currentColor" aria-hidden="true">
        <g v-if="key === 'sticks'">
            <rect x="8" y="22" width="48" height="9" rx="4.5" transform="rotate(-18 32 26)" />
            <rect x="8" y="33" width="48" height="9" rx="4.5" transform="rotate(-18 32 37)" opacity="0.85" />
            <rect x="8" y="44" width="48" height="9" rx="4.5" transform="rotate(-18 32 48)" opacity="0.7" />
        </g>
        <g v-else-if="key === 'powder'">
            <path d="M6 52 Q32 8 58 52 Z" />
            <ellipse cx="32" cy="53" rx="27" ry="4" opacity="0.6" />
        </g>
        <g v-else-if="key === 'tea'">
            <path d="M12 28h32v10a16 16 0 0 1-32 0z" />
            <path d="M44 31h4a5 5 0 0 1 0 10h-5" fill="none" stroke="currentColor" stroke-width="3" />
            <path d="M22 8c-3 4 3 6 0 10M32 8c-3 4 3 6 0 10" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.7" />
        </g>
        <g v-else-if="key === 'oil'">
            <path d="M32 6C32 6 14 28 14 40a18 18 0 0 0 36 0C50 28 32 6 32 6z" />
        </g>
        <g v-else-if="key === 'balm'">
            <rect x="10" y="28" width="44" height="26" rx="7" />
            <rect x="14" y="16" width="36" height="12" rx="5" opacity="0.8" />
        </g>
        <g v-else>
            <rect x="16" y="26" width="32" height="30" rx="7" />
            <rect x="26" y="14" width="12" height="12" rx="2" opacity="0.8" />
            <rect x="21" y="7" width="22" height="7" rx="2.5" opacity="0.7" />
        </g>
    </svg>
</template>