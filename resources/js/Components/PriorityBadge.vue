<template>
    <span
        :class="[
            baseClasses,
            colorClasses[priority],
            sizeClasses[size]
        ]"
    >
        <svg
            v-if="showIcon"
            :class="iconClasses[size]"
            fill="currentColor"
            viewBox="0 0 8 8"
        >
            <circle cx="4" cy="4" :r="iconRadius[priority]" />
        </svg>
        {{ priority }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    priority: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    showIcon: {
        type: Boolean,
        default: true
    }
});

const baseClasses = 'inline-flex items-center font-medium rounded-full';

const colorClasses = {
    'Low': 'bg-green-100 text-green-800',
    'Medium': 'bg-yellow-100 text-yellow-800',
    'High': 'bg-orange-100 text-orange-800',
    'Critical': 'bg-red-100 text-red-800',
};

const sizeClasses = {
    'sm': 'px-2.5 py-0.5 text-xs',
    'md': 'px-3 py-1 text-sm',
    'lg': 'px-4 py-1.5 text-base',
};

const iconClasses = {
    'sm': 'mr-1.5 h-2 w-2',
    'md': 'mr-1.5 h-2.5 w-2.5',
    'lg': 'mr-2 h-3 w-3',
};

const iconRadius = {
    'Low': 2,
    'Medium': 3,
    'High': 3.5,
    'Critical': 4,
};
</script>