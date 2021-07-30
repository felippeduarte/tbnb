import StockData from './components/views/StockData.vue'
import SimulatedPrice from './components/views/SimulatedPrice.vue'
import UpdateQuotes from './components/views/UpdateQuotes.vue'
import NotFound from './components/views/404.vue'

const routes = [
    { name:'stockData', path: '/stockData/:symbol', component: StockData },
    { name:'simulatedPrice', path: '/simulatedPrice', component: SimulatedPrice },
    { name:'updateQuotes', path: '/updateQuotes', component: UpdateQuotes },
    
    { name: '404', path:'/404', component: NotFound },
];

export default routes