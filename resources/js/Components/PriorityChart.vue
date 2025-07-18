<template>
    <div class="w-full">
        <div v-if="chartData && chartData.length > 0">
            <canvas ref="chartCanvas" width="400" height="200"></canvas>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
            No data available
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
    data: {
        type: Array,
        default: () => []
    }
});

const chartCanvas = ref(null);
let chartInstance = null;

const chartData = ref(props.data);

const priorityColors = {
    'Low': '#10B981',      // Green
    'Medium': '#F59E0B',   // Amber
    'High': '#F97316',     // Orange
    'Critical': '#EF4444'  // Red
};

const createChart = () => {
    if (!chartCanvas.value || !chartData.value.length) return;

    const ctx = chartCanvas.value.getContext('2d');
    
    // Destroy existing chart if it exists
    if (chartInstance) {
        chartInstance.destroy();
    }

    const labels = chartData.value.map(item => item.priority);
    const data = chartData.value.map(item => item.count);
    const colors = chartData.value.map(item => priorityColors[item.priority] || '#6B7280');

    chartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderColor: colors.map(color => color + '80'),
                borderWidth: 1,
                hoverBackgroundColor: colors,
                hoverBorderColor: colors,
                hoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            elements: {
                arc: {
                    borderWidth: 2
                }
            }
        }
    });
};

onMounted(() => {
    createChart();
});

watch(() => props.data, (newData) => {
    chartData.value = newData;
    createChart();
}, { deep: true });
</script>

<style scoped>
canvas {
    max-height: 300px;
}
</style>