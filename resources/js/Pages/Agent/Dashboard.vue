<template>
    <Head title="Agent Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agent Dashboard</h2>
                <AvailabilityToggle :initial-status="stats.is_available" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatsCard
                        title="Assigned Tickets"
                        :value="stats.assigned_tickets"
                        icon="ticket"
                        color="blue"
                    />
                    <StatsCard
                        title="Open"
                        :value="stats.open_tickets"
                        icon="clock"
                        color="yellow"
                    />
                    <StatsCard
                        title="In Progress"
                        :value="stats.in_progress_tickets"
                        icon="progress"
                        color="orange"
                    />
                    <StatsCard
                        title="Resolved"
                        :value="stats.resolved_tickets"
                        icon="check"
                        color="green"
                    />
                </div>

                <!-- Priority Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Active Tickets by Priority</h3>
                            <div class="space-y-4">
                                <PriorityBar
                                    v-for="(count, priority) in priorityBreakdown"
                                    :key="priority"
                                    :priority="priority"
                                    :count="count"
                                    :total="stats.assigned_tickets"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">My Categories</h3>
                            <div class="space-y-2">
                                <span
                                    v-for="category in stats.categories"
                                    :key="category"
                                    class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded-full mr-2 mb-2"
                                >
                                    {{ category }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">My Recent Tickets</h3>
                            <Link
                                :href="route('agent.tickets.index')"
                                class="text-sm text-indigo-600 hover:text-indigo-900"
                            >
                                View All →
                            </Link>
                        </div>
                        <TicketsTable :tickets="recentTickets" :show-client="true" />
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
import TicketsTable from '@/Components/TicketsTable.vue';
import AvailabilityToggle from '@/Components/AvailabilityToggle.vue';
import PriorityBar from '@/Components/PriorityBar.vue';

defineProps({
    stats: Object,
    recentTickets: Array,
    priorityBreakdown: Object,
});
</script>