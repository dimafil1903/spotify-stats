// Updated Welcome page
<script setup lang="ts">
import {defineProps} from 'vue';
import UserProfile from '@/Components/UserProfile.vue';
import TopTracks from '@/Components/Spotify/Tracks/TopTracks.vue';
import LoginButton from '@/Components/Spotify/LoginButton.vue';
import GlobalPlayer from '@/Components/Spotify/GlobalPlayer.vue';
import ShareButton from '@/Components/Spotify/Elements/ShareButton.vue';
import ListenerProfileButton from "@/Components/Spotify/Elements/ListenerProfile.vue";

const props = defineProps({
    auth: Object,
});

const user = props.auth.user;
</script>

<template>
    <div class="bg-gray-950 text-white min-h-screen p-6">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-center">Welcome to Your Spotify Stats</h1>
        </header>

        <template v-if="user">
            <div class="space-y-4 sm:space-y-8">
                <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between px-2 sm:px-0">
                    <UserProfile :user="user"/>
                    <ShareButton/>
                    <ListenerProfileButton />

                </div>

                <TopTracks/>
            </div>
        </template>

        <template v-else>
            <div class="flex justify-center items-center min-h-screen">
                <LoginButton/>
            </div>
        </template>
    </div>

    <GlobalPlayer/>
</template>

<style scoped>
/* Ensure content is spaced out and aligned well */
.bg-gray-950 {
    background-color: #1a1a1a;
}

.text-white {
    color: white;
}

.text-center {
    text-align: center;
}

.space-y-8 > * + * {
    margin-top: 2rem;
}

@media (max-width: 768px) {
    /* Responsive adjustments for mobile screens */
    .space-y-8 {
        padding: 1rem;
    }
}
</style>
