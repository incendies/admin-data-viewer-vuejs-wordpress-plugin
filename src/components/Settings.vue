<template>
    <div>
        <h1>Settings</h1>
        <form @submit.prevent="saveSettings">
            <div class="form-group">
                <label for="numRows">Number of Rows:</label>
                <input type="number" id="numRows" v-model="settings.numRows" min="1" max="10" />
            </div>

            <div class="form-group">
                <label for="dateFormat">Date Format:</label>
                <select id="dateFormat" v-model="settings.dateFormat">
                    <option value="human">Human-readable</option>
                    <option value="timestamp">Unix Timestamp</option>
                </select>
            </div>

            <button type="submit">Save Settings</button>
            <p v-if="statusMessage">{{ statusMessage }}</p>
        </form>
    </div>
</template>

<script>
export default {
    name: "Settings",
    data() {
        return {
            settings: {
                numRows: 5,
                dateFormat: 'human'
            },
            statusMessage: "",
            apiData: {
                root_url: typeof yunusPluginData !== 'undefined' ? yunusPluginData.root_url.replace(/\/?$/, '/') : 'http://localhost:5173/',
                nonce: typeof yunusPluginData !== 'undefined' ? yunusPluginData.nonce : 'development_nonce'
            }
        };
    },
    methods: {
        async loadSettings() {
            if (this.apiData.root_url === 'http://localhost:5173/') {
                console.log("Mock loading settings locally");
                this.settings.numRows = 5;
                this.settings.dateFormat = 'human';
                this.statusMessage = "Settings loaded (Mock)";
                return;
            }

            try {
                const apiUrl = `${this.apiData.root_url}wp-json/yunus/v1/get-settings`;
                const headers = { 'X-WP-Nonce': this.apiData.nonce };

                console.log(`API URL (loadSettings): ${apiUrl}`);

                const response = await fetch(apiUrl, { headers });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Non-JSON response:", text);
                    throw new Error("Expected JSON response, received HTML or other format.");
                }

                const result = await response.json();

                if (result) {
                    this.settings.numRows = result.numRows || 5;
                    this.settings.dateFormat = result.dateFormat || 'human';
                }
            } catch (error) {
                console.error("Error loading settings:", error);
                this.statusMessage = "Error loading settings. Please check console for details.";
            }
        },
        async saveSettings() {
            if (this.apiData.root_url === 'http://localhost:5173/') {
                console.log("Mock saving settings locally:", this.settings);
                this.statusMessage = "Settings saved successfully! (Mock)";
                return;
            }

            try {
                const apiUrl = `${this.apiData.root_url}wp-json/yunus/v1/update-setting`;
                const headers = {
                    'X-WP-Nonce': this.apiData.nonce,
                    'Content-Type': 'application/json'
                };

                console.log("API URL (saveSettings):", apiUrl);

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        setting_key: 'settings',
                        setting_value: this.settings
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    const text = await response.text();
                    console.error("Non-JSON response:", text);
                    throw new Error("Expected JSON response, received HTML or other format.");
                }

                const result = await response.json();
                this.statusMessage = result.success ? "Settings saved successfully!" : "Failed to save settings.";
            } catch (error) {
                console.error("Error saving settings:", error);
                this.statusMessage = "Error saving settings. Please check console for details.";
            }
        }
    },
    mounted() {
        this.loadSettings();
    }
};
</script>

<style scoped>
.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input, select {
    padding: 5px;
    width: 100%;
    max-width: 200px;
}

button {
    padding: 8px 12px;
    background-color: #42b983;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #369b72;
}

p {
    margin-top: 10px;
    color: #42b983;
}
</style>
