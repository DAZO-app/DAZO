// resources/js/app.js
import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';

import VueSocialSharing from 'vue-social-sharing';
import { createHead } from '@unhead/vue/client';

const app = createApp(App);
const pinia = createPinia();
const head = createHead();

app.use(pinia);
app.use(router);
app.use(VueSocialSharing);
app.use(head);

app.mount('#app');
