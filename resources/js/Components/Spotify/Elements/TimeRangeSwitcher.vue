<script setup lang="ts">
import { useTimeRangeStore } from '@/stores/timeRangeStore';

const timeRangeStore = useTimeRangeStore();
const ranges = [
    { label: '1 Y', value: 'long_term', icon: 'fas fa-calendar-alt' },
    { label: '6 M', value: 'medium_term', icon: 'fas fa-calendar' },
    { label: '1 M', value: 'short_term', icon: 'fas fa-calendar-day' },
];

function selectRange(value: string) {
    timeRangeStore.setTimeRange(value);
}
</script>

<template>
    <div class="relative text-sm">
        <div class="flex items-center bg-neutral-900/50 backdrop-blur-sm p-1 rounded-full shadow-xl">
            <div
                v-for="range in ranges"
                :key="range.value"
                class="relative group cursor-pointer px-4 py-2 transition-all duration-200 ease-out"
                @click="selectRange(range.value)"
            >
                <div
                    v-if="timeRangeStore.currentTimeRange === range.value"
                    class="absolute inset-0 bg-green-500 rounded-full shadow-lg shadow-green-500/20 -z-10 animate-pulse"
                ></div>

                <div class="flex items-center gap-2">
                    <i :class="[
                        range.icon,
                        'text-sm transition-all duration-200',
                        timeRangeStore.currentTimeRange === range.value ? 'text-white' : 'text-gray-400 group-hover:text-white'
                    ]"></i>
                    <span :class="[
                        'text-sm font-medium transition-all duration-200',
                        timeRangeStore.currentTimeRange === range.value ? 'text-white' : 'text-gray-400 group-hover:text-white'
                    ]">{{ range.label }}</span>
                </div>

                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                    <div class="text-xs text-gray-400 bg-neutral-900/90 px-2 py-1 rounded">
                        {{ range.value === 'short_term' ? 'Last 4 weeks' :
                        range.value === 'medium_term' ? 'Last 6 months' :
                            'All time' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
