<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ticket
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subject
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priority
                    </th>
                    <th v-if="showClient" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Client
                    </th>
                    <th v-if="showAgent" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Agent
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ ticket.ticket_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ ticket.subject }}</div>
                        <div class="text-sm text-gray-500">{{ ticket.category?.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <StatusBadge :status="ticket.status" />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <PriorityBadge :priority="ticket.priority" />
                    </td>
                    <td v-if="showClient" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ ticket.client?.user?.name || 'N/A' }}
                    </td>
                    <td v-if="showAgent" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ ticket.agent?.user?.name || 'Unassigned' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(ticket.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Link 
                            :href="getTicketUrl(ticket)"
                            class="text-indigo-600 hover:text-indigo-900"
                        >
                            View
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Empty State -->
        <div v-if="tickets.length === 0" class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="mt-2">No tickets found</p>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import StatusBadge from '@/Components/StatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'

const props = defineProps({
    tickets: {
        type: Array,
        default: () => []
    },
    showClient: {
        type: Boolean,
        default: false
    },
    showAgent: {
        type: Boolean,
        default: false
    }
})

const page = usePage()

const userRole = computed(() => page.props.auth.role || 'client')

const getTicketUrl = (ticket) => {
    if (userRole.value === 'admin') {
        return `/admin/tickets/${ticket.id}`
    } else if (userRole.value === 'agent') {
        return `/agent/tickets/${ticket.id}`
    } else {
        return `/client/tickets/${ticket.id}`
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>