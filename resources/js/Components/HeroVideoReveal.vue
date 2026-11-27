<script setup>
// Three portrait videos slide in from the right one after another,
// then "Welcome to Cinnamon Trust" is revealed underneath.
//
// Put your 3 video files in:  public/videos/
// and change the names below (or pass a :videos prop).
defineProps({
    videos: {
        type: Array,
        default: () => ['videos/0f0c3dcfbb5828804ee58e2c5d57d50d.mp4', '/videos/87d5bd84b17f0d24927b7aba4b344efb_720w.mp4', '/videos/693641a095fd0633b546b1f6ba39fe6f_720w.mp4'],
    },
    posters: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="hvr mx-auto w-full max-w-md">
        <div class="grid grid-cols-3 gap-2 sm:gap-3">
            <div
                v-for="(src, i) in videos"
                :key="src"
                class="hvr-panel overflow-hidden rounded-2xl bg-[#14503A] ring-1 ring-white/15"
                :style="{ '--i': i }"
            >
                <video
                    class="block aspect-[9/16] h-full w-full object-cover"
                    :src="src"
                    :poster="posters[i]"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="auto"
                    aria-hidden="true"
                />
            </div>
        </div>

        <p class="hvr-welcome mt-6 text-center">
            <span class="block text-base text-white/75 sm:text-lg">Welcome to</span>
            <span class="font-display block text-3xl font-bold tracking-tight text-[#E3B04B] sm:text-4xl">
                Cinnamon Trust
            </span>
        </p>
    </div>
</template>

<style scoped>
/* Panels fly in from the right, one after another (0s, .3s, .6s) */
.hvr-panel {
    opacity: 0;
    transform: translateX(140%);
    animation: hvr-in 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: calc(var(--i) * 0.3s + 0.2s);
}

/* Text is wiped in from the left once the last panel has landed */
.hvr-welcome {
    opacity: 0;
    clip-path: inset(0 100% 0 0);
    animation: hvr-text 1s ease-out 1.9s forwards;
}

@keyframes hvr-in {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes hvr-text {
    to {
        opacity: 1;
        clip-path: inset(0 0 0 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .hvr-panel,
    .hvr-welcome {
        animation: none;
        opacity: 1;
        transform: none;
        clip-path: none;
    }
}
</style>
