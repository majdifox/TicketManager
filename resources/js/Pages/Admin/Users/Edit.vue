<template>
    <Head title="Edit User" />
    
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        id="name"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        id="email"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input
                                        v-model="form.password"
                                        type="password"
                                        id="password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p class="mt-1 text-sm text-gray-600">Leave blank to keep current password</p>
                                    <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.password }}
                                    </div>
                                </div>

                                <!-- Password Confirmation -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input
                                        v-model="form.password_confirmation"
                                        type="password"
                                        id="password_confirmation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>

                                <!-- Department -->
                                <div>
                                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                                    <input
                                        v-model="form.department"
                                        type="text"
                                        id="department"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.department" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.department }}
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        id="phone"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.phone }}
                                    </div>
                                </div>

                                <!-- Role (if admin/agent) -->
                                <div v-if="user.role_id !== 3">
                                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                                    <select
                                        v-model="form.role_id"
                                        id="role_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Select Role</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.role_id" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.role_id }}
                                    </div>
                                </div>

                                <!-- Agent Availability -->
                                <div v-if="user.role_id === 2">
                                    <label class="flex items-center">
                                        <input
                                            v-model="form.is_available"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <span class="ml-2 text-sm text-gray-600">Available for assignments</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <Link
                                    :href="route('admin.users.index')"
                                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50"
                                >
                                    Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    department: props.user.admin?.department || props.user.agent?.department || '',
    phone: props.user.admin?.phone || props.user.agent?.phone || '',
    role_id: props.user.role_id,
    is_available: props.user.agent?.is_available || false,
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>