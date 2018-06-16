<?php
script('effectcash', array(
	'Chart.bundle.min',
	'Chart.min',
	'selectize.min',
	'effectlist',
	'script',
	'start'
));
style('effectcash', array(
	'selectize',
	'effectlist',
	'style'
));
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>
	<div id="app-content">
		<div id="app-navigation-right">
			<?php print_unescaped($this->inc('navigation/form')); ?>
		</div>
		<div id="app-content-main">
			<?php print_unescaped($this->inc('content/index')); ?>
		</div>
		<div id="app-content-search">
			<?php print_unescaped($this->inc('content/search')); ?>
		</div>
	</div>
</div>
