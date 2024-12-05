// stores/timeRangeStore.ts
import { defineStore } from 'pinia';

export const useTimeRangeStore = defineStore('timeRange', {
    state: () => ({
        currentTimeRange: 'short_term'
    }),
    actions: {
        setTimeRange(range: string) {
            this.currentTimeRange = range;
        }
    },
    getters: {
        timeRangeLabel: (state) => {
            const labels = {
                'short_term': 'Last 4 weeks',
                'medium_term': 'Last 6 months',
                'long_term': 'All time'
            };
            return labels[state.currentTimeRange];
        }
    }
});
