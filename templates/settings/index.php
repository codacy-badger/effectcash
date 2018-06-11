<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		></button>
	</div>
	<div id="app-settings-content">
		<input type="hidden" id="app-settings-dateformat-datepicker" value="<?php echo $_['dateformats'][$_['dateformat']]['datepicker'] ?>">
		<input type="hidden" id="app-settings-dateformat-moment" value="<?php echo $_['dateformats'][$_['dateformat']]['moment'] ?>">
		<div id="effectcash-settings-form">
			<label>Datumsformat</label>
			<select name="dateformat">
				<?php foreach($_['dateformats'] as $key => $df) { ?>
					<option <?php echo ($key ==  $_['dateformat'] ? 'selected="selected"' : '') ?>><?php echo $key; ?></option>
				<?php } ?>
			</select>
			<button id="effectcash-settings-bttn-submit" class="button"><?php p($l->t('Save')); ?></button>
		</div>
	</div>
</div>
