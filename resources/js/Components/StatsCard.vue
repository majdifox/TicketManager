<template>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div :class="`flex-shrink-0 bg-${color}-500 rounded-md p-3`">
                    <component :is="iconComponent" class="h-6 w-6 text-white" />
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            {{ title }}
                        </dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ value }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { 
    TicketIcon, 
    ClockIcon, 
    UsersIcon, 
    UserIcon, 
    CheckCircleIcon,
    ExclamationCircleIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
    title: String,
    value: [String, Number],
    icon: String,
    color: {
        type: String,
        default: 'indigo'
    }
});

const iconComponent = computed(() => {
    const icons = {
        ticket: TicketIcon,
        clock: ClockIcon,
        users: UsersIcon,
        user: UserIcon,
        check: CheckCircleIcon,
        progress: ExclamationCircleIcon,
    };
    return icons[props.icon] || TicketIcon;
});
</script>