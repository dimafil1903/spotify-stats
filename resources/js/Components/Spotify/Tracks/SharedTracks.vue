<script setup lang="ts">
import { ref } from 'vue';
import TrackCard from '@/Components/Spotify/Tracks/TrackCard.vue';
import TrackListItem from '@/Components/Spotify/Tracks/TrackListItem.vue';
import TrackTable from '@/Components/Spotify/Tracks/TrackTable.vue';
import { usePlayerStore } from '@/stores/playerStore';

const props = defineProps({
    tracks: {
        type: Array,
        required: true
    }
});

const viewMode = ref('list');
const playerStore = usePlayerStore();

const handleTrackClick = (track) => {
    if (playerStore.currentTrack?.id === track.id) {
        playerStore.togglePlayPause();
        return;
    }
    playerStore.playTrack(track, props.tracks);
};

</script>

<template>
    <div class="bg-gray-950 rounded-lg shadow-md md:p-6 p-2">
        <!-- View Mode Toggle -->
        <div class="flex justify-end space-x-1 sm:space-x-2 md:space-x-4 mb-2 sm:mb-4 md:mb-6">
            <button
                v-for="mode in ['grid', 'list', 'table']"
                :key="mode"
                @click="viewMode = mode"
                :class="[
                    'p-1 sm:p-2 md:p-3 rounded flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10',
                    viewMode === mode ? 'bg-green-500 text-white' : 'text-gray-400',
                    mode === 'table' ? 'hidden sm:block' : ''
                ]"
            >
                <i :class="`fas fa-${mode === 'grid' ? 'th' : mode}`"></i>
            </button>
        </div>

        <!-- Tracks Display -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <TrackCard
                v-for="track in tracks"
                :key="track.id"
                :track="track"
                :isCurrentTrack="track.id === playerStore.currentTrack?.id"
                :isPlaying="playerStore.isPlaying"
                @playTrack="handleTrackClick(track)"
            />
        </div>

        <div v-if="viewMode === 'list'" class="space-y-4">
            <TrackListItem
                v-for="track in tracks"
                :key="track.id"
                :track="track"
                :isCurrentTrack="track.id === playerStore.currentTrack?.id"
                :isPlaying="playerStore.isPlaying"
                @playTrack="handleTrackClick(track)"
            />
        </div>

        <div v-if="viewMode === 'table'">
            <TrackTable
                :tracks="tracks"
                :currentTrackId="playerStore.currentTrack?.id"
                :isPlaying="playerStore.isPlaying"
                @playTrack="handleTrackClick"
            />
        </div>
    </div>
</template>
