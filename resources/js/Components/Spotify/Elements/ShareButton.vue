<script setup lang="ts">
import { ref, onUnmounted } from 'vue';
import axios from 'axios';
import { Share2, Copy, Check, Link, AlertCircle } from 'lucide-vue-next';
import { useTimeRangeStore } from '@/stores/timeRangeStore';
import { storeToRefs } from 'pinia';

interface ApiError {
    response?: {
        data?: {
            message?: string;
        };
    };
}

const timeRangeStore = useTimeRangeStore();
const { timeRangeLabel } = storeToRefs(timeRangeStore);

const modal = ref(false);
const shareUrl = ref('');
const loading = ref(false);
const copied = ref(false);
const error = ref('');

const createShare = async () => {
    if (loading.value) return;
    loading.value = true;
    error.value = '';

    try {
        const response = await axios.post('/api/spotify/share', {
            time_range: timeRangeStore.currentTimeRange
        });
        shareUrl.value = response.data.share_url;
        modal.value = true;
    } catch (e: unknown) {
        const apiError = e as ApiError;
        error.value = apiError.response?.data?.message || 'Failed to create share link';
    } finally {
        loading.value = false;
    }
};

const copyToClipboard = async () => {
    if (!shareUrl.value || loading.value) return;

    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(shareUrl.value);
            copied.value = true;
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = shareUrl.value;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.select();

            try {
                document.execCommand('copy');
                copied.value = true;
            } catch (error) {
                console.error('Copy failed', error);
                throw new Error('Copy failed');
            } finally {
                textArea.remove();
            }
        }
    } catch (error) {
        error.value = 'Failed to copy to clipboard';
    } finally {
        if (copied.value) {
            setTimeout(() => copied.value = false, 2000);
        }
    }
};

const closeModal = () => {
    modal.value = false;
    shareUrl.value = '';
    error.value = '';
    copied.value = false;
};

onUnmounted(() => {
    closeModal();
});
</script>

<template>
    <div>
        <!-- Share Button -->
        <button
            @click="createShare"
            :disabled="loading"
            class="group inline-flex items-center space-x-2 px-3 py-1.5 sm:px-4 sm:py-2 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-green-500/20 disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base"
        >
            <Share2 class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-hover:rotate-12"/>
            <span>Share Stats</span>
            <div v-if="loading" class="ml-2 animate-spin rounded-full h-3 w-3 sm:h-4 sm:w-4 border-2 border-white border-t-transparent"></div>
        </button>

        <!-- Share Modal -->
        <div v-if="modal"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             @click.self="closeModal">
            <div class="bg-neutral-900 p-4 sm:p-6 rounded-xl w-full max-w-md shadow-xl border border-neutral-800">
                <!-- Header -->
                <div class="flex justify-between items-start mb-3 sm:mb-4">
                    <h3 class="text-lg sm:text-xl font-semibold flex items-center gap-2">
                        <Link class="w-4 h-4 sm:w-5 sm:h-5 text-green-500"/>
                        Share Tracks
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-white p-1">✕</button>
                </div>

                <!-- Error State -->
                <div v-if="error" class="mb-3 p-2 sm:p-3 bg-red-500/20 border border-red-500/50 rounded-lg flex items-start gap-2">
                    <AlertCircle class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 mt-0.5"/>
                    <p class="text-red-500 text-xs sm:text-sm flex-1">{{ error }}</p>
                </div>

                <!-- Content -->
                <div v-else class="space-y-3 sm:space-y-4">
                    <p class="text-xs sm:text-sm text-gray-400">
                        Share your {{ timeRangeLabel }} top tracks with friends
                    </p>

                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            :value="shareUrl"
                            readonly
                            class="flex-1 px-2 py-1.5 sm:px-3 sm:py-2 bg-gray-800 rounded-lg text-xs sm:text-sm border border-gray-700 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                        />
                        <button
                            @click="copyToClipboard"
                            :disabled="!shareUrl || loading"
                            class="p-1.5 sm:p-2 hover:bg-gray-800 rounded-lg transition-all duration-200 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{ 'text-green-500': copied }"
                        >
                            <Copy v-if="!copied" class="w-4 h-4 sm:w-5 sm:h-5"/>
                            <Check v-else class="w-4 h-4 sm:w-5 sm:h-5"/>
                        </button>
                    </div>

                    <p class="text-[10px] sm:text-xs text-gray-500">
                        Link expires in 7 days. You can create up to 50 share links.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
