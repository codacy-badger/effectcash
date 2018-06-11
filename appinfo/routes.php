<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\EffectCash\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
     ['name' => 'page#search', 'url' => '/search', 'verb' => 'GET'],
     ['name' => 'page#budget_new', 'url' => '/budget_new', 'verb' => 'GET'],
     ['name' => 'page#budget_edit', 'url' => '/budget_edit', 'verb' => 'GET'],
     ['name' => 'page#budget_submit', 'url' => '/budget_submit', 'verb' => 'POST'],
     ['name' => 'page#budgets_load', 'url' => '/budgets_load', 'verb' => 'GET'],
     ['name' => 'page#groups_load', 'url' => '/groups_load', 'verb' => 'GET'],
     ['name' => 'page#settings_save', 'url' => '/settings_save', 'verb' => 'POST'],
    ]
];
