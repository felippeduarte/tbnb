<template>
    <div>
        <div class="row m-1">
            <div class="col-6">
                <i class="bi bi-list-ul"></i> Stocks
            </div>
            <div class="col-6 text-right">
                <b-button class="btn btn-success" v-b-modal.modal-new-stock><i class="bi bi-plus"></i> Add new</b-button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item" v-for="stock in stocks" :key="stock.symbol">
                        <router-link :to="{ name: 'stockData', params: { symbol: stock.symbol } }">
                            {{stock.symbol}} <i class="bi bi-box-arrow-up-left"></i>
                        </router-link>
                        <span class="float-right">
                            <b-button variant="danger" @click="openDeleteStockModal(stock.symbol)"><i class="bi bi-trash"></i></b-button>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <b-modal id="modal-new-stock" title="New Stock">
            Symbol: <input type="text" style="text-transform: uppercase" class="form-control" v-model="form.symbol" />
            <span v-if="('symbol' in formErrors)" class="fa fa-warning form-control-feedback"></span>
            <ul><li v-for="(e, i) in formErrors['symbol']" :key="i" class="error text-danger">{{ e }}</li></ul>
            <template #modal-footer>
                <b-button
                    variant="success"
                    @click="newStock()"
                >
                    Save
                </b-button>
                <b-button
                    variant="secondary"
                    @click="$bvModal.hide('modal-new-stock')"
                >
                    Close
                </b-button>
            </template>
        </b-modal>
        <b-modal id="modal-delete-stock" title="Delete Stock">
            Are you sure you want to delete {{ symbolToDelete }}?
            <template #modal-footer>
                <b-button
                    variant="danger"
                    @click="deleteStock(symbolToDelete)"
                >
                    Yes, delete it!
                </b-button>
                <b-button
                    variant="secondary"
                    @click="$bvModal.hide('modal-delete-stock')"
                >
                    Cancel
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            symbolToDelete: '',
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
                    this.stocks.push(r.data);
                })
                .catch((err) => {
                    this.formErrors = err.response.data.errors;
                });
        },
        openDeleteStockModal(symbol) {
            this.symbolToDelete = symbol;
            this.$bvModal.show('modal-delete-stock');
        },
        deleteStock(symbol) {
            axios.delete('/api/stocks/'+symbol)
                .then(r => {
                    this.stocks = this.stocks.filter((s) => s.symbol != symbol);
                });
        },
        addToChart(symbol) {
            this.$root.$emit('addToChart', symbol);
        },
    }

}
</script>