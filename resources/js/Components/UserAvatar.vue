<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: Object,
    size: { type: Number, default: 36 },
});

// Photo if there is one, otherwise the first letters of the name.
const initials = computed(() =>
    (props.user?.name ?? '?')
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase(),
);
</script>

<template>
    <span
        class="inline-grid shrink-0 place-items-center overflow-hidden rounded-full bg-[#E3B04B] font-bold text-[#4B2106]"
        :style="{ width: size + 'px', height: size + 'px', fontSize: size * 0.38 + 'px' }"
    >
        <img v-if="user?.photo_url" :src="user.photo_url" alt="" class="h-full w-full object-cover" />
        <span v-else>{{ initials }}</span>
    </span>
</template>
