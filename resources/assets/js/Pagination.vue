<template>
  <ul class="pagination">
    <li :class="{disabled:!paging.previous}" @click.prevent="first">
      <a href="#">&larr; First</a>
    </li>
    <li :class="{disabled:!paging.previous}" @click.prevent="previous">
      <a href="#">&larr; Previous</a>
    </li>
    <li>
    <span style="float:left;min-width:130px;text-align:center;">Page {{current}} {{total?' of '+total:''}} </span>
      
    </li>
    <li :class="{disabled:!paging.next}" @click.prevent="next">
      <a href="#">Next &rarr;</a>
    <li>
    <li :class="{disabled:!paging.last}" @click.prevent="last">
      <a href="#">Last &rarr;</a>
    <li>
  </ul>
</template>

<script>
export default {
  props: ['paging', 'limit', 'offset', 'total'],
  computed: {
    current () {
      return Math.floor(this.offset / this.limit) + 1
    }
  },
  methods: {
    first () {
      this.offset = 0
      this.$dispatch('filter.change')
    },
    previous () {
      this.offset = this.paging.previous[0]
      this.$dispatch('filter.change')
    },
    next () {
      this.offset = this.paging.next[0]
      this.$dispatch('filter.change')
    },
    last () {
      this.offset = this.paging.last[0]
      this.$dispatch('filter.change')
    }
  }
}
</script>
