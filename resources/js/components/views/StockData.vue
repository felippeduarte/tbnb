<template>
    <div>
        <stock-chart ref="stockChart" v-if="chartData" :label="symbol" :chartData="chartData" :options="chartOptions"></stock-chart>
    </div>
</template>

<script>
import StockChart from '../StockChartComponent.vue';
export default {
    components: { StockChart },
    data() {
        return {
            symbol: '',
            chartSymbols: [],
            chartData: {
                labels: [],
                datasets: [],
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    onClick:  this.clickChartHandler
                }
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
        '$route' (to) {
            this.symbol = to.params.symbol;
            this.addToChart(this.symbol);
        }
    },
    beforeMount() {
        this.symbol = this.$route.params.symbol;
        this.$root.$on("addToChart", symbol => {
            this.addToChart(symbol);
        });
        this.$root.$on("newQuoteAdded", symbol => {
            if (this.chartSymbols.includes(symbol)) {
                this.removeFromChart(symbol);
                this.addToChart(symbol);
            }
        });
        this.addToChart(this.symbol);
    },
    methods: {
        addToChart(symbol) {
            //if symbol already exists in chart, don't add again
            if(this.chartData.datasets.find(d => d.label === symbol)) {
                return;
            }
            axios.get('/api/quotes?symbol='+symbol+'&limit='+this.limit)
                .then(r => {
                    //reverse resultset to append to chart
                    r.data = r.data.reverse();
                    var datasets = [];

                    //populate dataset with existing info
                    if(this.chartData.datasets) {
                        datasets = this.chartData.datasets;
                    }

                    //map data to chartjs format
                    var dates = r.data.map((d) => d.date);
                    var quotes = r.data.map((d) => d.quote);

                    //create new dataset from API data
                    var dataset = {
                        fill: false,
                        label: symbol,
                        data: quotes,
                        borderColor: this.availableChartColors[datasets.length],
                    };

                    //add new dataset
                    datasets.push(dataset);

                    //set chart prop data
                    this.chartData = {
                        labels: dates,
                        datasets: datasets,
                    };

                    this.chartSymbols.push(symbol);
                });
        },
        removeFromChart(symbol) {
            this.chartData = {
                labels: this.chartData.labels,
                datasets: this.chartData.datasets.filter((d) => d.label != symbol)
            };
            this.chartSymbols = this.chartSymbols.filter((d) => d != symbol);
        },
        clickChartHandler(event,legend) {
            this.removeFromChart(legend.text);
        }
    }

}
</script>