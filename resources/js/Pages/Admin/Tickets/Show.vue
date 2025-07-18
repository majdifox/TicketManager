<template>
    <Head :title="`Ticket #${ticket.ticket_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ticket #{{ ticket.ticket_number }}
                </h2>
                <Link
                    :href="route('admin.tickets.index')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Back to Tickets
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Ticket Details -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">{{ ticket.subject }}</h3>
                                <div class="prose max-w-none">
                                    {{ ticket.description }}
                                </div>
                                <div v-if="ticket.attachment" class="mt-4">
                                    <a 
                                        :href="`/storage/${ticket.attachment}`" 
                                        target="_blank"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        View Attachment
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Messages/Comments Section -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Messages</h3>
                                <div class="text-sm text-gray-500">
                                    Message functionality would go here
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Ticket Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Ticket Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    ticket.status === 'Open' && 'bg-blue-100 text-blue-800',
                                                    ticket.status === 'In Progress' && 'bg-yellow-100 text-yellow-800',
                                                    ticket.status === 'Resolved' && 'bg-green-100 text-green-800',
                                                    ticket.status === 'Closed' && 'bg-gray-100 text-gray-800',
                                                ]"
                                            >
                                                {{ ticket.status }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                        <dd class="mt-1">
                                            <span
                                                :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    ticket.priority === 'Low' && 'bg-gray-100 text-gray-800',
                                                    ticket.priority === 'Medium' && 'bg-yellow-100 text-yellow-800',
                                                    ticket.priority === 'High' && 'bg-orange-100 text-orange-800',
                                                    ticket.priority === 'Critical' && 'bg-red-100 text-red-800',
                                                ]"
                                            >
                                                {{ ticket.priority }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.category?.name || 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Client</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.client?.user?.name || 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Assigned Agent</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ ticket.agent?.user?.name || 'Unassigned' }}</dd>
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

                        <!-- Actions -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold mb-4">Actions</h3>
                                <div class="space-y-2">
                                    <Link
                                        :href="route('admin.tickets.edit', ticket.id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                                    >
                                        Edit Ticket
                                    </Link>
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
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    ticket: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};
</script>