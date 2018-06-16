<?php
namespace OCA\EffectCash\AppInfo;

use OC\AppFramework\Utility\SimpleContainer;
use \OCP\AppFramework\App;
use \OCA\EffectCash\Db\BudgetMapper;
use \OCA\EffectCash\Controller\PageController;
use \OCA\EffectCash\Controller\BudgetController;
use \OCA\EffectCash\Service\ConfigService;

class Application extends App {

	public function __construct (array $urlParams=array()) {
		parent::__construct('effectcash', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('PageController', function($c) {
			/** @var SimpleContainer $c */
			return new PageController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('BudgetMapper'),
        $c->query('L10N'),
				$c->query('ConfigService')
			);
		});

		$container->registerService('BudgetController', function($c) {
			/** @var SimpleContainer $c */
			return new BudgetController(
				$c->query('AppName'),
				$c->query('Request'),
				$c->query('UserId'),
				$c->query('BudgetMapper'),
        $c->query('L10N'),
				$c->query('ConfigService')
			);
		});

		$server = $container->getServer();
		$container->registerService('BudgetMapper', function($c) use ($server) {
			/** @var SimpleContainer $c */
			return new BudgetMapper(
				$server->getDatabaseConnection()
			);
		});

    $container->registerService('L10N', function($c) {
      return $c->query('OCP\IL10N');
    });

		$container->registerService('ConfigService', function ($c) {
			return new ConfigService(
				$c->query('AppName'),
				$c->query('CoreConfig'),
				$c->query('UserId')
			);
		});

	}
}
