<?php

namespace OCA\EffectCash\Tests\Unit\Controller;

use PHPUnit_Framework_TestCase;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IDBConnection;

use OCA\EffectCash\Controller\PageController;


class PageControllerTest extends PHPUnit_Framework_TestCase {

	private $controller;
	private $userId = 'john';
	private $configService;
	private $budgetMapper;
	private $il10n;

	public function setUp() {
		$request = $this->getMockBuilder('OCP\IRequest')->getMock();
		$connection = \OC::$server->getDatabaseConnection();

		$config = $this->getMockBuilder('OCP\IConfig')->getMock();

		$this->il10n = $this->getMockBuilder('OCP\IL10N')->getMock();

		$this->configService = $this->getMockBuilder('\OCA\EffectCash\Service\ConfigService')
			->setConstructorArgs(['effectcash', $config, $this->userId])
			->getMock();

		$this->budgetMapper = $this->getMockBuilder('OCA\EffectCash\Db\BudgetMapper')
			->setConstructorArgs([$connection])
			->getMock();

		$this->controller = new PageController(
			'effectcash', $request, $this->userId, $this->budgetMapper, $this->il10n, $this->configService
		);
	}

	public function testIndex() {
		$result = $this->controller->index();

		$this->assertEquals('index', $result->getTemplateName());
		$this->assertTrue($result instanceof TemplateResponse);
	}

	public function testGroupsLoad() {
		$result = $this->controller->groupsLoad();

		$this->assertTrue($result instanceof JsonResponse);
	}

}
