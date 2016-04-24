<template>
  <div class="filter-search">
    <input class="form-control" v-model="query" @input="search(query)" type="text" placeholder="Search..." spellcheck='false'>
    <i class='fa fa-search'></i>
  </div>
</template>

<script>
export default {
  data() {
    return {'query': ''}
  },
  methods: {
    search (query) {
      this.$dispatch('query.change', query)
    }
  }
}
</script>
