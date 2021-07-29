<template>
    <div>
        <div id="alertContainer">
            <b-alert v-for="(item,index) in alertList"
                :closable="item.closable"
                :type="item.type"
                :duration="item.duration"
                :key="item.key"
                :show="true"
                @close="alertList.splice(index, 1)">
                <span v-html="item.message"></span>
            </b-alert>
        </div>
        <navbar></navbar>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9"></div>
                <div class="col-md-3 float-right">
                    <stocks-list></stocks-list>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
export default {
    data() {
        return {
            alertList: [],
        }
    },
    beforeMount() {
        this.$root.$on("addAlertSuccess", message => {
            this.addAlertSuccess(message);
        });
        this.$root.$on("addAlertError", message => {
            alert('a');
            this.addAlertError(message);
        });
    },
    methods: {
        addAlertSuccess(message) {
            this.addAlert("success", true, true, message);
        },
        addAlertError(message) {
            this.addAlert("danger", true, true, message);
        },
        addAlert(type, canClose, isDisplayTime, message) {
            var conf = {
                type: type,
                closable: canClose,
                message: message,
                key: new Date().getTime()
            };
            if(isDisplayTime) {
                conf.duration = 5000;
            }
            this.alertList.push(conf);
        }
    },
}
</script>