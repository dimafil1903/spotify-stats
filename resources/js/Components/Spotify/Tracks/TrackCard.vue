<template>
    <div
        class="bg-gray-950 p-4 rounded-lg shadow flex flex-col items-center text-center hover:bg-gray-800 transition cursor-pointer"
        @click="playTrack"
    >
        <div class="relative mx-5">
            <img
                :src="track.album.images[0].url"
                :alt="track.name"
                class="w-24 h-24 rounded-lg object-cover"
            />
            <!-- Playing Animation -->
            <div v-if="isCurrentTrack && isPlaying"
                 class="absolute inset-0 bg-black/40 flex items-center justify-center rounded-lg">
                <div class="flex items-end gap-1 h-5">
                    <div class="w-1 bg-white animate-music-bar" style="--bar-height: 60%;"></div>
                    <div class="w-1 bg-white animate-music-bar" style="--bar-height: 30%;"></div>
                    <div class="w-1 bg-white animate-music-bar" style="--bar-height: 100%;"></div>
                    <div class="w-1 bg-white animate-music-bar" style="--bar-height: 50%;"></div>
                </div>
            </div>
        </div>
        <p class="text-md font-semibold">{{ track.name }}</p>
        <p class="text-sm text-gray-400">
            by
            <span v-for="(artist, index) in track.artists" :key="artist.id">
                <a :href="artist.spotifyUrl" target="_blank" class="text-green-500 hover:underline">{{
                        artist.name
                    }}</a>
                <span v-if="index < track.artists.length - 1">, </span>
            </span>
        </p>
        <p class="text-xs text-gray-500">Album: {{ track.album.name }}</p>
        <p class="text-xs text-gray-500">Duration: {{ track.duration.formatted }}</p>
    </div>
</template>

<script setup lang="ts">
import {defineProps, defineEmits} from 'vue';

const props = defineProps({
    track: Object,
    isCurrentTrack: Boolean,
    isPlaying: Boolean,
});

const emit = defineEmits(['playTrack']);

function playTrack() {
    emit('playTrack', props.track);
}

function formatDuration(durationMs) {
    const minutes = Math.floor(durationMs / 60000);
    const seconds = Math.floor((durationMs % 60000) / 1000);
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
}
</script>

<style scoped>
.bg-gray-950 {
    background-color: #212121;
}

@keyframes musicBar {
    0%, 100% {
        transform: scaleY(1);
    }
    50% {
        transform: scaleY(0.5);
    }
}

/* Animation for each bar */
.animate-music-bar {
    height: var(--bar-height);
    transform-origin: bottom; /* Keeps the bottom fixed */
    animation: musicBar 1s infinite;
    animation-timing-function: ease-in-out;
}

/* Different delays for each bar */
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
</style>
