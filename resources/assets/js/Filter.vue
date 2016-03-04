<template>

<h4 class="filter-title">{{data.displayName}}</h4>
<div class="filter-list" v-bind:class="{'filter-collapsed':isCollapsed}">
		<label class="filter-option checkbox" v-for="(option, count) in data.options" data-count="{{count}}">
		<input type="checkbox" value="{{option}}" @change="change(data.filterProperty, option)" v-model="data.selection">
		{{option}}
		</label>
		<button type="button" class="filter-btn btn" @click="expand()">Show more</button>
</div>

</template>

<script>

export default {
	props: ['data'],
	data() {
		return {'expanded': false}
	},
	computed: {
		isCollapsed() {
			return this.data.count > 8 && !this.expanded;
		}
	},
	methods: {
		change() {
			this.$dispatch('filter.change')
		},
		expand() {
			this.expanded = !this.expanded;
		}
	}
}

</script>
