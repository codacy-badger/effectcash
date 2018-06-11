<?php
script('effectcash', 'Chart.bundle.min');
script('effectcash', 'Chart.min');
script('effectcash', 'selectize.min');
script('effectcash', 'rrule');
script('effectcash', 'script');
style('effectcash', 'selectize');
style('effectcash', 'style');
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>
	<div id="app-navigation-right">
		<?php print_unescaped($this->inc('navigation/form')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<div id="app-content-main">
				<?php print_unescaped($this->inc('content/index')); ?>
			</div>
			<div id="app-content-search">
				<?php print_unescaped($this->inc('content/search')); ?>
			</div>
		</div>
	</div>
</div>
