<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    maxWidth: { type: String, default: '2xl' },
    closeable: { type: Boolean, default: true },
});
const emit = defineEmits(['close']);

watch(
    () => props.show,
    () => {
        document.body.style.overflow = props.show ? 'hidden' : null;
    },
);

const close = () => {
    if (props.closeable) emit('close');
};
const onEscape = (e) => {
    if (e.key === 'Escape' && props.show) close();
};

onMounted(() => document.addEventListener('keydown', onEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', onEscape);
    document.body.style.overflow = null;
});

const maxWidthClass = computed(
    () =>
        ({
            sm: 'sm:max-w-sm',
            md: 'sm:max-w-md',
            lg: 'sm:max-w-lg',
            xl: 'sm:max-w-xl',
            '2xl': 'sm:max-w-2xl',
        })[props.maxWidth],
);
</script>

<template>
    <Teleport to="body">
        <div v-show="show" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0">
            <div class="fixed inset-0 bg-gray-500/75" @click="close" />
            <div class="relative mb-6 w-full overflow-hidden rounded-lg bg-white shadow-xl sm:mx-auto" :class="maxWidthClass">
                <slot v-if="show" />
            </div>
        </div>
    </Teleport>
</template>