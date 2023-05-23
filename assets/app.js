import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { cloneDeep } from 'lodash';
import { ElLoading } from 'element-plus';

import App from '~/App.vue';
import router from '~/router/index.js';
import '~/styles/index.scss';
import '~/styles/transitions.css';

const pinia = createPinia();

pinia.use(({ store }) => {
  const initialState = cloneDeep(store.$state);
  store.$reset = () => store.$patch(cloneDeep(initialState));
});

const app = createApp(App);

app.use(pinia);
app.use(router);
app.use(ElLoading);

app.mount('#app');

app.config.warnHandler = (message, instance, trace) => {
  const rules = [
    'Component inside <Transition> renders non-element root node that cannot be animated.',
  ];
  if (!rules.includes(message)) {
    console.warn('[Vue warn]: ' + message + '\n' + trace);
  }
};
