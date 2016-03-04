<template>

<div class="col-sm-4 col-md-3 hidden-xs">
    <div class="panel panel-default panel-filter">
        <div class="panel-body">
            <filter v-for="data in filter" :data="data"></filter>
        </div>
    </div>
</div>
<div class="col-sm-8 col-md-9">
    <dataset v-for="(uri, dataset) in datasets" :dataset="dataset"></dataset>
</div>

</template>

<script>
import Dataset from './Dataset.vue';
import Filter from './Filter.vue';

export default {
    components: {
        Dataset: Dataset,
        Filter: Filter
    },
    ready() {
        this.fetch();
        this.$on('filter.change', this.fetch)
    },
    data() {
        return {
            datasets: [],
            filter: [],
            paging: {}
        }
    },
    methods: {
        fetch() {
            var selection = {};
            for(var i in this.filter) {
                var obj = this.filter[i];
                if ( obj.selection && obj.selection.length) {
                    selection[obj.filterProperty] = obj.selection.join(',');
                }
            }
            this.$http.get('/api/info', selection).then(function(res) {
                this.datasets = res.data.datasets;
                this.paging = res.data.paging;

                if (this.filter && this.filter[0]) {
                    // TODO: only update filter.options
                } else {
                    this.filter = res.data.filter;
                    for(var i in this.filter) {
                        this.filter[i].selection = [];
                    }
                }
            })
        }
    }
}
</script>
