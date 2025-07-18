<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="dashboardRoute">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="dashboardRoute" :active="route().current(dashboardRouteName)">
                                    Dashboard
                                </NavLink>
                                
                                <NavLink :href="ticketsRoute" :active="route().current(ticketsRouteName)">
                                    Tickets
                                </NavLink>

                                <!-- Admin Links -->
                                <template v-if="isAdmin">
                                    <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.*')">
                                        Users
                                    </NavLink>
                                    <NavLink :href="route('admin.categories.index')" :active="route().current('admin.categories.*')">
                                        Categories
                                    </NavLink>
                                    <NavLink :href="route('admin.reports.index')" :active="route().current('admin.reports.*')">
                                        Reports
                                    </NavLink>
                                </template>

                                <!-- Agent Links -->
                                <template v-if="isAgent">
                                    <NavLink :href="route('agent.profile')" :active="route().current('agent.profile')">
                                        Profile
                                    </NavLink>
                                </template>

                                <!-- Client Links -->
                                <template v-if="isClient">
                                    <NavLink :href="route('client.profile')" :active="route().current('client.profile')">
                                        Profile
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Notifications -->
                            <NotificationDropdown />

                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="profileRoute">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="logoutRoute" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="dashboardRoute" :active="route().current(dashboardRouteName)">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="ticketsRoute" :active="route().current(ticketsRouteName)">
                            Tickets
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="profileRoute">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="logoutRoute" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const page = usePage();

const userRole = computed(() => page.props.auth.role || 'client');
const isAdmin = computed(() => userRole.value === 'admin');
const isAgent = computed(() => userRole.value === 'agent');
const isClient = computed(() => userRole.value === 'client');

const dashboardRoute = computed(() => {
    if (isAdmin.value) return route('admin.dashboard');
    if (isAgent.value) return route('agent.dashboard');
    return route('client.dashboard');
});

const dashboardRouteName = computed(() => {
    if (isAdmin.value) return 'admin.dashboard';
    if (isAgent.value) return 'agent.dashboard';
    return 'client.dashboard';
});

const ticketsRoute = computed(() => {
    if (isAdmin.value) return route('admin.tickets.index');
    if (isAgent.value) return route('agent.tickets.index');
    return route('client.tickets.index');
});

const ticketsRouteName = computed(() => {
    if (isAdmin.value) return 'admin.tickets.*';
    if (isAgent.value) return 'agent.tickets.*';
    return 'client.tickets.*';
});

const profileRoute = computed(() => {
    if (isAdmin.value) return route('admin.profile');
    if (isAgent.value) return route('agent.profile');
    return route('client.profile');
});

const logoutRoute = computed(() => {
    if (isAdmin.value) return route('admin.logout');
    if (isAgent.value) return route('agent.logout');
    return route('client.logout');
});
</script>