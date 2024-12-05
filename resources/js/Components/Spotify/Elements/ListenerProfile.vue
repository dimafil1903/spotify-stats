<script setup lang="ts">
import {ref} from 'vue';
import {useTimeRangeStore} from '@/stores/timeRangeStore';
import {
    Heart,
    Music,
    User,
    Activity,
    BarChart3,
    X
} from 'lucide-vue-next';
import {Chart as ChartJS, RadialLinearScale, PointElement, LineElement, Filler, Tooltip} from 'chart.js';
import {Radar} from 'vue-chartjs'; // Changed from RadarChart to Radar

ChartJS.register(RadialLinearScale, PointElement, LineElement, Filler, Tooltip);

interface ProfileData {
    summary: string;
    sections: Record<string, {
        title: string;
        value: string;
        description: string;
        emoji: string;
        icon: string;
        color: string;
    }>;
    features: Array<{
        name: string;
        value: number;
        color: string;
        description: string;
    }>;
    charts: {
        radar: {
            labels: string[];
            datasets: Array<{
                data: number[];
                backgroundColor: string;
                borderColor: string;
                pointBackgroundColor: string;
            }>;
        };
    };
}

const timeRangeStore = useTimeRangeStore();
const isLoading = ref(false);
const error = ref('');
const showModal = ref(false);
const profile = ref<ProfileData | null>(null);

const fetchProfile = async () => {
    if (isLoading.value) return;

    try {
        isLoading.value = true;
        error.value = '';
        const response = await axios.get('/api/spotify/listener-profile', {
            params: {time_range: timeRangeStore.currentTimeRange}
        });
        profile.value = response.data;
        showModal.value = true;
    } catch (e: any) {
        error.value = e.response?.data?.message || 'Failed to generate profile';
    } finally {
        isLoading.value = false;
    }
};

const closeModal = () => {
    showModal.value = false;
    error.value = '';
};

const getIcon = (iconName: string) => {
    const icons = {heart: Heart, music: Music, user: User, activity: Activity};
    return icons[iconName] ?? Music;
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: true,
    scales: {
        r: {
            min: 0,
            max: 1,
            ticks: {
                stepSize: 0.2,
                showLabelBackdrop: false,
                color: 'rgba(255, 255, 255, 0.6)',
                font: {
                    size: 10,
                    family: 'CircularSp, system-ui'
                }
            },
            grid: {
                color: 'rgba(255, 255, 255, 0.1)'
            },
            angleLines: {
                color: 'rgba(255, 255, 255, 0.1)'
            },
            pointLabels: {
                color: 'rgba(255, 255, 255, 0.8)',
                font: {
                    size: 11,
                    family: 'CircularSp, system-ui',
                    weight: 'bold'
                }
            }
        }
    },
    plugins: {
        tooltip: {
            backgroundColor: '#282828',
            titleFont: {
                family: 'CircularSp, system-ui'
            },
            bodyFont: {
                family: 'CircularSp, system-ui'
            },
            padding: 12,
            cornerRadius: 8,
            callbacks: {
                label: (context) => `${context.label}: ${Math.round(context.raw * 100)}%`
            }
        }
    }
};
</script>

<template>
    <div>
        <!-- Generate Profile Button -->
        <button
            @click="fetchProfile"
            :disabled="isLoading"
            class="group inline-flex items-center space-x-2 px-6 py-3 bg-[#1DB954] hover:bg-[#1ed760] rounded-full transition-all duration-200 transform hover:scale-105 shadow-lg font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <BarChart3 class="w-5 h-5 transition-transform group-hover:rotate-12"/>
            <span>Generate Your Profile</span>
            <div v-if="isLoading"
                 class="ml-2 animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"/>
        </button>

        <!-- Modal -->
        <div v-if="showModal"
             class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50"
             @click.self="closeModal">
            <div class="bg-[#121212] p-8 rounded-xl w-full max-w-4xl m-4 shadow-2xl border border-[#282828] max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-[#1DB954] flex items-center justify-center">
                            <BarChart3 class="w-7 h-7"/>
                        </div>
                        <div>
                            <h4 class="text-[11px] uppercase font-bold text-gray-400 tracking-widest">Spotify Stats</h4>
                            <h3 class="text-2xl font-bold">Your Listener Profile</h3>
                        </div>
                    </div>
                    <button @click="closeModal"
                            class="text-gray-400 hover:text-white transition-colors p-2 hover:bg-[#282828] rounded-full">
                        <X class="w-5 h-5"/>
                    </button>
                </div>

                <!-- Error State -->
                <div v-if="error"
                     class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                    <p class="text-red-400 text-sm">{{ error }}</p>
                </div>

                <!-- Profile Content -->
                <div v-else-if="profile" class="space-y-8">
                    <!-- Summary -->
                    <div class="text-center p-6 bg-gradient-to-br from-[#1DB954]/20 to-[#1DB954]/5 rounded-xl border border-[#1DB954]/10">
                        <p class="text-xl text-[#1DB954] font-bold">{{ profile.summary }}</p>
                    </div>

                    <!-- Profile Sections -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="(section, key) in profile.sections"
                             :key="key"
                             class="relative group p-4 rounded-lg bg-[#181818] hover:bg-[#282828] transition-all duration-300 cursor-default border border-[#282828]"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity rounded-lg"
                                 :style="{ backgroundImage: `linear-gradient(to bottom right, ${section.color}, transparent)` }"/>

                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                     :style="{ backgroundColor: `${section.color}30` }">
                                    <component :is="getIcon(section.icon)"
                                               class="w-5 h-5"
                                               :style="{ color: section.color }"/>
                                </div>
                                <div>
                                    <h4 class="text-[11px] uppercase font-bold text-gray-400 tracking-widest">
                                        {{ section.title }}
                                    </h4>
                                    <p class="text-lg font-bold">{{ section.value }}</p>
                                </div>
                                <span class="text-2xl ml-auto">{{ section.emoji }}</span>
                            </div>
                            <p class="text-sm text-gray-400 leading-relaxed">{{ section.description }}</p>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Audio Features -->
                        <div class="p-6 bg-[#181818] rounded-xl border border-[#282828]">
                            <h4 class="text-[11px] uppercase font-bold text-gray-400 tracking-widest mb-6">Audio Features Analysis</h4>
                            <div class="space-y-4">
                                <div v-for="feature in profile.features"
                                     :key="feature.name"
                                     class="group"
                                >
                                    <div class="flex justify-between mb-2">
                                        <span class="text-sm font-bold">{{ feature.name }}</span>
                                        <span class="text-sm text-gray-400">{{ feature.description }}</span>
                                    </div>
                                    <div class="bg-[#282828] rounded-full h-2 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 group-hover:opacity-90"
                                             :style="{
                                                 width: `${feature.value * 100}%`,
                                                 backgroundColor: feature.color
                                             }"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Radar Chart -->
                        <div class="p-6 bg-[#181818] rounded-xl border border-[#282828] aspect-square">
                            <h4 class="text-[11px] uppercase font-bold text-gray-400 tracking-widest mb-4">Sound Profile</h4>
                            <Radar
                                v-if="profile.charts.radar"
                                :data="profile.charts.radar"
                                :options="chartOptions"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.gradient-animate {
    animation: gradient 15s ease infinite;
    background-size: 200% 200%;
}
</style>
