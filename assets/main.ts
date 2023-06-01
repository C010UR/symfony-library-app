import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { cloneDeep } from 'lodash';
import { ElLoading } from 'element-plus';

import App from '@/App.vue';
import router from '@/router/index.js';
import '@/styles/index.scss';
import '@/styles/transitions.css';

const pinia = createPinia();

// Update $reset to work with Function syntax
pinia.use(({ store }) => {
  const initialState = cloneDeep(store.$state);
  store.$reset = () => store.$patch(cloneDeep(initialState));
});

const app = createApp(App);

// Vue store
app.use(pinia);
// Vue router
app.use(router);
// Loading animation
app.use(ElLoading);

app.mount('#app');
