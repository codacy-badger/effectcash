<?php

namespace OCA\EffectCash\Tests\Unit\Controller;

use PHPUnit_Framework_TestCase;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\IDBConnection;

use OCA\EffectCash\Controller\SettingController;

class SettingControllerTest extends PHPUnit_Framework_TestCase {

	private $controller;
	private $userId = 'john';
	private $il10n;
	private $configService;

	public function setUp() {
		$request = $this->getMockBuilder('OCP\IRequest')->getMock();

		$connection = \OC::$server->getDatabaseConnection();
		$config = $this->getMockBuilder('OCP\IConfig')->getMock();

		$this->il10n = $this->getMockBuilder('OCP\IL10N')->getMock();

		$this->configService = $this->getMockBuilder('\OCA\EffectCash\Service\ConfigService')
			->setConstructorArgs(['effectcash', $config, $this->userId])
			->getMock();

		$this->controller = new SettingController(
			'effectcash', $request, $this->userId, $this->il10n, $this->configService
		);
	}

	public function testGetSettingsContainer() {
		$this->configService->expects($this->once())
			->method('getSettingsContainer')
			->willReturn([]);

		$this->assertEquals([], $this->controller->getSettingsContainer());
	}

	public function setSettings() {
		$this->configService->expects($this->once())
			->method('setSettings')
			->with([])
			->willReturn([]);

		$this->assertEquals([], $this->controller->setSettings([]));
	}

}
