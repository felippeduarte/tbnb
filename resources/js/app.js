require('./bootstrap');
import { BootstrapVue } from 'bootstrap-vue'
import VueRouter from 'vue-router'
import routes from './routes'

window.Vue = require('vue').default;

Vue.use(VueRouter);
Vue.use(BootstrapVue);

Vue.component('home', require('./components/HomeComponent.vue').default);
Vue.component('navbar', require('./components/NavbarComponent.vue').default);
Vue.component('stocks-list', require('./components/StocksListComponent.vue').default);
Vue.component('stock-chart', require('./components/StockChartComponent.vue').default);

const router = new VueRouter({
    routes
});

axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response.status === 404) {
        return router.push({ name: '404' });
    } else {
        return Promise.reject(error);
    }
});

const app = new Vue({
    router
}).$mount('#app');
