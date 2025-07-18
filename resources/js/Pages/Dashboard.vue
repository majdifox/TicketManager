<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                        Welcome back!
                    </h2>
                    <p class="text-white/60 mt-1">Here's what's happening with your account today.</p>
                </div>
                <div class="hidden sm:flex items-center space-x-3">
                    <div class="flex items-center space-x-2 px-4 py-2 bg-green-500/20 rounded-xl border border-green-500/30">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-green-400 text-sm font-medium">Online</span>
                    </div>
                    <button class="p-2 bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-all duration-300 hover:scale-105">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Active Tickets -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300 hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/60 text-sm font-medium">Active Tickets</p>
                                <p class="text-3xl font-bold text-white mt-2">24</p>
                                <p class="text-green-400 text-sm mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17H7V7" />
                                    </svg>
                                    +12% from last week
                                </p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-xl">
                                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resolved Today -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-emerald-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300 hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/60 text-sm font-medium">Resolved Today</p>
                                <p class="text-3xl font-bold text-white mt-2">8</p>
                                <p class="text-green-400 text-sm mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Great progress!
                                </p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-xl">
                                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300 hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/60 text-sm font-medium">Avg Response</p>
                                <p class="text-3xl font-bold text-white mt-2">2.4h</p>
                                <p class="text-purple-400 text-sm mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Lightning fast
                                </p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-xl">
                                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Satisfaction -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-yellow-500/20 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300 hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-white/60 text-sm font-medium">Satisfaction</p>
                                <p class="text-3xl font-bold text-white mt-2">98%</p>
                                <p class="text-yellow-400 text-sm mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    Excellent rating
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-500/20 rounded-xl">
                                <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Recent Activity -->
                <div class="lg:col-span-2">
                    <div class="group relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-white">Recent Activity</h3>
                                <button class="text-blue-400 hover:text-blue-300 text-sm font-medium transition-colors duration-200">
                                    View All
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-start space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300 group/item">
                                    <div class="flex-shrink-0 w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium">Ticket #1234 resolved</p>
                                        <p class="text-white/60 text-sm">Issue with login fixed by Sarah Johnson</p>
                                        <p class="text-white/40 text-xs mt-1">2 minutes ago</p>
                                    </div>
                                    <div class="opacity-0 group-hover/item:opacity-100 transition-opacity duration-200">
                                        <button class="p-2 hover:bg-white/10 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300 group/item">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium">New ticket created</p>
                                        <p class="text-white/60 text-sm">Payment issue reported by John Doe</p>
                                        <p class="text-white/40 text-xs mt-1">15 minutes ago</p>
                                    </div>
                                    <div class="opacity-0 group-hover/item:opacity-100 transition-opacity duration-200">
                                        <button class="p-2 hover:bg-white/10 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-start space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300 group/item">
                                    <div class="flex-shrink-0 w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-medium">System update deployed</p>
                                        <p class="text-white/60 text-sm">Version 2.1.0 with bug fixes and improvements</p>
                                        <p class="text-white/40 text-xs mt-1">1 hour ago</p>
                                    </div>
                                    <div class="opacity-0 group-hover/item:opacity-100 transition-opacity duration-200">
                                        <button class="p-2 hover:bg-white/10 rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Status -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="group relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300">
                            <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button class="w-full flex items-center space-x-3 p-3 bg-blue-500/10 hover:bg-blue-500/20 rounded-xl border border-blue-500/20 hover:border-blue-500/30 transition-all duration-300 group/btn hover:scale-105">
                                    <div class="p-2 bg-blue-500/20 rounded-lg group-hover/btn:bg-blue-500/30 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </div>
                                    <span class="text-white font-medium">Create Ticket</span>
                                </button>

                                <button class="w-full flex items-center space-x-3 p-3 bg-green-500/10 hover:bg-green-500/20 rounded-xl border border-green-500/20 hover:border-green-500/30 transition-all duration-300 group/btn hover:scale-105">
                                    <div class="p-2 bg-green-500/20 rounded-lg group-hover/btn:bg-green-500/30 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <span class="text-white font-medium">View Reports</span>
                                </button>

                                <button class="w-full flex items-center space-x-3 p-3 bg-purple-500/10 hover:bg-purple-500/20 rounded-xl border border-purple-500/20 hover:border-purple-500/30 transition-all duration-300 group/btn hover:scale-105">
                                    <div class="p-2 bg-purple-500/20 rounded-lg group-hover/btn:bg-purple-500/30 transition-colors duration-300">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-white font-medium">Settings</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="group relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300">
                            <h3 class="text-lg font-bold text-white mb-4">System Status</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                        <span class="text-white/80">API Services</span>
                                    </div>
                                    <span class="text-green-400 text-sm font-medium">Operational</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                        <span class="text-white/80">Database</span>
                                    </div>
                                    <span class="text-green-400 text-sm font-medium">Healthy</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                                        <span class="text-white/80">Email Service</span>
                                    </div>
                                    <span class="text-yellow-400 text-sm font-medium">Maintenance</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                        <span class="text-white/80">File Storage</span>
                                    </div>
                                    <span class="text-green-400 text-sm font-medium">Online</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Performance Chart -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-white">Performance Overview</h3>
                            <select class="bg-white/5 border border-white/20 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 90 days</option>
                            </select>
                        </div>
                        
                        <!-- Simple Chart Placeholder -->
                        <div class="h-64 bg-gradient-to-b from-blue-500/10 to-purple-500/10 rounded-xl flex items-end justify-center space-x-2 p-4">
                            <div class="bg-blue-500/30 rounded-t-lg" style="height: 60%; width: 12%;"></div>
                            <div class="bg-blue-500/30 rounded-t-lg" style="height: 75%; width: 12%;"></div>
                            <div class="bg-blue-500/50 rounded-t-lg" style="height: 90%; width: 12%;"></div>
                            <div class="bg-blue-500/40 rounded-t-lg" style="height: 70%; width: 12%;"></div>
                            <div class="bg-blue-500/60 rounded-t-lg" style="height: 95%; width: 12%;"></div>
                            <div class="bg-blue-500/30 rounded-t-lg" style="height: 65%; width: 12%;"></div>
                            <div class="bg-blue-500/50 rounded-t-lg" style="height: 80%; width: 12%;"></div>
                        </div>
                        
                        <div class="mt-4 flex items-center justify-center space-x-6 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-white/70">Tickets Resolved</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                <span class="text-white/70">Response Time</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Performance -->
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-pink-500/10 to-rose-500/10 rounded-2xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative bg-black/20 backdrop-blur-xl rounded-2xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300">
                        <h3 class="text-xl font-bold text-white mb-6">Top Performers</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center font-bold text-white">
                                    1
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-medium">Sarah Johnson</p>
                                    <p class="text-white/60 text-sm">24 tickets resolved</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-yellow-400 font-bold">4.9★</p>
                                    <p class="text-white/60 text-sm">Rating</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-r from-gray-400 to-gray-600 rounded-xl flex items-center justify-center font-bold text-white">
                                    2
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-medium">Mike Chen</p>
                                    <p class="text-white/60 text-sm">19 tickets resolved</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-yellow-400 font-bold">4.8★</p>
                                    <p class="text-white/60 text-sm">Rating</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all duration-300">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-amber-600 rounded-xl flex items-center justify-center font-bold text-white">
                                    3
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-medium">Emily Rodriguez</p>
                                    <p class="text-white/60 text-sm">17 tickets resolved</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-yellow-400 font-bold">4.7★</p>
                                    <p class="text-white/60 text-sm">Rating</p>
                                </div>
                            </div>
                        </div>

                        <button class="w-full mt-4 py-3 bg-gradient-to-r from-pink-500/20 to-rose-500/20 hover:from-pink-500/30 hover:to-rose-500/30 rounded-xl border border-pink-500/20 hover:border-pink-500/30 text-white font-medium transition-all duration-300 hover:scale-105">
                            View Full Leaderboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<style scoped>
/* Additional custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out forwards;
}

.animate-slideInLeft {
    animation: slideInLeft 0.8s ease-out forwards;
}

/* Staggered animation delays */
.grid > div:nth-child(1) { animation-delay: 0.1s; }
.grid > div:nth-child(2) { animation-delay: 0.2s; }
.grid > div:nth-child(3) { animation-delay: 0.3s; }
.grid > div:nth-child(4) { animation-delay: 0.4s; }

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Glassmorphism effect enhancement */
.backdrop-blur-xl {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

/* Custom gradient text */
.gradient-text {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6, #06b6d4);
    background-size: 200% 200%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 3s ease infinite;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}
</style>