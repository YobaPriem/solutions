<template>
	<div v-bem>
		<div v-bem.wrap>
			<div v-bem.background></div>
			<div v-bem.container>
				<button v-bem.close-btn @click="close"></button>
				<div v-bem.content>
					<div v-bem.left>
						<img :src="item.images.detail" alt="">
					</div>
					<div v-bem.right>
						<div v-bem.title>{{ item.title }}</div>
						<div v-bem.text>{{ item.text }}</div>
						<div v-bem.hr></div>
						<ol v-bem.list>
							<li v-bem.list-item v-for="(material, id) in item.materials" :key="id">
								<span v-bem.counter>{{ id + 1 }}</span>
								<span>{{ material.title }}</span>
							</li>
						</ol>
						<div v-bem.more-wrapper>
							<a v-bem.more :href="item.url.detail" target="_blank">Подробнее</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	module.exports = {
		data() { return {
		};},
		props: {
			item: Object
		},
		computed: {
		},
		methods: {
			close() {
				this.$parent.closePopup();
			}
		},
		watch: {},
		components: {
		},
		mixins:[],
		created() {},
		mounted() {
		}
	}
</script>