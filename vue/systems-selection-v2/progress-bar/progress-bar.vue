<template>
	<div v-bem>
			<div class="b-solutions-progress-bar" data-devise="<?= $step['bar']['devisePoint'] ?: '' ?>">
					<div class="b-solutions-progress-bar__line">
						<div class="b-solutions-progress-bar__step b-solutions-progress-bar__step--start<?= $isProgress ? ' b-solutions-progress-bar__step--active' : '' ?>">
							<div class="b-solutions-progress-bar__step-text">Старт</div>
							<div class="b-solutions-progress-bar__step-value"><?= ($step['firstStep'] ? '' : ($isProgress ? '1' : '0%')) ?></div>
						</div>
						<?php foreach ($step['points'] as $point) { ?>
							<div class="b-solutions-progress-bar__step b-solutions-progress-bar__step--point<?= $point['active'] ? ' b-solutions-progress-bar__step--active' : '' ?>"
									style="left: <?= $point['progress'] ?>">
								<div class="b-solutions-progress-bar__step-text"><?= $point['text'] ?></div>
								<div class="b-solutions-progress-bar__step-value"><?= $point['value'] ?></div>
							</div>
						<?php } ?>
						<div class="b-solutions-progress-bar__step b-solutions-progress-bar__step--end">
							<div class="b-solutions-progress-bar__step-text">Готовое<br>решение</div>
							<div class="b-solutions-progress-bar__step-value">100%</div>
						</div>
						<?php if ($isProgress) { ?>
							<div
									class="b-solutions-progress-bar__step b-solutions-progress-bar__step--current"
									style="left: <?= $step['bar']['progress'] ?>"
							>
								<div class="b-solutions-progress-bar__step-text"></div>
								<div class="b-solutions-progress-bar__step-value"><?= $step['bar']['value'] ?: $step['bar']['progress'] ?></div>
							</div>
							<div class="b-solutions-progress-bar__progress"
									style="width: <?= $step['bar']['progress'] ?>"></div>
						<?php } ?>
					</div>
		</div>
	</div>
</template>
<script>
	import VueSlider from 'vue-slider-component';

	module.exports = {
		data() { return {
			questionsTree: questionsTree
		};},
		props: [],
		computed: {
		},
		methods: {},
		watch: {},
		components: {VueSlider},
		mixins:[],
		created() {},
	}
</script>