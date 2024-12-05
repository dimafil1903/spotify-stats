<template>
    <div v-if="currentTrack" class="fixed bottom-0 left-0 right-0 bg-stone-950 no-select rounded-t-lg">
        <div class="max-w-screen-xl mx-auto px-3 py-2">
            <div class="grid grid-cols-3 items-center gap-2 sm:gap-3">
                <!-- Track Info -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <img
                        :src="currentTrack.album.images[0].url"
                        :alt="currentTrack.name"
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg object-cover"
                    />
                    <div class="min-w-0">
                        <p class="text-white font-medium text-xs sm:text-sm truncate">{{ currentTrack.name }}</p>
                        <p class="text-gray-400 text-xs truncate">
                            {{ currentTrack.artists.map(artist => artist.name).join(', ') }}
                        </p>
                    </div>
                </div>

                <!-- Player Controls - Centered -->
                <div class="flex flex-col items-center gap-1">
                    <div class="flex items-center gap-x-4 sm:gap-x-6">
                        <button
                            @click="playPrevious"
                            class="text-gray-400 hover:text-white transition text-sm sm:text-base"
                        >
                            <i class="fas fa-backward"></i>
                        </button>

                        <button
                            @click="togglePlayPause"
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white text-black flex items-center justify-center hover:scale-105 transition"
                        >
                            <i :class="['text-sm sm:text-lg', isPlaying ? 'fas fa-pause' : 'fas fa-play']"></i>
                        </button>

                        <button
                            @click="playNext"
                            class="text-gray-400 hover:text-white transition text-sm sm:text-base"
                        >
                            <i class="fas fa-forward"></i>
                        </button>
                    </div>

                    <!-- Progress Bar with Time -->
                    <div class="w-full flex items-center gap-2">
                        <span class="text-gray-400 text-xs">{{ formatTime(currentTime) }}</span>
                        <div class="flex-grow bg-gray-700 h-1 rounded-full cursor-pointer" @click="seek">
                            <div
                                :style="{ width: `${(currentTime / duration) * 100}%` }"
                                class="bg-white h-full rounded-full relative group"
                            >
                                <div class="hidden group-hover:block absolute right-0 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-white rounded-full shadow-lg"></div>
                            </div>
                        </div>
                        <span class="text-gray-400 text-xs">{{ formatTime(duration) }}</span>
                    </div>
                </div>

                <!-- Volume Control - Right aligned -->
                <div class="flex justify-end items-center gap-1 sm:gap-2">
                    <i class="fas fa-volume-up text-gray-400 text-sm sm:text-base"></i>
                    <input
                        type="range"
                        v-model="volume"
                        @input="updateVolume"
                        min="0"
                        max="100"
                        class="w-16 sm:w-20 accent-white"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { usePlayerStore } from '@/stores/playerStore.ts';

const playerStore = usePlayerStore();
const { currentTrack, isPlaying, volume } = storeToRefs(playerStore);
const { togglePlayPause, playNext, playPrevious } = playerStore;

const audio = ref(null);
const currentTime = ref(0);
const duration = ref(0);

// Format time in MM:SS
function formatTime(seconds) {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Seek functionality
function seek(event) {
    if (!audio.value) return;
    const rect = event.currentTarget.getBoundingClientRect();
    const percent = (event.clientX - rect.left) / rect.width;
    const newTime = percent * duration.value;
    currentTime.value = newTime;
    audio.value.currentTime = newTime;
}

// Initialize audio on component mount
onMounted(() => {
    audio.value = new Audio();

    // Set up audio event listeners
    audio.value.addEventListener('timeupdate', updateProgress);
    audio.value.addEventListener('ended', handleTrackEnd);
    audio.value.addEventListener('loadedmetadata', () => {
        duration.value = audio.value.duration;
    });

    window.addEventListener('keydown', handleKeyDown);
});

// Clean up on component unmount
onUnmounted(() => {
    if (audio.value) {
        audio.value.removeEventListener('timeupdate', updateProgress);
        audio.value.removeEventListener('ended', handleTrackEnd);
        audio.value.pause();
    }

    window.removeEventListener('keydown', handleKeyDown);
});

// Watch for changes in current track
watch(currentTrack, (newTrack) => {
    if (!newTrack || !audio.value) return;

    audio.value.src = newTrack.preview_url;
    audio.value.volume = volume.value / 100;

    if (isPlaying.value) {
        audio.value.play();
    }
});

// Watch for play/pause changes
watch(isPlaying, (newIsPlaying) => {
    if (!audio.value) return;

    if (newIsPlaying) {
        audio.value.play();
    } else {
        audio.value.pause();
    }
});

// Handle volume changes
function updateVolume(event) {
    if (!audio.value) return;
    audio.value.volume = event.target.value / 100;
}

// Update progress bar
function updateProgress() {
    if (!audio.value) return;
    currentTime.value = audio.value.currentTime;
}

// Handle track end
function handleTrackEnd() {
    playNext();
}

// Handle keydown events
function handleKeyDown(event) {
    if (event.code === 'Space') {
        event.preventDefault(); // Prevent scrolling when pressing space
        togglePlayPause();
    } else if (event.code === 'ArrowRight') {
        playNext();
    } else if (event.code === 'ArrowLeft') {
        playPrevious();
    }
}
</script>

<style scoped>
/* Adjusted styles for reduced height */
input[type="range"] {
    @apply h-1 rounded-lg appearance-none cursor-pointer;
    background: rgba(255, 255, 255, 0.1);
}

input[type="range"]::-webkit-slider-thumb {
    @apply appearance-none w-2 h-2 bg-white rounded-full;
}
</style>
