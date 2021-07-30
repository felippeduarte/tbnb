<template>
    <div>
        <div class="row">
            <div class="col-12">
                <ul class="list-group">
                    <li class="list-group-item" v-for="stock in stocks" :key="stock.symbol">
                        {{ stock.symbol }} -> {{ stock.simulatedPrice }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            stocks: [],
        }
    },
    beforeMount() {
        axios.get('/api/simulatedPrice')
            .then((r) => {
                this.stocks = r.data;
            });
    }
}
</script>