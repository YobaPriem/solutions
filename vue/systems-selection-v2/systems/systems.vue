<template>
	<section v-bem>
		<header v-bem.header>
			<div v-bem.header-title>Ваши системы</div>
		</header>
		<div v-bem.list>
			<spinner v-if="loading" :size="70" :line-size="15" line-fg-color="#e41f25" v-bem.spinner></spinner>
			<article v-bem.item v-for="(item, index) in systems" :key="index">
				<div v-bem.item-top-right>
					<div v-bem.item-share @click="copyLink($event, item.url.detail)">
						<div v-bem.item-share-popup>Ссылка скопирована</div>
					</div>
				</div>
				<div v-bem.item-img>
					<img :src="item.images.preview" :alt="item.title">
				</div>
				<div v-bem.item-content>
					<header v-bem.item-header>
						<div v-bem.item-devise-img>
							<img :src="item.images.preview" :alt="item.title">
						</div>
						<div v-bem.item-text-content>
							<div v-bem.item-title>{{ item.title }}</div>
							<div v-bem.item-text>{{ item.text }}</div>
						</div>
					</header>
					<div v-bem.item-links>
						<div v-bem.item-link-wrapper>
							<a v-bem.item-link :href="item.document.url" target="_blank">
								<img src="/img/solutions/detail/file.svg" alt="">
								<div v-bem.item-link-info>
									<div v-bem.item-link-title>{{ item.document.title }}</div>
									<div v-bem.item-link-text>{{ item.document.extension }}, {{ item.document.filesize }}</div>
								</div>
							</a>
						</div>
						<div v-bem.item-link-wrapper>
							<a v-bem.item-link :href="item.bim" target="_blank">
								<img src="/img/solutions/detail/grid.svg" alt="">
								<div v-bem.item-link-info>
									<div v-bem.item-link-title>BIM каталог</div>
								</div>
							</a>
						</div>
						<div v-bem.item-link-wrapper v-for="(blueprint, id) in item.blueprints" :key="id">
							<a v-bem.item-link :href="blueprint.url" download>
								<img src="/img/solutions/detail/file.svg" alt="">
								<div v-bem.item-link-info>
									<div v-bem.item-link-title>{{ blueprint.title }}</div>
									<div v-bem.item-link-text>{{ blueprint.extension }}, {{ blueprint.filesize }}</div>
								</div>
							</a>
						</div>
					</div>
					<div v-bem.item-hr></div>
					<footer v-bem.item-footer>
						<div v-bem.item-flex-row>
							<button v-bem.item-more @click="setSystemForPopup(index)">Подробнее о системе</button>
							<button v-bem.item-tech @click="addToForm(index)">Тех. предложение</button>
						</div>
						<div v-bem.item-flex-row>
							<div v-if="item.price" v-bem.item-price>от {{ item.price }} ₽ / м²</div>
							<a :href="item.url.shop" target="_blank" v-bem.item-store>Перейти в интернет-магазин</a>
						</div>
					</footer>
				</div>
			</article>
		</div>
		<footer v-bem.footer>
			<div v-bem.footer-info>* Не является публичной офертой</div>
		</footer>
		<solutions-popup v-if="systemPopup" :item="systems[systemPopup]"></solutions-popup>
	</section>
</template>
<script>
import solutionsPopup from 'components-vue/systems-selection-v2/popup';
import spinner from 'vue-simple-spinner';

module.exports = {
		data() { return {
			loading: false,
			systems: {},
			systemPopup: '',
		};},
		props: {
			question: Object,
		},
		computed: {
		},
		methods: {
			copyLink(event, url) {
				let el = document.createElement('textarea')

				el.value = url;
				document.body.appendChild(el);
				el.select();
				document.execCommand('copy');
				document.body.removeChild(el);

				let popup = event.target.firstElementChild;

				popup.classList.add('active');

				setTimeout(function(){
					popup.classList.remove('active')
				}, 2000);
			},
			loadSystems() {
				this.loading = true;

				this.$api.loadSystems(this.question.system).then(data => {
					this.systems = data;

					if (Object.keys(this.systems).length === 1) {
						this.addToForm(Object.keys(this.systems)[0]);
					}
					this.loading = false;
				});
			},
			addToForm(id) {
				this.$parent.handleAddToForm(this.systems[id.toString()]);
			},
			setSystemForPopup(id) {
				this.systemPopup = id.toString();
				document.querySelector('body').style.overflowY = 'hidden';
			},
			closePopup() {
				this.systemPopup = '';
				document.querySelector('body').style.overflowY = 'auto';
			}
		},
		watch: {
			question() {
				this.loadSystems();
			}
		},
		components: { solutionsPopup, spinner },
		mixins:[],
		created() {
			this.loadSystems();
		},
	}
</script>