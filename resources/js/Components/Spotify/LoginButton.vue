<!-- resources/js/Components/LoginButton.vue -->
<template>
    <button
        @click="redirectToSpotify"
        class="inline-flex items-center px-6 py-3 text-white bg-green-500 rounded-full shadow-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 transition ease-in-out duration-200"
    >
        <img
            src="https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg"
            alt="Spotify Logo"
            class="w-6 h-6 mr-3"
        />
        <span class="font-semibold">Login with Spotify</span>
    </button>
</template>

<script setup lang="ts">
function redirectToSpotify() {
    window.location.href = '/auth/spotify';
}
</script>
