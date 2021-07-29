require('./bootstrap');
import { BootstrapVue } from 'bootstrap-vue'

window.Vue = require('vue').default;

Vue.use(BootstrapVue);

Vue.component('home', require('./components/HomeComponent.vue').default);
Vue.component('navbar', require('./components/NavbarComponent.vue').default);
Vue.component('stocks-list', require('./components/StocksListComponent.vue').default);

const app = new Vue({
    el: '#app',
});
