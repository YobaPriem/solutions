<template>
	<div v-bem>
		<h2 v-bem.title>{{ question.question }}</h2>
		<ul v-bem.items>
			<li v-bem.item v-for="(answer, id) in question.answers" :key="id" @click="setNextQuestion(id)">
				<div v-bem.item-image-wrapper>
					<img :src="answer.image" :alt="answer.name">
				</div>
				<div v-bem.item-content>
					<p v-bem.item-title>{{ answer.name }}</p>
					<p v-bem.item-text>{{ answer.description }}</p>
				</div>
			</li>
		</ul>
	</div>
</template>
<script>
	module.exports = {
		data() {
			return {

			};
		},
		props: {
			question: Object,
		},
		computed: {
		},
		methods: {
			setNextQuestion(id) {
				Array.from(event.target.parentNode.children).forEach(element => {
					element.classList.remove('active');
				});

				if (event.target.classList.contains('active')) {
					event.target.classList.remove('active');
				} else {
					event.target.classList.add('active');
				}

				this.$parent.handleChildChange(id);
			}
		},
		watch: {},
		components: {},
		mixins:[],
		created() {},
	}
</script>