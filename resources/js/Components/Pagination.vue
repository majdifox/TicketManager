<template>
    <div class="relative">
        <!-- Notification Bell -->
        <button
            @click="toggleDropdown"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-5 5-5-5h5V3h0zm-7-7a3 3 0 016 0v4l2 1v1H6v-1l2-1V10z"></path>
            </svg>
            
            <!-- Badge -->
            <span 
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <div 
            v-if="isOpen"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        >
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Notifications</h3>
                    <div class="flex space-x-2">
                        <button
                            v-if="unreadCount > 0"
                            @click="markAllAsRead"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            Mark all read
                        </button>
                        <Link 
                            :href="viewAllRoute"
                            class="text-sm text-blue-600 hover:text-blue-800"
                            @click="closeDropdown"
                        >
                            View all
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="max-h-96 overflow-y-auto">
                <div v-if="loading" class="p-4 text-center text-gray-500">
                    Loading...
                </div>
                
                <div v-else-if="notifications.length === 0" class="p-4 text-center text-gray-500">
                    No notifications
                </div>

                <div v-else>
                    <div
                        v-for="notification in notifications"
                        :key="notification.id"
                        class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                        :class="{ 'bg-blue-50': !notification.read_at }"
                        @click="markAsRead(notification.id)"
                    >
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ getNotificationTitle(notification) }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ getNotificationMessage(notification) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-2">
                                    {{ formatDate(notification.created_at) }}
                                </p>
                            </div>
                            <div 
                                v-if="!notification.read_at"
                                class="ml-2 w-2 h-2 bg-blue-500 rounded-full"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div 
            v-if="isOpen"
            @click="closeDropdown"
            class="fixed inset-0 z-40"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()
const isOpen = ref(false)
const loading = ref(false)
const notifications = ref([])
const unreadCount = ref(0)

const userRole = computed(() => page.props.auth.role || 'client')

const viewAllRoute = computed(() => {
    if (userRole.value === 'admin') return route('admin.notifications')
    if (userRole.value === 'agent') return route('agent.notifications')
    return route('client.notifications')
})

const toggleDropdown = async () => {
    isOpen.value = !isOpen.value
    if (isOpen.value) {
        await fetchNotifications()
    }
}

const closeDropdown = () => {
    isOpen.value = false
}

const fetchNotifications = async () => {
    try {
        loading.value = true
        const response = await axios.get('/notifications/fetch', {
            params: { unread_only: 0 }
        })
        notifications.value = response.data.notifications
        unreadCount.value = response.data.unread_count
    } catch (error) {
        console.error('Failed to fetch notifications:', error)
    } finally {
        loading.value = false
    }
}

const markAsRead = async (notificationId) => {
    try {
        const response = await axios.post(`/notifications/${notificationId}/read`)
        unreadCount.value = response.data.unread_count
        
        // Update the notification in the list
        const notification = notifications.value.find(n => n.id === notificationId)
        if (notification) {
            notification.read_at = new Date().toISOString()
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error)
    }
}

const markAllAsRead = async () => {
    try {
        await axios.post('/notifications/read-all')
        unreadCount.value = 0
        
        // Update all notifications in the list
        notifications.value.forEach(notification => {
            notification.read_at = new Date().toISOString()
        })
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
    }
}

const getNotificationTitle = (notification) => {
    const data = JSON.parse(notification.data)
    return data.title || 'Notification'
}

const getNotificationMessage = (notification) => {
    const data = JSON.parse(notification.data)
    return data.message || 'You have a new notification'
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInMinutes = Math.floor((now - date) / (1000 * 60))
    
    if (diffInMinutes < 1) return 'Just now'
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`
    return `${Math.floor(diffInMinutes / 1440)}d ago`
}

// Fetch initial unread count
onMounted(async () => {
    try {
        const response = await axios.get('/notifications/fetch', {
            params: { unread_only: 1 }
        })
        unreadCount.value = response.data.unread_count
    } catch (error) {
        console.error('Failed to fetch unread count:', error)
    }
})
</script>