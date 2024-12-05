
<script setup lang="ts">
import { ref, onMounted, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { usePlayerStore } from '@/stores/playerStore';
import { useTimeRangeStore } from '@/stores/timeRangeStore';
import TimeRangeSwitcher from '@/Components/Spotify/Elements/TimeRangeSwitcher.vue';
import TrackCard from '@/Components/Spotify/Tracks/TrackCard.vue';
import TrackListItem from '@/Components/Spotify/Tracks/TrackListItem.vue';
import TrackTable from '@/Components/Spotify/Tracks/TrackTable.vue';
import ViewModeSwitcher from '@/Components/Spotify/Elements/ViewModeSwitcher.vue';

const tracks = ref([]);
const viewMode = ref('list');
const page = ref(1);
const hasMoreTracks = ref(true);
const loadMoreTrigger = ref<HTMLElement | null>(null);
const isLoading = ref(false);
let observer: IntersectionObserver | null = null;

const playerStore = usePlayerStore();
const timeRangeStore = useTimeRangeStore();
const isPlaying = computed(() => playerStore.isPlaying);

async function fetchTracks(isLoadMore = false) {
    if (isLoading.value) return;

    try {
        isLoading.value = true;
        const response = await axios.get('/api/spotify/top-tracks', {
            params: {
                time_range: timeRangeStore.currentTimeRange,
                page: page.value
            },
        });

        const newTracks = response.data.data;

        if (isLoadMore) {
            tracks.value = [...tracks.value, ...newTracks];
            playerStore.playlist = [...playerStore.playlist, ...newTracks];
        } else {
            tracks.value = newTracks;
            playerStore.playlist = newTracks;
        }

        hasMoreTracks.value = newTracks.length > 0;
    } catch (error) {
        console.error('Error fetching top tracks:', error);
        hasMoreTracks.value = false;
    } finally {
        isLoading.value = false;
    }
}

function handleTrackClick(track) {
    if (playerStore.currentTrack?.id === track.id) {
        playerStore.togglePlayPause();
        return;
    }
    playerStore.playTrack(track, playerStore.playlist);
}

function setupInfiniteScroll() {
    if (observer) {
        observer.disconnect();
    }

    const options = {
        root: null,
        rootMargin: '100px',
        threshold: 0.1
    };

    observer = new IntersectionObserver((entries) => {
        const entry = entries[0];
        if (entry.isIntersecting && hasMoreTracks.value && !isLoading.value) {
            console.log('Loading more tracks...'); // Debug log
            loadMoreTracks();
        }
    }, options);

    if (loadMoreTrigger.value) {
        observer.observe(loadMoreTrigger.value);
    }
}

function loadMoreTracks() {
    page.value++;
    fetchTracks(true);
}

// Replace watchEffect with watch
watch(() => timeRangeStore.currentTimeRange, (newTimeRange, oldTimeRange) => {
    if (newTimeRange !== oldTimeRange && !isLoading.value) {
        page.value = 1;
        hasMoreTracks.value = true; // Reset this when changing time range
        fetchTracks(false).then(() => {
            // Reinitialize infinite scroll after fetching new tracks
            setupInfiniteScroll();
        });
    }
}, { immediate: false });


onMounted(() => {
    fetchTracks(false).then(() => {
        setupInfiniteScroll();
    });
});

onBeforeUnmount(() => {
    if (observer) {
        observer.disconnect();
        observer = null;
    }
});
</script>

<template>
    <div class="bg-gray-950 rounded-lg shadow-md px-3 py-4 sm:p-4 md:p-6">
        <!-- Header Section -->
        <div class="flex flex-col gap-4 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h2 class="text-xl sm:text-2xl font-bold text-white">Top Tracks</h2>
                <TimeRangeSwitcher />
            </div>

            <ViewModeSwitcher
                v-model="viewMode"
                class="self-end"
            />
        </div>

        <!-- Loading State -->
        <div v-if="isLoading && !tracks.length"
             class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-2 border-green-500 border-t-transparent"></div>
        </div>

        <!-- Tracks Display -->
        <template v-else>
            <div v-if="viewMode === 'grid'"
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <TrackCard
                    v-for="track in tracks"
                    :key="track.id"
                    :track="track"
                    :isCurrentTrack="track.id === playerStore.currentTrack?.id"
                    :isPlaying="isPlaying"
                    @playTrack="handleTrackClick(track)"
                />
            </div>

            <div v-else-if="viewMode === 'list'"
                 class="space-y-2 sm:space-y-3">
                <TrackListItem
                    v-for="track in tracks"
                    :key="track.id"
                    :track="track"
                    :isCurrentTrack="track.id === playerStore.currentTrack?.id"
                    :isPlaying="isPlaying"
                    @playTrack="handleTrackClick(track)"
                />
            </div>

            <div v-else-if="viewMode === 'table'"
                 class="hidden sm:block">
                <TrackTable
                    :tracks="tracks"
                    :currentTrackId="playerStore.currentTrack?.id"
                    :isPlaying="isPlaying"
                    @playTrack="handleTrackClick"
                />
            </div>

            <div
                v-show="hasMoreTracks"
                ref="loadMoreTrigger"
                class="h-20 mt-4 flex justify-center items-center"
            >
                <div v-if="isLoading"
                     class="animate-spin rounded-full h-6 w-6 border-2 border-green-500 border-t-transparent">
                </div>
                <div v-else class="text-gray-400 text-sm">
                    Scroll for more
                </div>
            </div>
        </template>
    </div>
</template>
