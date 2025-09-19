import './bootstrap';
import { createApp } from 'vue';

// Import Vue components
import HomeComponent from './components/HomeComponent.vue';

// Create Vue app
const app = createApp({});

// Register components
app.component('home-component', HomeComponent);

// Mount Vue app
app.mount('#app');