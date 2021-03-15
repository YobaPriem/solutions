<template>
	<section v-bem>
		<header v-bem.header>
			<h2 v-bem.title>Больше конструкций</h2>
			<p v-bem.text>Если вам требуются системы и для других конструкций, то давайте подберем и их!
			Выберите тип строительства...</p>
		</header>
		<div v-bem.items>
			<button v-bem.item v-for="(item, id) in links" :key="id" @click="setNextQuestion(id)">
				<img :src="item.image" :alt="item.name">
				<div v-bem.item-content>
					<div v-bem.text>{{ item.name}}</div>
				</div>
			</button>
		</div>
	</section>
</template>
<script>

module.exports = {
		data() { return {
		};},
		props: {
			links: Object
		},
		computed: {},
		methods: {
			setNextQuestion(id) {
				this.$parent.handleChildChange(id, true);
			}
		},
		watch: {},
		components: {},
		mixins:[],
		created() {},
	}
</script>