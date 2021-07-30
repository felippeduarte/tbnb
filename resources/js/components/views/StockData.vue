<template>
    <div>
        <stock-chart v-if="chartData" :label="symbol" :chartData="chartData" :options="chartOptions"></stock-chart>
    </div>
</template>

<script>
import StockChart from '../StockChartComponent.vue';
export default {
    components: { StockChart },
    data() {
        return {
            symbol: '',
            chartData: {},
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
            },
            limit: 30,
            availableChartColors: [
                '#ff7f50', '#87cefa', '#da70d6', '#32cd32', '#6495ed',
                '#ff69b4', '#ba55d3', '#cd5c5c', '#ffa500', '#40e0d0',
                '#1e90ff', '#ff6347', '#7b68ee', '#00fa9a', '#ffd700',
                '#6b8e23', '#ff00ff', '#3cb371', '#b8860b', '#30e0e0'
            ],
        }
    },
    watch: {
        '$route' (to, from) {
            this.symbol = to.params.symbol;
            this.addToChart(this.symbol);
        }
    },
    beforeMount() {
        this.symbol = this.$route.params.symbol;
        this.$root.$on("addToChart", symbol => {
            this.addToChart(symbol);
        });
        this.addToChart(this.symbol);
    },
    methods: {
        addToChart(symbol) {
            //if symbol already exists in chart, don't add again
            if(this.chartData.datasets) {
                if(this.chartData.datasets.find(d => d.label === symbol)) {
                    return;
                }
            }
            axios.get('/api/quotes?symbol='+symbol+'&limit='+this.limit)
                .then(r => {
                    var datasets = [];

                    //populate dataset with existing info
                    if(this.chartData.datasets) {
                        datasets = this.chartData.datasets;
                    }

                    //map data to chartjs format
                    var dates = r.data.map((d) => d.date);
                    var quotes = r.data.map(function(d) {
                        return d.quote;
                    });

                    //create new dataset from API data
                    var dataset = {
                        fill: false,
                        label: symbol,
                        data: quotes,
                        borderColor: this.availableChartColors[datasets.length],
                    };

                    //merge X axis
                    if(this.chartData.labels) {
                        dates = [...new Set([...this.chartData.labels ,...dates])];
                    }

                    //add new dataset
                    datasets.push(dataset);

                    //set chart prop data
                    this.chartData = {
                        labels: dates,
                        datasets: datasets,
                    };
                });
        },
        removeFromChart(symbol) {
            debuger;
        }
    }

}
</script>