'use strict';

import Vue from 'vue';
import vueResource from 'vue-resource';
import DatasetList from './DatasetList.vue';

Vue.use(vueResource);
Vue.config.debug = true
Vue.config.strict = true
new Vue({
	el: 'body',
	components: {
		DatasetList: DatasetList,
	}
})
