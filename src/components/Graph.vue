<template>
    <div>
        <h1>Graph Data</h1>
        <BarChart v-if="isVisible" :data="chartData" :options="chartOptions" />
        <p v-if="statusMessage">{{ statusMessage }}</p>
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import { Chart as ChartJS, Title, Tooltip, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, BarElement, CategoryScale, LinearScale);

export default {
    name: "Graph",
    components: {
        // Lazy load BarChart component
        BarChart: defineAsyncComponent(() => import('vue-chartjs').then(module => module.Bar))
    },
    data() {
        return {
            chartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Sample Data',
                        backgroundColor: '#42b983',
                        data: []
                    }
                ]
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false
            },
            statusMessage: "",
            isVisible: false, // Control visibility to delay rendering
            apiData: {
                root_url: typeof yunusPluginData !== 'undefined' ? yunusPluginData.root_url : 'http://localhost:5173',
                nonce: typeof yunusPluginData !== 'undefined' ? yunusPluginData.nonce : 'development_nonce'
            }
        };
    },
    methods: {
        async fetchGraphData() {
            try {
                const apiUrl = 'https://miusage.com/v1/challenge/2/static/';
                const response = await fetch(apiUrl);
                
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Received non-JSON response");
                }

                const result = await response.json();
                console.log("Graph data:", result);  // Debugging log

                // Use only the top 10 data points, adjust as necessary
                const rows = result.table.data.rows.slice(0, 10);
                this.chartData = {
                    labels: rows.map(item => item.title),
                    datasets: [
                        {
                            label: 'Pageviews',
                            backgroundColor: '#42b983',
                            data: rows.map(item => item.pageviews)
                        }
                    ]
                };
            } catch (error) {
                console.error("Error fetching graph data:", error);
                this.statusMessage = "Error fetching graph data.";
            }
        },
        handleVisibility() {
            if (!this.isVisible) {
                this.isVisible = true;
                this.fetchGraphData();
            }
        }
    },
    mounted() {
        // Load data only when the component becomes visible
        this.handleVisibility();
    }
};
</script>

<style scoped>
h1 {
    color: #42b983;
}

p {
    margin-top: 10px;
    color: #42b983;
}
</style>
