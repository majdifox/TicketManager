<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatsCard
                        title="Total Tickets"
                        :value="stats.total_tickets"
                        icon="ticket"
                        color="blue"
                    />
                    <StatsCard
                        title="Open Tickets"
                        :value="stats.open_tickets"
                        icon="clock"
                        color="yellow"
                    />
                    <StatsCard
                        title="Active Agents"
                        :value="`${stats.available_agents}/${stats.total_agents}`"
                        icon="users"
                        color="green"
                    />
                    <StatsCard
                        title="Total Clients"
                        :value="stats.total_clients"
                        icon="user"
                        color="purple"
                    />
                </div>

                <!-- Performance Metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Ticket Distribution by Priority</h3>
                            <PriorityChart :data="stats.tickets_by_priority" />
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Agent Performance</h3>
                            <AgentPerformanceTable :agents="stats.agent_performance" />
                        </div>
                    </div>
                </div>

                <!-- Recent Tickets - Temporarily commented out to test -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Tickets</h3>
                            <Link
                                :href="route('admin.tickets.index')"
                                class="text-sm text-indigo-600 hover:text-indigo-900"
                            >
                                View All →
                            </Link>
                        </div>
                        <!-- Temporarily replace with simple table to test -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subject
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="ticket in recentTickets" :key="ticket.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ticket.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ticket.subject }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ticket.status }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatsCard from '@/Components/StatsCard.vue';
import PriorityChart from '@/Components/PriorityChart.vue';
import AgentPerformanceTable from '@/Components/AgentPerformanceTable.vue';
// Temporarily commented out to test
// import TicketsTable from '@/Components/TicketsTable.vue';

defineProps({
    stats: Object,
    recentTickets: Array,
});
</script>