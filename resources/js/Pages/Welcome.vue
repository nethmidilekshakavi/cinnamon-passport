<script setup>
// Full-screen intro: 3 videos slide in from the right (one after another),
// then "Welcome to Cinnamon Trust" is revealed in the middle.
// Slots:  #top = nav bar,  #bottom = buttons (fade in last)
defineProps({
    videos: {
        type: Array,
        default: () => ['videos/0f0c3dcfbb5828804ee58e2c5d57d50d.mp4', 'videos/693641a095fd0633b546b1f6ba39fe6f_720w.mp4', 'videos/87d5bd84b17f0d24927b7aba4b344efb_720w.mp4', ],
    },
    posters: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="fvi relative h-screen min-h-[560px] w-full overflow-hidden bg-black">
        <!-- 3 full-height video panels -->
        <div class="absolute inset-0 grid grid-cols-3 gap-1 p-1 sm:gap-1.5 sm:p-1.5">
            <div
                v-for="(src, i) in videos"
                :key="src"
                class="fvi-panel overflow-hidden rounded-lg bg-[#14503A] sm:rounded-xl"
                :style="{ '--i': i }"
            >
                <video
                    class="block h-full w-full object-cover"
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

        <!-- Dark tint so the text is readable -->
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-black/55 via-black/30 to-black/65"></div>

        <!-- Nav -->
        <div class="fvi-top relative z-10">
            <slot name="top" />
        </div>

        <!-- Welcome text -->
        <div class="pointer-events-none absolute inset-0 z-10 grid place-items-center px-5">
            <h1 class="fvi-welcome text-center text-white">
                <span class="block text-lg tracking-wide text-white/85 sm:text-2xl">Welcome to</span>
                <span class="font-display block text-5xl font-bold tracking-tight text-[#E3B04B] drop-shadow-lg sm:text-7xl lg:text-8xl">
                    Cinnamon Trust
                </span>
            </h1>
        </div>

        <!-- Buttons + scroll hint -->
        <div class="fvi-bottom absolute inset-x-0 bottom-0 z-10 flex flex-col items-center gap-5 px-5 pb-8">
            <slot name="bottom" />
            <svg viewBox="0 0 24 24" class="fvi-bounce h-6 w-6 text-white/70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 9l6 6 6-6" />
            </svg>
        </div>
    </section>
</template>

<style scoped>
.fvi {
    height: 100vh;
    height: 100svh;
}

/* Panels fly in from the right: 0.2s, 0.55s, 0.9s */
.fvi-panel {
    opacity: 0;
    transform: translateX(120%);
    animation: fvi-in 1s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: calc(var(--i) * 0.35s + 0.2s);
}

/* Welcome text wipes in after the last panel lands */
.fvi-welcome {
    opacity: 0;
    clip-path: inset(0 100% 0 0);
    animation: fvi-text 1.1s ease-out 2s forwards;
}

/* Buttons fade up last */
.fvi-bottom {
    opacity: 0;
    transform: translateY(16px);
    animation: fvi-up 0.8s ease-out 3s forwards;
}

.fvi-top {
    opacity: 0;
    animation: fvi-fade 0.8s ease-out 0.4s forwards;
}

.fvi-bounce {
    animation: fvi-bob 1.6s ease-in-out infinite;
}

@keyframes fvi-in {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
@keyframes fvi-text {
    to {
        opacity: 1;
        clip-path: inset(0 0 0 0);
    }
}
@keyframes fvi-up {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
@keyframes fvi-fade {
    to {
        opacity: 1;
    }
}
@keyframes fvi-bob {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(6px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .fvi-panel,
    .fvi-welcome,
    .fvi-bottom,
    .fvi-top {
        animation: none;
        opacity: 1;
        transform: none;
        clip-path: none;
    }
    .fvi-bounce {
        animation: none;
    }
}
</style>
