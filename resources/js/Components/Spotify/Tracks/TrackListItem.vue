<template>
    <div
        class="bg-gray-950 p-3 rounded-lg shadow flex items-center justify-between hover:bg-gray-800 transition cursor-pointer"
        @click="playTrack"
    >
        <div class="flex items-center space-x-3 w-full overflow-hidden">
            <!-- Album Cover -->
            <div class="relative flex-shrink-0">
                <img
                    :src="track.album.images[0].url"
                    :alt="track.name"
                    class="w-10 h-10 md:w-12 md:h-12 rounded object-cover"
                />
                <!-- Playing Animation -->
                <div v-if="isCurrentTrack && isPlaying" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="flex items-end gap-1 h-5">
                        <div class="w-1 bg-white animate-music-bar" style="--bar-height: 60%;"></div>
                        <div class="w-1 bg-white animate-music-bar" style="--bar-height: 30%;"></div>
                        <div class="w-1 bg-white animate-music-bar" style="--bar-height: 100%;"></div>
                        <div class="w-1 bg-white animate-music-bar" style="--bar-height: 50%;"></div>
                    </div>
                </div>
            </div>

            <!-- Track Info -->
            <div class="truncate min-w-0">
                <p class="font-semibold text-sm md:text-base truncate">{{ track.name }}</p>
                <p class="text-xs md:text-sm text-gray-400 truncate">
                    by
                    <span v-for="(artist, index) in track.artists" :key="artist.id">
                        <a :href="artist.spotifyUrl" target="_blank" class="text-green-500 hover:underline">{{ artist.name }}</a>
                        <span v-if="index < track.artists.length - 1">, </span>
                    </span>
                </p>
            </div>
        </div>

        <!-- Listen Link -->
        <a
            :href="track.spotifyUrl"
            target="_blank"
            class="text-green-500 text-xs md:text-sm hover:underline flex-shrink-0"
        >
            Listen
        </a>
    </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    track: Object,
    isCurrentTrack: Boolean,
    isPlaying: Boolean,
});

const emit = defineEmits(['playTrack']);

function playTrack() {
    emit('playTrack', props.track);
}
</script>

<style scoped>
/* Загальний стиль */
.bg-gray-950 {
    background-color: #1c1c1c;
}

/* Анімація музичних барів */
@keyframes musicBar {
    0%, 100% {
        transform: scaleY(1);
    }
    50% {
        transform: scaleY(0.5);
    }
}

.animate-music-bar {
    height: var(--bar-height);
    transform-origin: bottom;
    animation: musicBar 1s infinite;
    animation-timing-function: ease-in-out;
}

.animate-music-bar:nth-child(1) {
    animation-delay: 0s;
}
.animate-music-bar:nth-child(2) {
    animation-delay: 0.1s;
}
.animate-music-bar:nth-child(3) {
    animation-delay: 0.2s;
}
.animate-music-bar:nth-child(4) {
    animation-delay: 0.3s;
}

/* Уніфіковане обрізання тексту */
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Гнучке вирівнювання для обмеження ширини */
.flex-shrink-0 {
    flex-shrink: 0;
}
</style>
