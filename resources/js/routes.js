import StockData from './components/views/StockData.vue'
import NotFound from './components/views/404.vue'

const routes = [
    { name:'stockData', path: '/stockData/:symbol', component: StockData },
    { name: '404', path:'/404', component: NotFound },
];

export default routes