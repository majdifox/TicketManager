<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <Link :href="route('agent.dashboard')" class="text-gray-600 hover:text-gray-900 mr-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">Agent Profile</h1>
          </div>
          <div class="flex items-center space-x-4">
            <div class="flex items-center">
              <span class="text-sm text-gray-500 mr-2">Status:</span>
              <span v-if="user.agent.is_available" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Available
              </span>
              <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Unavailable
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center">
              <div class="w-24 h-24 bg-purple-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900">{{ user.name }}</h3>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
              <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Support {{ user.role.name }}
              </div>
            </div>
            
            <div class="mt-6 space-y-4">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Employee since</span>
                <span class="text-gray-900">{{ formatDate(user.created_at) }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Department</span>
                <span class="text-gray-900">{{ user.agent.department || 'Not specified' }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Phone</span>
                <span class="text-gray-900">{{ user.agent.phone || 'Not specified' }}</span>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
              <div class="space-y-2">
                <button 
                  @click="toggleAvailability"
                  :class="[
                    'w-full flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md',
                    user.agent.is_available 
                      ? 'text-red-700 bg-red-100 hover:bg-red-200' 
                      : 'text-green-700 bg-green-100 hover:bg-green-200'
                  ]"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ user.agent.is_available ? 'Go Unavailable' : 'Go Available' }}
                </button>
                <Link :href="route('agent.tickets.index')" class="w-full flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  View My Tickets
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Agent Information</h2>
              <p class="text-sm text-gray-600 mt-1">Update your agent profile and work details.</p>
            </div>

            <form @submit.prevent="updateProfile" class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                  <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    :class="{'border-red-300': errors.name}"
                  >
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <!-- Department -->
                <div>
                  <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                  <select
                    id="department"
                    v-model="form.department"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    :class="{'border-red-300': errors.department}"
                  >
                    <option value="">Select Department</option>
                    <option value="Technical Support">Technical Support</option>
                    <option value="Customer Service">Customer Service</option>
                    <option value="Sales">Sales</option>
                    <option value="Billing">Billing</option>
                    <option value="General">General</option>
                  </select>
                  <p v-if="errors.department" class="mt-1 text-sm text-red-600">{{ errors.department }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                  <input
                    type="tel"
                    id="phone"
                    v-model="form.phone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    :class="{'border-red-300': errors.phone}"
                  >
                  <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
                </div>

                <!-- Email (Read-only) -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                  <input
                    type="email"
                    id="email"
                    :value="user.email"
                    readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                  >
                  <p class="mt-1 text-xs text-gray-500">Email cannot be changed. Contact admin if needed.</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="mt-8 flex justify-end">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="form.processing" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                  </span>
                  <span v-else>Update Profile</span>
                </button>
              </div>
            </form>
          </div>

          <!-- Work Statistics -->
          <div class="bg-white rounded-lg shadow-md mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Work Statistics</h2>
              <p class="text-sm text-gray-600 mt-1">Your performance metrics and work overview.</p>
            </div>

            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                  <div class="text-2xl font-bold text-purple-600">{{ stats.total_tickets || 0 }}</div>
                  <div class="text-sm text-gray-500">Total Tickets</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-green-600">{{ stats.resolved_tickets || 0 }}</div>
                  <div class="text-sm text-gray-500">Resolved</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-orange-600">{{ stats.pending_tickets || 0 }}</div>
                  <div class="text-sm text-gray-500">Pending</div>
                </div>
              </div>

              <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Specializations</h3>
                <div class="flex flex-wrap gap-2">
                  <span 
                    v-for="category in stats.categories" 
                    :key="category"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                  >
                    {{ category }}
                  </span>
                  <span v-if="!stats.categories || stats.categories.length === 0" class="text-sm text-gray-500">
                    No specializations assigned
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

export default {
  name: 'AgentProfile',
  components: {
    Head,
    Link,
  },
  
  props: {
    user: {
      type: Object,
      required: true,
    },
    stats: {
      type: Object,
      default: () => ({}),
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },

  setup(props) {
    const form = useForm({
      name: props.user.name || '',
      department: props.user.agent?.department || '',
      phone: props.user.agent?.phone || '',
    })

    const updateProfile = () => {
      form.put(route('agent.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
          // Handle success
        },
      })
    }

    const toggleAvailability = () => {
      // Toggle availability using the existing route
      const availabilityForm = useForm({
        is_available: !props.user.agent.is_available
      })
      
      availabilityForm.post(route('agent.availability'), {
        preserveScroll: true,
        onSuccess: () => {
          // Update local state or refresh
          props.user.agent.is_available = !props.user.agent.is_available
        },
      })
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    }

    return {
      form,
      updateProfile,
      toggleAvailability,
      formatDate,
      errors: props.errors || {},
    }
  },
}
</script>

<style scoped>
/* Add any custom styles here */
</style>