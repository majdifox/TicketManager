<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-2">Welcome back, {{ $page.props.auth.user.name }}!</h3>
                        <p class="text-gray-600">Manage your support tickets and get help from our team.</p>
                        <div class="mt-4">
                            <Link
                                :href="route('client.tickets.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Create New Ticket
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatsCard
                        title="Total Tickets"
                        :value="stats.total_tickets"
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

                <!-- Ticket Trend Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Ticket Activity (Last 7 Days)</h3>
                        <TicketTrendChart :data="ticketTrend" />
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Recent Tickets</h3>
                            <Link
                                :href="route('client.tickets.index')"
                                class="text-sm text-indigo-600 hover:text-indigo-900"
                            >
                                View All →
                            </Link>
                        </div>
                        
                        <div v-if="recentTickets.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2">No tickets yet. Create your first ticket to get started!</p>
                        </div>
                        
                        <TicketsTable v-else :tickets="recentTickets" :show-agent="true" />
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
import TicketTrendChart from '@/Components/TicketTrendChart.vue';

defineProps({
    stats: Object,
    recentTickets: Array,
    ticketTrend: Array,
});
</script>