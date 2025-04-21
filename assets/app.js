import './bootstrap.js';
import './styles/tailwind.css';
import './styles/app.css';
import { createApp } from 'vue/dist/vue.esm-bundler.js';
import App from './components/App.vue';

/*
 * Initialize Vue application
 */
createApp(App).mount('#app');
