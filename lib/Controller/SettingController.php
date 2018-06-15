<?php
namespace OCA\EffectCash\Controller;

use RRule\RRule;

use OCP\IRequest;
use OCP\IL10N;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;
use OCA\EffectCash\Service\ConfigService;

class SettingController extends Controller {

	private $userId;
	private $l10n;
	private $configService;

	public function __construct($appName, IRequest $request, $userId, IL10N $l10n, ConfigService $configService){
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->l10n = $l10n;
		$this->configService = $configService;
	}

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @return array
	*/
  public function getSettingsContainer() {
  	return $this->configService->getSettingsContainer();
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param array $settings
  * @return array
	*/
  public function setSettings($settings) {
  	return $this->configService->setSettings($settings);
  }

}
