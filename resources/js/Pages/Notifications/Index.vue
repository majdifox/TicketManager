<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Notifications
                </h2>
                <button
                    v-if="unread_count > 0"
                    @click="markAllAsRead"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Mark All as Read
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <!-- Notifications List -->
                        <div v-if="notifications.data.length === 0" class="text-center py-8">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5V3h0zm-7-7a3 3 0 016 0v4l2 1v1H6v-1l2-1V10z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                                <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="notification in notifications.data"
                                :key="notification.id"
                                class="border rounded-lg p-4 hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-blue-50 border-blue-200': !notification.read_at }"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <h3 class="text-sm font-medium text-gray-900">
                                                {{ getNotificationTitle(notification) }}
                                            </h3>
                                            <span 
                                                v-if="!notification.read_at"
                                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                                                New
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ getNotificationMessage(notification) }}
                                        </p>
                                        <p class="mt-2 text-xs text-gray-500">
                                            {{ formatDate(notification.created_at) }}
                                        </p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button
                                            v-if="!notification.read_at"
                                            @click="markAsRead(notification.id)"
                                            class="text-sm text-blue-600 hover:text-blue-800"
                                        >
                                            Mark as read
                                        </button>
                                        <button
                                            @click="deleteNotification(notification.id)"
                                            class="text-sm text-red-600 hover:text-red-800"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="notifications.links" class="mt-6">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link
                                        v-if="notifications.prev_page_url"
                                        :href="notifications.prev_page_url"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Previous
                                    </Link>
                                    <Link
                                        v-if="notifications.next_page_url"
                                        :href="notifications.next_page_url"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    >
                                        Next
                                    </Link>
                                </div>
                                
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{ notifications.from || 0 }}</span>
                                            to
                                            <span class="font-medium">{{ notifications.to || 0 }}</span>
                                            of
                                            <span class="font-medium">{{ notifications.total }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <template v-for="link in notifications.links" :key="link.label">
                                                <Link
                                                    v-if="link.url"
                                                    :href="link.url"
                                                    v-html="link.label"
                                                    class="relative inline-flex items-center px-2 py-2 border text-sm font-medium"
                                                    :class="link.active 
                                                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                                />
                                                <span
                                                    v-else
                                                    v-html="link.label"
                                                    class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300"
                                                />
                                            </template>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    notifications: Object,
    unread_count: Number,
})

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/notifications/${notificationId}/read`)
        router.reload({ only: ['notifications', 'unread_count'] })
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/read-all')
        router.reload({ only: ['notifications', 'unread_count'] })
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

const deleteNotification = async (notificationId) => {
    if (confirm('Are you sure you want to delete this notification?')) {
        try {
            await axios.delete(`/notifications/${notificationId}`)
            router.reload({ only: ['notifications', 'unread_count'] })
        } catch (error) {
            console.error('Failed to delete notification:', error)
        }
    }
}

const getNotificationTitle = (notification) => {
    try {
        // Check if data is already an object or needs parsing
        const data = typeof notification.data === 'string' 
            ? JSON.parse(notification.data) 
            : notification.data;
        
        // Generate titles based on notification type
        switch (data.type) {
            case 'ticket_created':
                return `New Ticket: ${data.ticket_number}`;
            case 'ticket_assigned':
                return `Ticket Assigned: ${data.ticket_number}`;
            case 'ticket_status_updated':
                return `Ticket Updated: ${data.ticket_number}`;
            default:
                return 'Notification';
        }
    } catch (error) {
        console.error('Error parsing notification data:', error, notification);
        return 'Notification';
    }
}

const getNotificationMessage = (notification) => {
    try {
        // Check if data is already an object or needs parsing
        const data = typeof notification.data === 'string' 
            ? JSON.parse(notification.data) 
            : notification.data;
        
        // Generate messages based on notification type
        switch (data.type) {
            case 'ticket_created':
                return `A new ticket "${data.subject}" has been created with ${data.priority} priority.`;
            case 'ticket_assigned':
                return `You have been assigned to ticket "${data.subject}" from ${data.client_name}.`;
            case 'ticket_status_updated':
                return `Ticket "${data.subject}" status changed from ${data.old_status} to ${data.new_status}.`;
            default:
                return 'You have a new notification';
        }
    } catch (error) {
        console.error('Error parsing notification data:', error, notification);
        return 'You have a new notification';
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString()
}
</script>