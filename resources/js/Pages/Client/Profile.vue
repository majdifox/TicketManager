<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <Link :href="route('client.dashboard')" class="text-gray-600 hover:text-gray-900 mr-4">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
          </div>
          <div class="text-sm text-gray-500">
            Last updated: {{ formatDate(user.updated_at) }}
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
              <div class="w-24 h-24 bg-blue-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900">{{ user.name }}</h3>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
              <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Active {{ user.role.name }}
              </div>
            </div>
            
            <div class="mt-6 space-y-4">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Member since</span>
                <span class="text-gray-900">{{ formatDate(user.created_at) }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Company</span>
                <span class="text-gray-900">{{ user.client.company || 'Not specified' }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Phone</span>
                <span class="text-gray-900">{{ user.client.phone || 'Not specified' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
              <p class="text-sm text-gray-600 mt-1">Update your profile information and contact details.</p>
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{'border-red-300': errors.name}"
                  >
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <!-- Company -->
                <div>
                  <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                  <input
                    type="text"
                    id="company"
                    v-model="form.company"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{'border-red-300': errors.company}"
                  >
                  <p v-if="errors.company" class="mt-1 text-sm text-red-600">{{ errors.company }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                  <input
                    type="tel"
                    id="phone"
                    v-model="form.phone"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                  <p class="mt-1 text-xs text-gray-500">Email cannot be changed. Contact support if needed.</p>
                </div>
              </div>

              <!-- Address -->
              <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea
                  id="address"
                  v-model="form.address"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  :class="{'border-red-300': errors.address}"
                  placeholder="Enter your full address..."
                ></textarea>
                <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
              </div>

              <!-- Submit Button -->
              <div class="mt-8 flex justify-end">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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

          <!-- Security Section -->
          <div class="bg-white rounded-lg shadow-md mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Security</h2>
              <p class="text-sm text-gray-600 mt-1">Manage your account security settings.</p>
            </div>

            <div class="p-6">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Password</h3>
                  <p class="text-sm text-gray-500">Last changed: {{ formatDate(user.updated_at) }}</p>
                </div>
                <button class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 focus:outline-none focus:underline">
                  Change Password
                </button>
              </div>

              <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h3>
                  <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                </div>
                <button class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 focus:outline-none focus:underline">
                  Enable 2FA
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
  name: 'ClientProfile',
  components: {
    Head,
    Link,
  },
  
  props: {
    user: {
      type: Object,
      required: true,
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
  },

  setup(props) {
    const form = useForm({
      name: props.user.name || '',
      company: props.user.client?.company || '',
      phone: props.user.client?.phone || '',
      address: props.user.client?.address || '',
    })

    const updateProfile = () => {
      form.put(route('client.profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
          // Handle success (maybe show a toast notification)
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