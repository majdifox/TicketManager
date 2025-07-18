<template>
    <div class="w-full">
        <div v-if="agents && agents.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Agent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Open Tickets
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Resolved Today
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Performance
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="agent in agents" :key="agent.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ getInitials(agent.name) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ agent.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ agent.email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getStatusClass(agent.is_available)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                <span :class="getStatusDotClass(agent.is_available)" class="w-2 h-2 rounded-full mr-1"></span>
                                {{ agent.is_available ? 'Available' : 'Busy' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ agent.open_tickets || 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ agent.resolved_today || 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-1 mr-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div 
                                            :class="getPerformanceBarClass(agent.performance_score)"
                                            class="h-2 rounded-full transition-all duration-300"
                                            :style="{ width: (agent.performance_score || 0) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ agent.performance_score || 0 }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
            No agents found
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    agents: {
        type: Array,
        default: () => []
    }
});

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const getStatusClass = (isAvailable) => {
    return isAvailable 
        ? 'bg-green-100 text-green-800' 
        : 'bg-yellow-100 text-yellow-800';
};

const getStatusDotClass = (isAvailable) => {
    return isAvailable 
        ? 'bg-green-400' 
        : 'bg-yellow-400';
};

const getPerformanceBarClass = (score) => {
    if (score >= 80) return 'bg-green-500';
    if (score >= 60) return 'bg-yellow-500';
    if (score >= 40) return 'bg-orange-500';
    return 'bg-red-500';
};
</script>