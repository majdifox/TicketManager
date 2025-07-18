<template>
    <div class="flex items-center space-x-3">
        <span class="text-sm font-medium text-gray-700">Status:</span>
        <button
            @click="toggleAvailability"
            :disabled="updating"
            :class="[
                'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
                isAvailable ? 'bg-green-600' : 'bg-gray-200',
                updating && 'opacity-50 cursor-not-allowed'
            ]"
            role="switch"
            :aria-checked="isAvailable"
        >
            <span
                :class="[
                    'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200',
                    isAvailable ? 'translate-x-5' : 'translate-x-0'
                ]"
            />
        </button>
        <span class="text-sm" :class="isAvailable ? 'text-green-600 font-medium' : 'text-gray-500'">
            {{ isAvailable ? 'Available' : 'Unavailable' }}
        </span>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    initialStatus: {
        type: Boolean,
        required: true
    }
});

const isAvailable = ref(props.initialStatus);
const updating = ref(false);

const toggleAvailability = () => {
    updating.value = true;
    
    router.post(route('agent.availability'), {
        is_available: !isAvailable.value
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isAvailable.value = !isAvailable.value;
        },
        onFinish: () => {
            updating.value = false;
        }
    });
};
</script>