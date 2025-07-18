<template>
    <AuthenticatedLayout>
        <Head :title="`Ticket ${ticket.ticket_number}`" />
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ ticket.ticket_number }}</h1>
                            <p class="text-gray-600 mt-1">{{ ticket.subject }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link 
                                :href="route('client.tickets.index')"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
                            >
                                ← Back to Tickets
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Ticket Details -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Details</h3>
                                
                                <div class="prose max-w-none">
                                    <p><strong>Description:</strong></p>
                                    <div class="bg-gray-50 p-4 rounded-md mt-2">
                                        {{ ticket.description }}
                                    </div>
                                </div>

                                <!-- Attachment -->
                                <div v-if="ticket.attachment" class="mt-6">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Attachment</h4>
                                    <a 
                                        :href="`/storage/${ticket.attachment}`" 
                                        target="_blank"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        Download Attachment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Chat/Messages Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white">
                                <TicketChat 
                                    :ticket="ticket" 
                                    :current-user-id="$page.props.auth.user.id"
                                />
                            </div>
                        </div>

                        <!-- Ticket Info -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Information</h3>
                                
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            <span :class="getStatusClass(ticket.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ ticket.status }}
                                            </span>
                                        </dd>
                                    </div>
                                    
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                        <dd class="mt-1">
                                            <span :class="getPriorityClass(ticket.priority)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ ticket.priority }}
                                            </span>
                                        </dd>
                                    </div>
                                    
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.category?.name || 'N/A' }}</dd>
                                    </div>
                                    
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(ticket.created_at) }}</dd>
                                    </div>
                                    
                                    <div v-if="ticket.resolved_at">
                                        <dt class="text-sm font-medium text-gray-500">Resolved</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(ticket.resolved_at) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Agent Info -->
                        <div v-if="ticket.agent" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Agent</h3>
                                
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.agent.user.name }}</dd>
                                    </div>
                                    
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.agent.user.email }}</dd>
                                    </div>
                                    
                                    <div v-if="ticket.agent.department">
                                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.agent.department }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- No Agent Message -->
                        <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Agent Assignment</h3>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                No Agent Assigned Yet
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p>Your ticket is waiting to be assigned to an agent. You'll be able to chat once an agent is assigned.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TicketChat from '@/Components/TicketChat.vue'

const props = defineProps({
    ticket: Object,
    chatifyConversationId: String,
})

const getPriorityClass = (priority) => {
    const classes = {
        'Low': 'bg-green-100 text-green-800',
        'Medium': 'bg-yellow-100 text-yellow-800',
        'High': 'bg-orange-100 text-orange-800',
        'Critical': 'bg-red-100 text-red-800'
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}

const getStatusClass = (status) => {
    const classes = {
        'Open': 'bg-blue-100 text-blue-800',
        'In Progress': 'bg-yellow-100 text-yellow-800',
        'Resolved': 'bg-green-100 text-green-800',
        'Closed': 'bg-gray-100 text-gray-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>