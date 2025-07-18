<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Date Range Filter -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Filter by Date Range</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input
                                    type="date"
                                    v-model="dateRange.start"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                <input
                                    type="date"
                                    v-model="dateRange.end"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                />
                            </div>
                            <div class="flex items-end">
                                <button
                                    @click="generateReport"
                                    class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                >
                                    Generate Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-2xl font-bold text-gray-900">{{ stats.total_tickets || 0 }}</div>
                            <div class="text-sm text-gray-500">Total Tickets</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-2xl font-bold text-green-600">{{ stats.resolved_tickets || 0 }}</div>
                            <div class="text-sm text-gray-500">Resolved Tickets</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_tickets || 0 }}</div>
                            <div class="text-sm text-gray-500">Pending Tickets</div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-2xl font-bold text-blue-600">{{ stats.avg_resolution_time || '0h' }}</div>
                            <div class="text-sm text-gray-500">Avg. Resolution Time</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Tickets by Status -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tickets by Status</h3>
                            <div class="space-y-2">
                                <div v-for="(count, status) in stats.by_status" :key="status" class="flex items-center">
                                    <span class="w-24 text-sm text-gray-600">{{ status }}</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-4 ml-2">
                                        <div 
                                            :class="[
                                                'h-4 rounded-full',
                                                status === 'Open' && 'bg-blue-500',
                                                status === 'In Progress' && 'bg-yellow-500',
                                                status === 'Resolved' && 'bg-green-500',
                                                status === 'Closed' && 'bg-gray-500',
                                            ]"
                                            :style="`width: ${(count / stats.total_tickets * 100) || 0}%`"
                                        ></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets by Priority -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tickets by Priority</h3>
                            <div class="space-y-2">
                                <div v-for="(count, priority) in stats.by_priority" :key="priority" class="flex items-center">
                                    <span class="w-24 text-sm text-gray-600">{{ priority }}</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-4 ml-2">
                                        <div 
                                            :class="[
                                                'h-4 rounded-full',
                                                priority === 'Low' && 'bg-gray-500',
                                                priority === 'Medium' && 'bg-yellow-500',
                                                priority === 'High' && 'bg-orange-500',
                                                priority === 'Critical' && 'bg-red-500',
                                            ]"
                                            :style="`width: ${(count / stats.total_tickets * 100) || 0}%`"
                                        ></div>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Categories -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Top Categories</h3>
                            <div class="space-y-2">
                                <div v-for="category in stats.top_categories" :key="category.name" class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ category.name }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ category.count }} tickets</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agent Performance -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Agent Performance</h3>
                            <div class="space-y-2">
                                <div v-for="agent in stats.agent_performance" :key="agent.name" class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ agent.name }}</span>
                                    <span class="text-sm font-medium text-gray-900">{{ agent.resolved }} resolved</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Export Report</h3>
                        <div class="flex space-x-4">
                            <button
                                @click="exportReport('pdf')"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                            >
                                Export as PDF
                            </button>
                            <button
                                @click="exportReport('csv')"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                            >
                                Export as CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_tickets: 0,
            resolved_tickets: 0,
            pending_tickets: 0,
            avg_resolution_time: '0h',
            by_status: {},
            by_priority: {},
            top_categories: [],
            agent_performance: [],
        })
    },
});

const dateRange = reactive({
    start: new Date(new Date().setMonth(new Date().getMonth() - 1)).toISOString().split('T')[0],
    end: new Date().toISOString().split('T')[0],
});

const generateReport = () => {
    router.get(route('admin.reports.index'), dateRange, {
        preserveState: true,
        preserveScroll: true,
    });
};

const exportReport = async (format) => {
    try {
        // Show loading state (optional)
        const loadingToast = document.createElement('div');
        loadingToast.textContent = `Generating ${format.toUpperCase()} report...`;
        loadingToast.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded shadow-lg z-50';
        document.body.appendChild(loadingToast);

        // Build the export URL with parameters
        const params = new URLSearchParams({
            start: dateRange.start,
            end: dateRange.end,
            format: format
        });
        
        // Create the export URL
        const exportUrl = route('admin.reports.export') + '?' + params.toString();
        
        // Use fetch to handle the request properly
        const response = await fetch(exportUrl, {
            method: 'GET',
            headers: {
                'Accept': format === 'pdf' ? 'application/pdf' : 'text/csv',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        // Remove loading toast
        document.body.removeChild(loadingToast);

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || 'Export failed');
        }

        // Create blob and download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `tickets-report-${dateRange.start}-to-${dateRange.end}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        // Show success message
        const successToast = document.createElement('div');
        successToast.textContent = `${format.toUpperCase()} report downloaded successfully!`;
        successToast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
        document.body.appendChild(successToast);
        setTimeout(() => {
            if (document.body.contains(successToast)) {
                document.body.removeChild(successToast);
            }
        }, 3000);

    } catch (error) {
        console.error('Export error:', error);
        
        // Show error message
        const errorToast = document.createElement('div');
        errorToast.textContent = `Export failed: ${error.message}`;
        errorToast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
        document.body.appendChild(errorToast);
        setTimeout(() => {
            if (document.body.contains(errorToast)) {
                document.body.removeChild(errorToast);
            }
        }, 5000);
    }
};
</script>