<template>
    <div>
        <h1>Table Data</h1>
        <table v-if="tableData.length">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Title</th>
                    <th>Pageviews</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, index) in tableData" :key="index">
                    <td>{{ item.id }}</td>
                    <td>{{ item.url }}</td>
                    <td>{{ item.title }}</td>
                    <td>{{ item.pageviews }}</td>
                    <td>{{ formatDate(item.date) }}</td>
                </tr>
            </tbody>
        </table>
        <p v-else>Loading data...</p>
    </div>
</template>

<script>
export default {
    name: "Table",
    data() {
        return {
            tableData: [] // Data to store API response
        };
    },
    methods: {
        // Fetch the table data from the external API
        async fetchTableData() {
            try {
                const apiUrl = 'https://miusage.com/v1/challenge/2/static/';
                
                // Fetch data from the external API
                const response = await fetch(apiUrl);
                
                // Ensure the response is JSON
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Non-JSON response detected:", text);
                    throw new Error("Received non-JSON response");
                }

                // Parse JSON response
                const result = await response.json();
                console.log("Fetched Data:", result); // Log the result to understand the structure

                // Access the table data array
                if (result.table && result.table.data && Array.isArray(result.table.data.rows)) {
                    this.tableData = result.table.data.rows;
                } else {
                    console.error("Unexpected data format:", result);
                }
            } catch (error) {
                console.error("Error fetching table data:", error);
            }
        },
        // Format the timestamp into a readable date
        formatDate(timestamp) {
            const date = new Date(timestamp * 1000); // Assuming the date is in seconds
            return date.toLocaleDateString(); // Customize the format if needed
        }
    },
    mounted() {
        this.fetchTableData();
    }
};
</script>

<style scoped>
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

th,
td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}

h1 {
    color: #42b983;
}
</style>
