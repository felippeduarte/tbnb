<template>
    <div>
        <div class="row">
            <div class="col-12">
                <h2>Update Quotes</h2>
                <p>Insert into the textbox with the pattern SYMBOL:PRICE.</p>
                <p>Example:<br/>ABCD:12.34<br/>XPTO:43.21</p>
                <p>One PRICE per line</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="col-sm-2 col-form-label">Date</label>
                <div class="col-sm-6">
                    <b-form-datepicker v-model="formDate" :max="maxQuoteDate"></b-form-datepicker>
                    <ul v-if="('date' in formErrors)"><li v-for="(e, i) in formErrors['date']" :key="i" class="error text-danger">{{ e }}</li></ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="col-sm-2 col-form-label">Quotes</label>
                <div class="col-sm-6">
                    <b-form-textarea
                        v-model="quotes"
                        rows="10"
                    ></b-form-textarea>
                    <ul v-if="('quote' in formErrors)"><li v-for="(e, i) in formErrors['quote']" :key="i" class="error text-danger">{{ e.join(', ') }}</li></ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-2">
                <b-button variant="success" @click="updateQuotes()">Save</b-button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            quotes: '',
            formErrors: [],
            formDate: '',
            maxQuoteDate: new Date(),
        }
    },
    methods: {
        updateQuotes() {
            try {
                var stocks = this.quotes.split("\n");
                stocks = stocks.map((s) => {
                    var d = s.split(':');
                    return {
                        "symbol":d[0],
                        "quote":d[1]
                    };
                });

                var data = {
                    date : this.formDate,
                    stocks : stocks,
                };

                axios.post('/api/quotes/', data)
                    .then((r) => {
                        this.$root.$emit('addAlertSuccess', 'Quotes Updated successfully!');
                        this.formErrors = [];
                    })
                    .catch((err) => {
                        var errors = err.response.data.errors;
                        var stockErrors = Object.keys(errors).filter(function(key) {
                            return /^stocks\..*/.test(key);
                        });
                        var stockErrors = stockErrors.map((e) => {
                            return errors[e];
                        });
                        errors.quote = stockErrors;
                        this.formErrors = errors;
                    });
            } catch (e) {
                this.$root.$emit('addAlertError', 'An error occurred processing the data. Please check your input');
            }
            
        }
    }
}
</script>