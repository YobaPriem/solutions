<template>
	<section v-bem>
		<form v-if="!status" @submit="checkForm" method="post" action="#" id="offer">
			<header>
				<h2 v-bem.title>Заполните форму и получите готовое техническое предложение</h2>
				<p v-bem.text>В техническом предложении будет сравнение предлагаемых систем, сметный расчет и многое другое!</p>
			</header>
			<div v-bem.chosen v-for="(system, index) in systems" :key="index">
				<div v-bem.chosen-header>
					<div v-bem.block-title>Выбранная система</div>
					<div v-bem.chosen-delete @click="deleteSystem(index)">Удалить</div>
				</div>
				<div v-bem.chosen-footer>
					<div v-bem.chosen-img>
						<img :src="system.images.preview" alt="">
					</div>
					<div v-bem.chosen-content>
						<div v-bem.block-title>{{ system.title }}</div>
						<div v-bem.text>{{ system.text }}</div>
					</div>
				</div>
			</div>
			<div v-bem.contacts-wrapper>
				<h3 v-bem.block-title>Контактные данные</h3>
				<div v-bem.contacts>
					<div v-bem.group>
						<label v-bem.label for="name">ФИО</label>
						<input v-bem.input v-model="name" id="name" type="text" name="name" placeholder="Как вас зовут?" required>
					</div>
					<div v-bem.group>
						<label v-bem.label for="phone">Телефон</label>
						<input v-bem.input v-model="phone" id="phone" type="text" name="phone" v-mask="'+7 ### ###-##-##'" placeholder="+7" required>
					</div>
					<div v-bem.group="'last'">
						<label v-bem.label for="email">Электронная почта</label>
						<input v-bem.input v-model="email" id="email" type="text" name="email" placeholder="Укажите вашу электронную почту" required>
					</div>
					<div v-bem.sms-wrapper v-if="phone">
						<div v-bem.group v-if="code.real">
							<label v-bem.label for="code">Код из смс</label>
							<input v-bem.input v-model="code.input" id="code" type="text" name="code"  v-mask="'# # # #'" placeholder="_ _ _ _" required>
						</div>
						<button v-bem.sms-btn v-on="code.real ? {click: checkCode} : {click: getCode}">
							{{ code.real ? 'Подтвердить' : 'Отправить СМС' }}
						</button>
						<div v-bem.sms-info>
							Введите специальный кол из СМС,<br>
							чтобы подтвердить ваш телефон
						</div>
					</div>
				</div>
			</div>
			<div v-bem.object-wrapper>
				<h3 v-bem.block-title>Информация по объекту</h3>
				<div v-bem.object>
					<div v-bem.group>
						<label v-bem.label for="objectName">Наименование объекта</label>
						<input v-bem.input v-model="object.name" id="objectName" type="text" name="objectName" placeholder="Укажите наименование объекта" required>
					</div>
					<div v-bem.group>
						<label v-bem.label for="objectAddress">Адрес объекта</label>
						<input v-bem.input v-model="object.address" id="objectAddress" type="text" name="objectAddress" placeholder="Укажите адрес объекта" required>
					</div>
				</div>
			</div>
			<div v-if='code.status' v-bem.submit-wrapper>
				<div v-bem.required-info>Все поля обязательны для заполнения</div>
				<div v-bem.policy><span>нажимая кнопку я подтверждаю согласеие<br>с <a href="#">политикой конфиденциальности</a></span></div>
				<button v-bem.submit type="submit">Получить техническое предложение</button>
			</div>
		</form>
		<div v-if="status" v-bem.status>
			<h1>{{ status === 'success' ? 'Заказ на техническое предложение оформлен.' : 'Что-то пошло не так :с.' }}</h1>
			<p>{{ status === 'success' ? 'Нащи менеджеры свяжутся с вами в ближайшее время.' : 'Повторите попытку позже. Если проблема осталась, то проблема осталась.' }}</p>
		</div>
	</section>
</template>
<script>
		/* global $ */
	// import 'suggestions-jquery/dist/css/suggestions.min.css';
	// import 'suggestions-jquery/dist/js/jquery.suggestions.min.js';
	import VueMask from 'v-mask'
	import Vue from 'vue';
	Vue.use(VueMask);

	module.exports = {
		data() { return {
			debug: false,
			name: '',
			phone: '',
			email: '',
			code: {
				input: '',
				real: false,
				status: false
			},
			object: {
				name: '',
				address: ''
			},
			status: ''
		};},
		props: {
			systems: Array,
			history: Array
		},
		computed: {},
		methods: {
			checkCode(e) {
				if (this.debug) {
					this.code.status = true;
				} else {
					if (this.code.input.length == 7) {
						let trimmedValue = this.code.input.replace(/ /g, '');

						this.$api.verifyCode(trimmedValue).then(data => {
							this.code.status = data;
						});

						e.preventDefault(e);
					} else {
						this.code.status = false;
					}
				}

				e.preventDefault(e);
			},
			checkForm(e) {
				let params = {
					name: this.name,
					phone: this.phone,
					email: this.email,
					code: this.code.input,
					object: this.object,
					systems: JSON.stringify(this.systems),
					history: JSON.stringify(this.history)
				};

				this.$api.submitForm(params).then(data => {
					console.log(data);
					if (data) {
						this.status = 'success';
					} else {
						this.status = 'error';
					}
					console.log(this.status);
				});

				e.preventDefault(e);
			},
			getCode(e) {
				if (this.debug) {
					this.code.real = true;
				} else {
					this.$api.getCode(this.phone).then(data => {
						if (data.length !== 0) {
							this.code.real = true;
						}
					});
				}

				e.preventDefault(e);
			},
			deleteSystem(id) {
				this.$parent.handleRemoveFromForm(id);
			}
		},
		watch: {},
		components: {},
		mixins:[],
		created() {},
		mounted: function() {
			// console.log($(this.$el));
			$('#objectAddress').suggestions({
				token: 'b577730f4ebd2d482d7ec054e29b7fe942866f60',
				type: 'ADDRESS',
				onSelect: info => this.object.address = info.value,

			});
		}
	}
</script>