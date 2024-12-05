<!-- resources/js/Components/UserStats.vue -->
<script setup lang="ts">
import { defineProps } from 'vue';
import TopTracks from './Spotify/TopTracks/TopTracks.vue';
import TopArtists from './TopArtists.vue';
import GenreStats from './GenreStats.vue';
import ListeningHistory from './ListeningHistory.vue';

const props = defineProps({
    topTracks: Array,
    topArtists: Array,
    genreStats: Object,
    listeningHistory: Array,
});
</script>

<template>
    <div class="container mx-auto space-y-8">
        <h1 class="text-2xl font-bold text-center mb-8">Your Spotify Stats</h1>


        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <TopTracks :tracks="topTracks" />
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <TopArtists :artists="topArtists" />
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <GenreStats :genres="genreStats" />
        </section>

        <section class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <ListeningHistory :history="listeningHistory" />
        </section>
    </div>
</template>
