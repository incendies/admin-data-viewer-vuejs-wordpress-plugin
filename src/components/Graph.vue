<template>
    <div>
        <h1>Graph Data</h1>
        <BarChart :data="chartData" :options="chartOptions" />
        <p v-if="statusMessage">{{ statusMessage }}</p>
    </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, BarElement, CategoryScale, LinearScale);

export default {
    name: "Graph",
    components: {
        BarChart: Bar
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
            apiData: {
                root_url: typeof yunusPluginData !== 'undefined' ? yunusPluginData.root_url : 'http://localhost:5173',
                nonce: typeof yunusPluginData !== 'undefined' ? yunusPluginData.nonce : 'development_nonce'
            }
        };
    },
    methods: {
        async fetchGraphData() {
            try {
                const apiUrl = `${this.apiData.root_url}/api/data-endpoint`; // Update this line to point to your actual API endpoint
                const headers = this.apiData.nonce !== 'development_nonce' ? { 'X-WP-Nonce': this.apiData.nonce } : {};

                const response = await fetch(apiUrl, { headers });

                // Ensure the response is JSON
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Received non-JSON response");
                }

                const result = await response.json();
                console.log("Graph data:", result);  // Debugging log

                // Update this section based on the actual structure of result
                if (result.data) { // Adjust this based on your actual API response structure
                    this.chartData = {
                        labels: result.data.map(item => item.label), // Adjust according to your data structure
                        datasets: [
                            {
                                label: 'Sample Data',
                                backgroundColor: '#42b983',
                                data: result.data.map(item => item.value) // Adjust according to your data structure
                            }
                        ]
                    };
                } else {
                    console.error("Unexpected graph data format:", result);
                    this.statusMessage = "Unexpected data format for graph data.";
                }
            } catch (error) {
                console.error("Error fetching graph data:", error);
                this.statusMessage = "Error fetching graph data.";
            }
        }
    },
    mounted() {
        this.fetchGraphData();
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