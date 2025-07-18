<template>
    <span
        :class="[
            baseClasses,
            colorClasses[status],
            sizeClasses[size]
        ]"
    >
        {{ status }}
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    }
});

const baseClasses = 'inline-flex items-center font-medium rounded-full';

const colorClasses = {
    'Open': 'bg-blue-100 text-blue-800',
    'In Progress': 'bg-yellow-100 text-yellow-800',
    'Resolved': 'bg-green-100 text-green-800',
    'Closed': 'bg-gray-100 text-gray-800',
};

const sizeClasses = {
    'sm': 'px-2.5 py-0.5 text-xs',
    'md': 'px-3 py-1 text-sm',
    'lg': 'px-4 py-1.5 text-base',
};
</script>