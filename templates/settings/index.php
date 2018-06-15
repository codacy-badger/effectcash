<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		></button>
	</div>
	<div id="app-settings-content">
		<input type="hidden" id="effectcash-settings" value='<?php echo(json_encode($_['settings'])) ?>'>
		<div id="effectcash-settings-form" class="input-frm">
			<label><?php p($l->t('Date format')); ?></label>
			<select name="dateformat"></select>
			<label><?php p($l->t('Currency format')); ?></label>
			<select name="currency"></select>
		</div>
	</div>
</div>
