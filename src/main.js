import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// Create app instance
const app = createApp(App);

// Use router before mounting the app
app.use(router);

// Mount the app
app.mount('#yunus-vue-app');

// Define default yunusPluginData for local development
if (typeof yunusPluginData === 'undefined') {
    window.yunusPluginData = {
        root_url: 'http://localhost:5173',  // Adjust this URL for local API endpoint
        nonce: 'development_nonce'
    };
}
