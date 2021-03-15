<template>
	<div v-bem>
		<div v-bem.wrap>
			<div v-if="!isObjectEmpty(selected)" v-bem.progress>
				<div v-bem.progress-step="'start'">
					<div v-bem.progress-step-top-text>Старт</div>
					<div v-bem.progress-step-marker></div>
					<div v-bem.progress-step-bottom-text></div>
				</div>
				<div v-bem.progress-bar>
					<solutions-progress size="5"
						:val="currentPercentage"
						:text="currentPercentage + ' %'"
						bar-color="#e00f20"
						bg-color="#fff"
						max="100">
					</solutions-progress>
				</div>
				<div v-bem.progress-step="'end'">
					<div v-bem.progress-step-top-text>Готовое<br>решение</div>
					<div v-bem.progress-step-marker></div>
					<div v-bem.progress-step-bottom-text>100 %</div>
				</div>
			</div>
			<div v-if="isObjectEmpty(selected)" v-bem.start-screen>
				<img src="/img/solutions/start.jpg" alt="">
				<div v-bem.content-title>Одна минута — и перед вами наилучший вариант</div>
				<button v-bem.content-btn @click="setFirstQuestion()">Подобрать систему</button>
			</div>
			<solutions-systems v-if="!isObjectEmpty(selected) && selected.system.length > 0" :question="selected"></solutions-systems>
			<div v-bem.hr v-if="!isObjectEmpty(selected) && !isObjectEmpty(selected.answers) && selected.system.length > 0" ></div>
			<solutions-question v-if="!isObjectEmpty(selected) && !isObjectEmpty(selected.answers)" :question="selected"></solutions-question>
			<div v-bem.content-btn-wrap>
				<button v-if="selected.parent" v-bem.content-btn="'back'" @click="previous()"><span>ВЕРНУТЬСЯ</span></button>
				<button v-if="selected.childs" v-bem.content-btn="'forward'" @click="next()"><span>ПРОДОЛЖИТЬ</span></button>
			</div>
			<div v-bem.hr v-if="showForm"></div>
			<solutions-form v-if="showForm" :systems="formSystems" :history="questionHistory"></solutions-form>
			<!-- <solutions-form :systems="formSystems" :history="questionHistory"></solutions-form> -->
		</div>
		<solutions-additional
			v-if="!isObjectEmpty(selected) && selected.system.length > 0"
			:links="questionsTree[Object.keys(questionsTree)[0]].answers">
		</solutions-additional>
		<!-- <solutions-additional
			:links="questionsTree[Object.keys(questionsTree)[0]].answers">
		</solutions-additional> -->
		<!-- <solutions-pdf></solutions-pdf> -->
	</div>
</template>
<script>
	import solutionsProgress from 'vue-simple-progress';
	import solutionsSystems from 'components-vue/systems-selection-v2/systems';
	import solutionsQuestion from 'components-vue/systems-selection-v2/question';
	import solutionsForm from 'components-vue/systems-selection-v2/form';
	import solutionsAdditional from 'components-vue/systems-selection-v2/solutions-additional';
	import solutionsPdf from 'components-vue/systems-selection-v2/pdf';

	module.exports = {
		data() { return {
			questionsTree: {},
			longestPath: 0,
			currentPercentage: 0,
			questionPercent: 0,
			previousPercent: [],
			questionHistory: [],
			selected: {},
			nextId: '',
			showForm: false,
			formSystems: [],
		};},
		props: [],
		computed: {

		},
		methods: {
			calculateDepthLevel(questionId = 1, depthLevel = 1, allowRecalculate = false,) {
				let element = this.questionsTree[questionId];

				// if (allowRecalculate) {
				// 	this.longestPath = depthLevel;
				// } else {
					 if (depthLevel > this.longestPath) {
						this.longestPath = depthLevel;
					 }
				// }

				if (element && element.childs && !this.isObjectEmpty(element.childs)) {
					depthLevel++;

					for (let child in element.childs) {
						this.calculateDepthLevel(child, depthLevel);
					}
				} else {
					depthLevel
				}
			},
			createMarker() {
				let markerRight = document.querySelector('.vue-simple-progress-bar').offsetWidth;
				let domElement = document.createElement('div');
				let parentElement = document.querySelector('.b-solutions__progress-bar').parentNode;

				domElement.innerHTML = '<div class="b-solutions__progress-step-top-text"></div><div class="b-solutions__progress-step-marker"></div><div class="b-solutions__progress-step-bottom-text">' + this.previousPercent.length + '</div>';
				console.log('b-solutions__progress-step--default');
				// А знаете почему два раза адд на класлист? Потому что ИЕ11 не понимает, что через запятую можно несколько классов передать
				// Здорово, правда?
				// А мы продолжаем это поддерживать
				// У меня просто пригорело, извините :С
				domElement.classList.add('b-solutions__progress-step');
				domElement.classList.add('b-solutions__progress-step--default')
				domElement.style.left = (markerRight + 11) + 'px';

				parentElement.appendChild(domElement);
			},
			resetMarkers() {
				let markers = document.querySelectorAll('.b-solutions__progress-step--default');

				markers.forEach(marker => {
					marker.parentNode.removeChild(marker);
				});
			},
			deleteMarker(num) {
				let marker = document.querySelectorAll('.b-solutions__progress-step--default')[num];
				marker.parentNode.removeChild(marker);
			},
			handleChildChange(id, allowChange = false) {
				this.nextId = id;

				if (allowChange === true) {
					this.resetMarkers();
					this.longestPath = 0;
					this.currentPercentage = 0;
					this.questionPercent = Math.abs(this.previousPercent[0]);
					this.questionHistory.splice(0);
					this.previousPercent.splice(0);

					// Пока не завершится анимация отката прогресс бара
					setTimeout(() => {
						this.next()
					}, 500);
				}
			},
			handleAddToForm(system = {}) {
				if (!this.isObjectEmpty(system)) {
					if (!this.searchInArray(system.id, this.formSystems)) {
						this.formSystems.push(system);
						this.showForm = true;
					}
				}
			},
			handleRemoveFromForm(id) {
				this.formSystems.splice(id, 1);

				if (this.formSystems.length < 1) {
					this.showForm = false;
				}
			},
			isObjectEmpty(object) {
				return Object.keys(object).length === 0;
			},
			next(setMarker = true) {
				if (this.nextId) {
					this.questionHistory.push(this.questionsTree[this.nextId].name);
					this.longestPath = 0;
					this.calculateDepthLevel(this.nextId, 1, true);
					this.previousPercent.push(-Math.abs(this.questionPercent));

					if (setMarker) {
						this.createMarker();
					}

					this.setSelectedQuestion(this.nextId);

					if (!this.selected.childs) {
						// тут последний вопрос - он же показ системы
						this.previousPercent.push(-Math.abs(this.questionPercent));
						this.currentPercentage = this.currentPercentage + this.questionPercent;
						this.moveTooltip();
					}

					this.nextId = '';
				}
			},
			previous() {
				let spliceOffset = 1;
				let markers = document.querySelectorAll('.b-solutions__progress-step--default');

				this.questionHistory.splice(this.questionHistory.length - spliceOffset);

				// Удаляем послединй маркер
				this.deleteMarker(markers.length - 1);

				// Последняя сисетма добавляем 2 значения в массив процентажа, а значит и вычесть надо два при возврате
				this.questionPercent = this.previousPercent[this.previousPercent.length - spliceOffset];

				if (!this.selected.childs && this.selected.system.length > 0) {
					spliceOffset = 2;
					this.questionPercent = this.questionPercent + this.previousPercent[this.previousPercent.length - spliceOffset]
				}

				this.previousPercent.splice(this.previousPercent.length - spliceOffset);


				this.longestPath = 0;
				this.calculateDepthLevel(this.selected.parent);
				this.setSelectedQuestion(this.selected.parent);
				this.nextId = '';
			},
			setFirstQuestion() {
				if (!this.isObjectEmpty(this.questionsTree)) {
					this.setSelectedQuestion(Object.keys(this.questionsTree)[0]);
				} else {
					console.warn('Eyy baus, can i have some questions, pls??????');
				}
			},
			searchInArray(id, array) {
				let exist = false;

				array.forEach(element => {
					if (element.id == id) {
						exist = true
					}
				});

				return exist;
			},
			setSelectedQuestion(id) {
				this.currentPercentage = this.currentPercentage + this.questionPercent;
				this.questionPercent = Math.ceil((100 - this.currentPercentage) / this.longestPath);

				var question = this.questionsTree[id];

				question.answers = {};

				if (question.childs) {
					Object.keys(question.childs).forEach(element => {
						question.answers[element] = this.questionsTree[element];
					});
				}

				this.selected = question;

				this.moveTooltip();
			},
			moveTooltip() {
				if (this.currentPercentage > 0) {
					let progressBarText = document.querySelector('.vue-simple-progress-text');

					// Нужен чтобы сдвинуть проценты
					let additionalOffset = 11;

					if (this.currentPercentage === 100) {
						additionalOffset = 34;
						document.querySelector('.b-solutions__progress-step--end').classList.add('hidden');
					} else {
						document.querySelector('.b-solutions__progress-step--end').classList.remove('hidden');
					}

					progressBarText.style.left = 'calc(' + this.currentPercentage + '% - ' + additionalOffset + 'px)';
				}
			}
		},
		watch: {},
		components: {
			solutionsQuestion,
			solutionsSystems,
			solutionsForm,
			solutionsAdditional,
			solutionsProgress,
			solutionsPdf
		},
		mixins:[],
		created() {},
		mounted() {
			this.$api.loadJSON().then(response => {
				this.questionsTree = response;
				this.calculateDepthLevel(Object.keys(this.questionsTree)[0]);
			}, response => {console.log(response)})
		}
	}
</script>