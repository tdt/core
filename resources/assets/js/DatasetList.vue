<template>

<div class="col-sm-4 col-md-3 hidden-xs">
    <div class="panel panel-default panel-filter">
        <div class="panel-body">
            <search-box></search-box>
            <filter v-for="data in filter" :data="data"></filter>
        </div>
    </div>
</div>
<div class="col-sm-8 col-md-9">
    <dataset v-for="(uri, dataset) in datasets" :dataset="dataset"></dataset>
    <pagination :paging="paging"></pagination>
</div>

</template>

<script>
import SearchBox from './SearchBox.vue';
import Dataset from './Dataset.vue';
import Filter from './Filter.vue';
import Pagination from './Pagination.vue';

export default {
    components: {
        SearchBox,
        Dataset,
        Filter,
        Pagination
    },
    ready() {
        this.fetch();
    },
    data() {
        return {
            datasets: [],
            filter: [],
            paging: {
                first: null,
                prev: null,
                next: null,
                last: null,
                total: null,
                previous: null,
                limit: null,
                offset: null
            },
            page: 1,
            query: ''
        }
    },
    methods: {
        fetch() {
            // Get selections
            var selection = {};
            for(var i in this.filter) {
                var obj = this.filter[i];
                if (obj.selection && obj.selection.length) {
                    selection[obj.filterProperty] = obj.selection.join(',');
                }
            }
            // Get search query
            if (this.query.length) {
                selection.query = this.query
            }
            // Get paging
            // selection.page_size = 1
            if (this.page > 1) {
                selection.page = this.page
            }
            console.log(selection)

            this.$http.get('/api/info', selection).then(function(res) {
                this.datasets = res.data.datasets;
                this.paging = res.data.paging;

                if (this.filter && this.filter[0]) {
                    for(var i in this.filter) {
                        this.filter[i].count = res.data.filter[i].count;
                        this.filter[i].options = res.data.filter[i].options;
                    }
                } else {
                    this.filter = res.data.filter;
                    for(var i in this.filter) {
                        this.filter[i].selection = [];
                    }
                }
            })
        }
    },
    events: {
        'query.change' (query) {
            this.page = 1
            this.query = query
            this.fetch()
        },
        'filter.change' (page) {
            this.page = page
            this.fetch()
        },
    }
}
</script>
