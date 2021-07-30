<template>
    <div>
        <div class="row m-1">
            <div class="col-6">
                <i class="bi bi-list-ul"></i> Stocks
            </div>
            <div class="col-6 text-right">
                <b-button class="btn btn-success" @click="openNewStockModal()"><i class="bi bi-plus"></i> Add new</b-button>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item" v-for="stock in stocks" :key="stock.symbol">
                        <router-link :to="{ name: 'stockData', params: { symbol: stock.symbol } }">
                            {{stock.symbol}}
                        </router-link>
                        <span class="float-right">
                            <b-button variant="danger" @click="openDeleteStockModal(stock.symbol)"><i class="bi bi-trash"></i></b-button>
                        </span>
                        <span class="float-right mr-2">
                            <b-button variant="info" @click="openUpdateQuoteModal(stock.symbol)"><i class="bi bi-currency-dollar"></i></b-button>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <b-modal id="modal-new-stock" title="New Stock">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Symbol</label>
                <div class="col-sm-10">
                    <input type="text" style="text-transform: uppercase" class="form-control" v-model="formNew.symbol" />
                    <ul v-if="('symbol' in formErrors)"><li v-for="(e, i) in formErrors['symbol']" :key="i" class="error text-danger">{{ e }}</li></ul>
                </div>
            </div>
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
            Are you sure you want to delete {{ activeSymbol }}?
            <template #modal-footer>
                <b-button
                    variant="danger"
                    @click="deleteStock(activeSymbol)"
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
        <b-modal id="modal-update-quote" title="Update Quote">
            <div class="form-group row">
                <label class="col-sm-12 col-form-label">Symbol {{ activeSymbol }}</label>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Date</label>
                <div class="col-sm-10">
                    <b-form-datepicker v-model="formQuote.date" :max="maxQuoteDate" class="mb-2"></b-form-datepicker>
                    <ul v-if="('date' in formErrors)"><li v-for="(e, i) in formErrors['date']" :key="i" class="error text-danger">{{ e }}</li></ul>
                </div>
                <label class="col-sm-2 col-form-label">Quote</label>
                <div class="col-sm-10">
                    <b-form-input v-model="formQuote.quote" type="number"></b-form-input>
                    <ul v-if="('quote' in formErrors)"><li v-for="(e, i) in formErrors['quote']" :key="i" class="error text-danger">{{ e }}</li></ul>
                </div>
            </div>

            <template #modal-footer>
                <b-button
                    variant="success"
                    @click="updateQuote()"
                >
                    Save
                </b-button>
                <b-button
                    variant="secondary"
                    @click="$bvModal.hide('modal-update-quote')"
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
            stockUrl: '/api/stocks/',
            quoteUrl: '/api/quotes/',
            activeSymbol: '',
            stocks: [],
            formErrors: [],
            formNew: {
                symbol: '',
            },
            formQuote: {
                symbol: '',
                date: '',
                quote: '',
            },
            maxQuoteDate: new Date(),
        }
    },
    mounted() {
        this.getStocks();
    },
    methods: {
        getStocks() {
            axios.get(this.stockUrl)
                .then(r => {
                    this.stocks = r.data;
                });
        },
        newStock() {
            axios.post(this.stockUrl, this.formNew)
                .then((r) => {
                    this.$bvModal.hide("modal-new-stock");
                    this.$root.$emit('addAlertSuccess', 'Stock saved!');
                    this.stocks.push(r.data);
                })
                .catch((err) => {
                    this.formErrors = err.response.data.errors;
                });
        },
        openNewStockModal() {
            this.formErrors = {};
            this.formNew.symbol = '';
            this.$bvModal.show('modal-new-stock');
        },
        openDeleteStockModal(symbol) {
            this.activeSymbol = symbol;
            this.$bvModal.show('modal-delete-stock');
        },
        openUpdateQuoteModal(symbol) {
            this.activeSymbol = symbol;
            this.$bvModal.show('modal-update-quote');
        },
        deleteStock(symbol) {
            axios.delete(this.stockUrl+symbol)
                .then(r => {
                    this.$bvModal.hide("modal-delete-stock");
                    this.$root.$emit('addAlertSuccess', 'Stock deleted!');
                    this.stocks = this.stocks.filter((s) => s.symbol != symbol);
                });
        },
        updateQuote() {
            var formQuote = {
                date: this.formQuote.date,
                stocks: [{
                    symbol: this.activeSymbol,
                    quote: this.formQuote.quote,
                }]
            }
            axios.post(this.quoteUrl, formQuote)
                .then((r) => {
                    this.$bvModal.hide('modal-update-quote');
                    this.$root.$emit('addAlertSuccess', 'Quote updated!');
                    this.$root.$emit('newQuoteAdded', this.activeSymbol);
                })
                .catch((err) => {
                    this.formErrors = err.response.data.errors;
                });
        },
        addToChart(symbol) {
            this.$root.$emit('addToChart', symbol);
        },
    }

}
</script>