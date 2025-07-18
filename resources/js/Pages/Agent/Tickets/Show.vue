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
                                :href="route('agent.tickets.index')"
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

                        <!-- Status Update Form -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Ticket</h3>
                                
                                <form @submit.prevent="updateTicket">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Status -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                            <select 
                                                v-model="updateForm.status"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required
                                            >
                                                <option v-for="status in statuses" :key="status" :value="status">
                                                    {{ status }}
                                                </option>
                                            </select>
                                            <div v-if="errors.status" class="mt-1 text-sm text-red-600">
                                                {{ errors.status }}
                                            </div>
                                        </div>

                                        <!-- Priority -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                            <select 
                                                v-model="updateForm.priority"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                required
                                            >
                                                <option v-for="priority in priorities" :key="priority" :value="priority">
                                                    {{ priority }}
                                                </option>
                                            </select>
                                            <div v-if="errors.priority" class="mt-1 text-sm text-red-600">
                                                {{ errors.priority }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button 
                                            type="submit"
                                            :disabled="processing"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span v-if="processing">Updating...</span>
                                            <span v-else>Update Ticket</span>
                                        </button>
                                    </div>
                                </form>
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

                        <!-- Client Info -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Information</h3>
                                
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.client?.user?.name || 'N/A' }}</dd>
                                    </div>
                                    
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.client?.user?.email || 'N/A' }}</dd>
                                    </div>
                                    
                                    <div v-if="ticket.client?.company">
                                        <dt class="text-sm font-medium text-gray-500">Company</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.client.company }}</dd>
                                    </div>
                                    
                                    <div v-if="ticket.client?.phone">
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.client.phone }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 bg-white border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                                
                                <div class="space-y-3">
                                    <button 
                                        @click="quickUpdateStatus('In Progress')"
                                        :disabled="ticket.status === 'In Progress' || processing"
                                        class="w-full px-3 py-2 text-sm bg-yellow-600 text-white rounded-md hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Mark In Progress
                                    </button>
                                    
                                    <button 
                                        @click="quickUpdateStatus('Resolved')"
                                        :disabled="ticket.status === 'Resolved' || ticket.status === 'Closed' || processing"
                                        class="w-full px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Mark Resolved
                                    </button>
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
import { reactive, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TicketChat from '@/Components/TicketChat.vue'

const props = defineProps({
    ticket: Object,
    chatifyConversationId: String,
    statuses: Array,
    priorities: Array,
    errors: Object
})

const processing = ref(false)

const updateForm = reactive({
    status: props.ticket.status,
    priority: props.ticket.priority
})

const updateTicket = () => {
    processing.value = true
    
    useForm(updateForm).put(route('agent.tickets.update', props.ticket.id), {
        onSuccess: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

const quickUpdateStatus = (status) => {
    processing.value = true
    
    useForm({
        status: status,
        priority: props.ticket.priority
    }).put(route('agent.tickets.update', props.ticket.id), {
        onSuccess: () => {
            processing.value = false
            updateForm.status = status
        },
        onError: () => {
            processing.value = false
        }
    })
}

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