<template>
  <ul class="pagination">
    <li :class="{disabled:!paging.previous}" @click.prevent="to(1)">
      <a href="#">&larr; First</a>
    </li>
    <li :class="{disabled:!paging.previous}" @click.prevent="to(paging.previous)">
      <a href="#">&larr; Previous</a>
    </li>
    <li>
    <span style="float:left;min-width:130px;text-align:center;">Page {{paging.current}} {{paging.last?' of '+paging.last:''}} </span>

    </li>
    <li :class="{disabled:!paging.next}" @click.prevent="to(paging.next)">
      <a href="#">Next &rarr;</a>
    <li>
    <li :class="{disabled:!paging.last}" @click.prevent="to(paging.last)">
      <a href="#">Last &rarr;</a>
    <li>
  </ul>
</template>

<script>
export default {
  props: ['paging'],
  methods: {
    to (page) {
      this.$dispatch('filter.change', page)
    }
  }
}
</script>
