<template>
    <div>
        <div class="flex justify-between items-center mb-1">
            <span class="text-sm font-medium text-gray-700">{{ priority }}</span>
            <span class="text-sm text-gray-500">{{ count }} ({{ percentage }}%)</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div
                :class="[
                    'h-2.5 rounded-full transition-all duration-300',
                    colorClass
                ]"
                :style="{ width: `${percentage}%` }"
            ></div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    priority: String,
    count: Number,
    total: Number,
});

const percentage = computed(() => {
    if (props.total === 0) return 0;
    return Math.round((props.count / props.total) * 100);
});

const colorClass = computed(() => {
    const colors = {
        'Low': 'bg-green-600',
        'Medium': 'bg-yellow-600',
        'High': 'bg-orange-600',
        'Critical': 'bg-red-600',
    };
    return colors[props.priority] || 'bg-gray-600';
});
</script>