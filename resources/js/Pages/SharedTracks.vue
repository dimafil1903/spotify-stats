<script setup lang="ts">
import {ref, onMounted} from 'vue';
import axios from 'axios';
import {Head} from '@inertiajs/vue3';
import GlobalPlayer from '@/Components/Spotify/GlobalPlayer.vue';
import SharedTracks from '@/Components/Spotify/Tracks/SharedTracks.vue';
const props = defineProps({
    token: {
        type: String,
        required: true
    }
});


const shareData = ref(null);
const loading = ref(true);
const error = ref(null);

const timeRangeLabels = {
    'short_term': 'Last 4 Weeks',
    'medium_term': 'Last 6 Months',
    'long_term': 'All Time'
};

onMounted(async () => {
    try {
        const response = await axios.get(`/api/spotify/shared/${props.token}`);
        shareData.value = response.data;
    } catch (e) {
        error.value = 'This share link has expired or is invalid';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="bg-gray-950 text-white min-h-screen p-6">
        <Head>
            <title>Shared Spotify Stats</title>
            <meta name="description"
                  :content="shareData?.user?.name ? `Check out ${shareData.user.name}'s top Spotify tracks!` : 'Shared Spotify Stats'">
        </Head>

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-center">Shared Spotify Stats</h1>
        </header>

        <div v-if="loading" class="flex justify-center items-center min-h-[200px]">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500"></div>
        </div>

        <div v-else-if="error" class="text-center p-8">
            <p class="text-red-500 text-xl">{{ error }}</p>
        </div>

        <div v-else>
            <!-- User Info -->
            <div class="mb-8 flex items-center justify-center space-x-4">
                <img
                    v-if="shareData.user.avatar"
                    :src="shareData.user.avatar"
                    :alt="shareData.user.name"
                    class="w-16 h-16 rounded-full"
                >
                <div class="text-center">
                    <h2 class="text-xl font-semibold">{{ shareData.user.name }}'s Top Tracks</h2>
                    <p class="text-gray-400">
                        {{ timeRangeLabels[shareData.time_range] }}
                    </p>
                </div>
            </div>

            <!-- Tracks List -->
            <div class="max-w-4xl mx-auto">
                <SharedTracks
                    :tracks="shareData.tracks"
                    :readonly="true"
                />
            </div>

            <!-- Expiration Notice -->
            <div class="mt-8 text-center text-sm text-gray-400">
                Share link expires {{ new Date(shareData.expires_at).toLocaleDateString() }}
            </div>
        </div>
    </div>

    <GlobalPlayer/>
</template>
