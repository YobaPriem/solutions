<template>
	<section v-bem>
		<button @click="print">ТЫК ДЛЯ PDF</button>
	</section>
</template>
<script>
	/*
		pdfMake очень мощный инструмент для рендера на клиенте пдфки, но шо-то не так с резолвом путей до изображений\шрифтов.
		Мб я дурак, мб лыжи не едут.
		Поэтому пока все нужный пикчи и шрифты режат в base64 в отдельном файлике и импортируются ниже ¯\_(ツ)_/¯
	*/
	import pdfMake from "pdfmake/build/pdfmake";
	import pdfFonts from "pdfmake/build/vfs_fonts";
	import pdfParams from 'component/systems-selection-v2/json/pdfParams.json';

	module.exports = {
		data() { return {
			convertedImage: ''
		};},
		props: {
		},
		computed: {},
		methods: {
			print() {
				pdfParams.content.mainPage.footer['image'] = pdfParams.mainPageFooter,

				pdfMake.vfs = pdfFonts.pdfMake.vfs;
				pdfMake.vfs["Proxima-Nova-Regular.ttf"] = pdfParams.fonts.ProximaNova.normal;
				pdfMake.vfs["Proxima-Nova-Bold.ttf"] = pdfParams.fonts.ProximaNova.bold;
				pdfMake.vfs["Proxima-Nova-Italic.ttf"] = pdfParams.fonts.ProximaNova.italic;
				pdfMake.vfs["Proxima-Nova-Bolditalic.ttf"] = pdfParams.fonts.ProximaNova.bolditalic;

				pdfMake.fonts = {
					Roboto: {
						normal: 'Roboto-Regular.ttf',
						bold: 'Roboto-Medium.ttf',
						italics: 'Roboto-Italic.ttf',
						bolditalics: 'Roboto-Italic.ttf'
					},
					ProximaNova: {
							normal: 'Proxima-Nova-Regular.ttf',
							bold: 'Proxima-Nova-Bold.ttf',
							italics: 'Proxima-Nova-Italic.ttf',
							bolditalics: 'Proxima-Nova-Bolditalic.ttf'
					}
				};

				pdfMake.tableLayouts = {
					solutionsTable: {
						hLineWidth: function () {
							return 0;
						},
						vLineWidth: function () {
							return 0;
						},
						paddingLeft: function () {
							return 5;
						},
						paddingRight: function () {
							return 5;
						},
						fillColor: function (i) {
							let color = '';

							if (i === 0) {
								color = 'red';
							} else {
								if (i % 2 === 0) {
									color = 'gray'
								}
							}

							return color;
						}
					}
				};

				let docDefinition = {
					pageMargins: [ 40, 60, 40, 0 ],
					header: function(currentPage, pageCount, pageSize) {
						if (currentPage === 1) {
							return [
								{
									image: pdfParams.logo,
									fit: [280, 45],
									alignment: 'right',
									margin: [0, 40, 20, 0]
								},
							]
						}

					},
					footer: function(currentPage, pageCount, pageSize) {
						let objectFooter = {};

						if (currentPage !== 1) {
							objectFooter = {
								image: pdfParams.logo
							}
						} else {
							objectFooter = {
								text: '',
								margin: 0,
								height: 0,
								width: 0
							}
						}

						return [
							objectFooter
						]
					},
					content: [
						pdfParams.content.mainPage.title,
						pdfParams.content.mainPage.subtitle,
						{
							text: 'Жилой комплекс «Морская звезда»',
							margin: [0, 15, 0, 0],
							fontSize: 18,
							font: 'ProximaNova',
							bold: true
						},
						{
							text: 'Исх. от 15-04-2020 г.',
							margin: [0, 40, 0, 0],
							fontSize: 14,
						},
						{
							text: 'Иванов Иван Иванович.',
							margin: [0, 40, 0, 0],
							fontSize: 14,
						},
						{
							text: '+7 987 000 00 00',
							margin: [0, 10, 0, 0],
							fontSize: 14,
						},
						{
							text: 'ivanov@tn.ru',
							link: 'mailto:ivanov@tn.ru',
							margin: [0, 10, 0, 30],
							fontSize: 14,
						},
						pdfParams.content.mainPage.footer,
						{
							text: 'Данные для расчета тарифов на доставку компонентов систем',
						},
						{
							layout: 'solutionsTable',
							table: {
								headerRows: 1,
								widths: ['10%', '50%', '40%'],
								body: [
									[ '№ п/п', 'Тип данных', ''],
									[ '1', 'Наименование объекта', 'testvalue'],
									[ '2', 'Местоположение объекта', 'testvalue'],
									[ '3', 'Изображение', {
										image: pdfParams.mainPageFooter,
										width: 100,
										height: 100
									}],
								]
							}
						},
						{
							text: 'Техническое\nпредложение',
							margin: [0, 130, 0, 0],
							fontSize: 32,
							font: 'Roboto',
							bold: true
						},
						{
							text: 'Техническое\nпредложение',
							margin: [0, 130, 0, 0],
							fontSize: 32,
							font: 'ProximaNova',
							bold: true
						},
					]
				};
				pdfMake.createPdf(docDefinition).open();
			},
			getB64Image(url) {
				this.convertFromCanvas(url);

				let b64Image = this.convertedImage;

				return b64Image;
			},
			convertFromCanvas(url) {
				let that = this;
				let img = new Image();

				img.setAttribute('crossOrigin', 'anonymous');

				img.onload = function () {
					let canvas = document.createElement("canvas");
					canvas.width = this.width;
					canvas.height = this.height;

					let ctx = canvas.getContext("2d");
					ctx.drawImage(this, 0, 0);

					let dataURL = canvas.toDataURL("image/png");

					that.convertedImage = dataURL;
				};

				img.src = url;
			},
		},
		watch: {},
		components: {},
		mixins:[],
		created() {
		},
	}
</script>