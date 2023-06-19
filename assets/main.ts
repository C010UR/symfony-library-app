import App from '@/App.vue';
import router from '@/router/index.js';
import '@/styles/index.scss';
import '@/styles/transitions.css';
import { ElLoading } from 'element-plus';
import { cloneDeep } from 'lodash';
import { createPinia } from 'pinia';
import { createApp } from 'vue';

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
// Element plus loading directive
app.use(ElLoading);

app.mount('#app');
