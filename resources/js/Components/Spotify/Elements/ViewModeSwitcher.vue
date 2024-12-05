<script setup lang="ts">
defineProps<{
    modelValue: string;
}>();

defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const modes = [
    { value: 'grid', icon: 'th', label: 'Grid view' },
    { value: 'list', icon: 'list', label: 'List view' },
    { value: 'table', icon: 'table', label: 'Table view' }
] as const;
</script>

<template>
    <div class="flex items-center gap-1">
        <button
            v-for="mode in modes"
            :key="mode.value"
            @click="$emit('update:modelValue', mode.value)"
            :class="[
                'p-1.5 sm:p-2 rounded flex items-center justify-center transition-all duration-200',
                'w-8 h-8 sm:w-9 sm:h-9',
                modelValue === mode.value
                    ? 'bg-green-500 text-white shadow-lg shadow-green-500/20'
                    : 'text-gray-400 hover:bg-neutral-800/50 hover:text-white',
                mode.value === 'table' ? 'hidden sm:flex' : ''
            ]"
            :title="mode.label"
        >
            <i :class="[
                'fas',
                `fa-${mode.icon}`,
                'text-sm sm:text-base'
            ]"></i>
        </button>
    </div>
</template>
