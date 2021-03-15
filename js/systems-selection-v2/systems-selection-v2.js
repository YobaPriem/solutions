import Vue from 'vue';
import vBem from 'v-bem';
import vueResource from 'vue-resource';
import Api from './api';

import solutions from 'components-vue/systems-selection-v2/app';

Vue.use(vBem, {blockPrefix: 'b-', modSeparator: '--'});
Vue.use(vueResource);

let app = new Vue({
	components: {solutions},
});

let $api = new Api(app.$http);

Vue.mixin({
	created: function() {
		this.$api = $api;
	},
});
document.addEventListener("DOMContentLoaded", function () {
	app.$mount("#systems-selection-v2");
});

export {app};
