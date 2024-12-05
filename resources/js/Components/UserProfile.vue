<!-- resources/js/Components/UserProfile.vue -->
<template>
    <div class="flex items-center space-x-4">
        <img v-if="user.spotify_image" :src="user.spotify_image" alt="User Image" class="w-10 h-10 rounded-full" />
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ user.name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ user.email }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
</script>
