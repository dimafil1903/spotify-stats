<template>
    <table class="min-w-full table-auto bg-gray-950 text-white rounded-lg shadow-md">
        <thead>
        <tr class="border-b border-gray-800">
            <th class="px-4 py-2 text-center"><i class="fas fa-music text-green-500 "></i><span class="mx-1"></span> Track</th>
            <th class="px-4 py-2 text-center"><i class="fas fa-users text-green-500 "></i><span class="mx-1"></span> Artist(s)</th>
            <th class="px-4 py-2 text-center"><i class="fa-solid fa-compact-disc text-green-500 "></i><span class="mx-1"></span> Album</th>
            <th class="px-4 py-2 text-center"><i class="fas fa-clock text-green-500 "></i><span class="mx-1"></span> Duration</th>
            <th class="px-4 py-2 text-center"><i class="fas fa-star text-green-500 "></i><span class="mx-1"></span> Popularity</th>
            <th class="px-4 py-2 text-center"><i class="fa-brands fa-spotify text-green-500"></i><span class="mx-1"></span> Spotify</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="track in tracks" :key="track.id"
            class="border-b border-gray-600 hover:bg-zinc-800 transition cursor-pointer" @click="playTrack(track)">
            <td class="px-4 py-2 flex items-center">
                <div class="relative mx-5">

                    <img
                        :src="track.album.images[0].url"
                        :alt="track.name"
                        class="w-12 h-12 rounded object-cover"
                    />
                    <!-- Playing Animation -->
                    <div v-if="isPlaying && currentTrackId === track.id"
                         class="absolute inset-0 bg-black/40 flex items-center justify-center rounded">
                        <div class="flex items-end gap-1 h-6">
                            <div class="w-1 bg-white animate-music-bar" style="--bar-height: 60%;"></div>
                            <div class="w-1 bg-white animate-music-bar" style="--bar-height: 30%;"></div>
                            <div class="w-1 bg-white animate-music-bar" style="--bar-height: 100%;"></div>
                            <div class="w-1 bg-white animate-music-bar" style="--bar-height: 50%;"></div>
                        </div>
                    </div>
                </div>
                <span>{{ track.name }}</span>
            </td>
            <td class="px-4 py-2">
                    <span v-for="(artist, index) in track.artists" :key="artist.id">
                        <a :href="artist.spotifyUrl" target="_blank"
                           class="text-green-500 hover:underline">{{ artist.name }}</a>
                        <span v-if="index < track.artists.length - 1">, </span>
                    </span>
            </td>
            <td class="px-4 py-2">
                <a :href="track.albumSpotifyUrl" target="_blank"
                   class="text-green-500 hover:underline">{{ track.album.name }}</a>
            </td>
            <td class="px-4 py-2 text-center">{{ track.duration.formatted }}</td>
            <td class="px-4 py-2 text-center">{{ track.popularity }}</td>
            <td class="px-4 py-2 text-center">
                <a :href="track.spotifyUrl" target="_blank" class="text-green-500 hover:underline">Listen</a>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup lang="ts">
import {defineProps, defineEmits} from 'vue';

const props = defineProps({
    tracks: Array,
    currentTrackId: String,
    isPlaying: Boolean,
});

const emit = defineEmits(['playTrack']);

function playTrack(track) {
    emit('playTrack', track);
}

function formatDuration(durationMs) {
    const minutes = Math.floor(durationMs / 60000);
    const seconds = Math.floor((durationMs % 60000) / 1000);
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
}
</script>

<style scoped>
.bg-gray-950 {
    background-color: #1a1a1a;
}

/* Keyframes for the animation */
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
