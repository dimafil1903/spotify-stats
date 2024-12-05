// stores/playerStore.js
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const usePlayerStore = defineStore('player', () => {
    const currentTrack = ref(null);
    const playlist = ref([]);
    const isPlaying = ref(false);
    const volume = ref(50);

    // Play a specific track and optionally update the playlist
    function playTrack(track, newPlaylist = []) {
        if (newPlaylist.length) {
            playlist.value = [...newPlaylist];
        }
        currentTrack.value = track;
        isPlaying.value = true;
    }

    // Toggle play/pause
    function togglePlayPause() {
        isPlaying.value = !isPlaying.value;
    }

    // Play next track in playlist
    function playNext() {
        if (!playlist.value.length) return;
        console.log(playlist.value.length);
        const currentIndex = playlist.value.findIndex(track => track.id === currentTrack.value?.id);
        const nextIndex = (currentIndex + 1) % playlist.value.length;
        currentTrack.value = playlist.value[nextIndex];
        isPlaying.value = true;
    }

    // Play previous track in playlist
    function playPrevious() {
        if (!playlist.value.length) return;

        const currentIndex = playlist.value.findIndex(track => track.id === currentTrack.value?.id);
        const prevIndex = (currentIndex - 1 + playlist.value.length) % playlist.value.length;
        currentTrack.value = playlist.value[prevIndex];
        isPlaying.value = true;
    }

    // Update volume
    function setVolume(newVolume) {
        volume.value = newVolume;
    }

    return {
        currentTrack,
        playlist,
        isPlaying,
        volume,
        playTrack,
        togglePlayPause,
        playNext,
        playPrevious,
        setVolume
    };
});
