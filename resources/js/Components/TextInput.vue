<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({ type: String, required: true });
const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-[#5A2D17] focus:ring-[#5A2D17]"
        v-model="model"
        ref="input"
    />
</template>
