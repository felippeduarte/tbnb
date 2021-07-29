<template>
    <div>
        Stocks <b-button class="btn btn-success text-right" v-b-modal.modal-new-stock><i class="bi bi-plus"></i> Add new</b-button>
        <ul class="list-group">
            <li class="list-group-item" v-for="stock in stocks" :key="stock.symbol">
                {{stock.symbol}} <a href="#" v-on:click="deleteStock(stock.symbol)"><i class="bi bi-trash"></i></a>
            </li>
        </ul>
        <b-modal id="modal-new-stock" title="New Stock">
            Symbol: <input type="text" style="text-transform: uppercase" class="form-control" v-model="form.symbol" />
            <span v-if="('symbol' in formErrors)" class="fa fa-warning form-control-feedback"></span>
            <ul><li v-for="(e, i) in formErrors['symbol']" :key="i" class="error text-danger">{{ e }}</li></ul>
            <template #modal-footer>
                <b-button
                    variant="success"
                    class="float-right"
                    @click="newStock()"
                >
                    Save
                </b-button>
                <b-button
                    variant="secondary"
                    class="float-right"
                    @click="show=false"
                >
                    Close
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            stocks: [],
            formErrors: [],
            form: {
                symbol: '',
            }
        }
    },
    mounted() {
        this.getStocks();
    },
    methods: {
        getStocks() {
            axios.get('/api/stocks')
                .then(r => {
                    this.stocks = r.data;
                });
        },
        newStock() {
            axios.post('/api/stocks', this.form)
                .then((r) => {
                    this.$bvModal.hide("modal-new-stock");
                    this.$root.$emit('addAlertSuccess', 'Stock saved!');
                })
                .catch((err) => {
                    this.formErrors = err.response.data.errors;
                });
        },
        deleteStock(symbol) {
            axios.delete('/api/stocks/'+symbol)
                .then(r => {
                    this.stocks = this.stocks.filter((s) => s.symbol != symbol);
                });
        },
    }

}
</script>