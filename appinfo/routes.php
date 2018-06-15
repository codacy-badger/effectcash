<?php

return [
  'routes' => [
    ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
    ['name' => 'page#groups_load', 'url' => '/groups_load', 'verb' => 'GET'],

    // budgets
    ['name' => 'budget#search', 'url' => '/budgets/search', 'verb' => 'GET'],
    ['name' => 'budget#between', 'url' => '/budgets/between', 'verb' => 'GET'],
    ['name' => 'budget#new', 'url' => '/budgets/new', 'verb' => 'GET'],
    ['name' => 'budget#update', 'url' => '/budgets/{id}', 'verb' => 'PUT'],
    ['name' => 'budget#delete', 'url' => '/budgets/{id}', 'verb' => 'DELETE'],
    ['name' => 'budget#read', 'url' => '/budgets/{id}', 'verb' => 'GET'],
    ['name' => 'budget#index', 'url' => '/budgets', 'verb' => 'GET'],
    ['name' => 'budget#create', 'url' => '/budgets', 'verb' => 'POST'],

    // settings
    ['name' => 'setting#get_settings_container', 'url' => '/settings/settings_container', 'verb' => 'GET'],
    ['name' => 'setting#set_settings', 'url' => '/settings/set_settings', 'verb' => 'PUT'],
   ]
];
