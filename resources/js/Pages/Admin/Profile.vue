<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <Link :href="route('admin.dashboard')" class="text-gray-600 hover:text-gray-900 mr-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">Administrator Profile</h1>
          </div>
          <div class="flex items-center space-x-4">
            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
              </svg>
              System Administrator
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
              <div class="w-24 h-24 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900">{{ user.name }}</h3>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
              <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Super {{ user.role.name }}
              </div>
            </div>
            
            <div class="mt-6 space-y-4">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Admin since</span>
                <span class="text-gray-900">{{ formatDate(user.created_at) }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Department</span>
                <span class="text-gray-900">{{ user.admin?.department || 'System Administration' }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Phone</span>
                <span class="text-gray-900">{{ user.admin?.phone || 'Not specified' }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Last Login</span>
                <span class="text-gray-900">{{ formatDate(user.updated_at) }}</span>
              </div>
            </div>

            <!-- Admin Quick Actions -->
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h4 class="text-sm font-medium text-gray-900 mb-3">Admin Tools</h4>
              <div class="space-y-2">
                <Link :href="route('admin.users.index')" class="w-full flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                  </svg>
                  Manage Users
                </Link>
                <Link :href="route('admin.reports.index')" class="w-full flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  View Reports
                </Link>
                <Link :href="route('admin.categories.index')" class="w-full flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  Manage Categories
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Administrator Information</h2>
              <p class="text-sm text-gray-600 mt-1">Update your administrator profile and contact details.</p>
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    :class="{'border-red-300': errors.department}"
                  >
                    <option value="">Select Department</option>
                    <option value="System Administration">System Administration</option>
                    <option value="IT Management">IT Management</option>
                    <option value="Operations">Operations</option>
                    <option value="Security">Security</option>
                    <option value="Executive">Executive</option>
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
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
                  <p class="mt-1 text-xs text-gray-500">Email cannot be changed for security reasons.</p>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="mt-8 flex justify-end">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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

          <!-- System Overview -->
          <div class="bg-white rounded-lg shadow-md mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">System Overview</h2>
              <p class="text-sm text-gray-600 mt-1">Quick system metrics and status.</p>
            </div>

            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="text-center">
                  <div class="text-2xl font-bold text-blue-600">{{ stats.total_tickets || 0 }}</div>
                  <div class="text-sm text-gray-500">Total Tickets</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-green-600">{{ stats.total_agents || 0 }}</div>
                  <div class="text-sm text-gray-500">Active Agents</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-purple-600">{{ stats.total_clients || 0 }}</div>
                  <div class="text-sm text-gray-500">Clients</div>
                </div>
                <div class="text-center">
                  <div class="text-2xl font-bold text-orange-600">{{ stats.total_categories || 0 }}</div>
                  <div class="text-sm text-gray-500">Categories</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="text-sm font-medium text-gray-900 mb-2">Ticket Status</h3>
                  <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Open</span>
                      <span class="font-medium text-red-600">{{ stats.open_tickets || 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">In Progress</span>
                      <span class="font-medium text-yellow-600">{{ stats.in_progress_tickets || 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Resolved</span>
                      <span class="font-medium text-green-600">{{ stats.resolved_tickets || 0 }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                  <h3 class="text-sm font-medium text-gray-900 mb-2">System Health</h3>
                  <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Available Agents</span>
                      <span class="font-medium text-green-600">{{ stats.available_agents || 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Avg. Resolution</span>
                      <span class="font-medium text-blue-600">{{ stats.avg_resolution_time || 0 }}h</span>
                    </div>
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">System Status</span>
                      <span class="font-medium text-green-600">Operational</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Security & Settings -->
          <div class="bg-white rounded-lg shadow-md mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Security & Settings</h2>
              <p class="text-sm text-gray-600 mt-1">Manage your account security and system preferences.</p>
            </div>

            <div class="p-6 space-y-6">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Password</h3>
                  <p class="text-sm text-gray-500">Last changed: {{ formatDate(user.updated_at) }}</p>
                </div>
                <button class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none focus:underline">
                  Change Password
                </button>
              </div>

              <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h3>
                  <p class="text-sm text-gray-500">Enhanced security for administrator account</p>
                </div>
                <button class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none focus:underline">
                  Configure 2FA
                </button>
              </div>

              <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Session Management</h3>
                  <p class="text-sm text-gray-500">Manage active sessions and login history</p>
                </div>
                <button class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none focus:underline">
                  View Sessions
                </button>
              </div>

              <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">System Backup</h3>
                  <p class="text-sm text-gray-500">Last backup: Today at 3:00 AM</p>
                </div>
                <button class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 focus:outline-none focus:underline">
                  Backup Settings
                </button>
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
  name: 'AdminProfile',
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
      department: props.user.admin?.department || '',
      phone: props.user.admin?.phone || '',
    })

    const updateProfile = () => {
      form.put(route('admin.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
          // Handle success
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
      formatDate,
      errors: props.errors || {},
    }
  },
}
</script>

<style scoped>
/* Add any custom styles here */
</style>