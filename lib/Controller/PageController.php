<?php
namespace OCA\EffectCash\Controller;

use RRule\RRule;

use OCP\IRequest;
use OCP\IL10N;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Controller;

use \OCA\EffectCash\Service\ConfigService;

use OCA\EffectCash\Db\Budget;
use OCA\EffectCash\Db\BudgetMapper;

class PageController extends Controller {
	private $userId;
	private $budgetMapper;
	private $trans;
	private $configService;

	public function __construct($AppName, IRequest $request, $UserId, BudgetMapper $BudgetMapper, IL10N $trans, ConfigService $configService){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->budgetMapper = $BudgetMapper;
		$this->trans = $trans;
		$this->configService = $configService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @return TemplateResponse
	 */
	public function index() {
		$parameters = [
			'settings' => $this->configService->getSettingsContainer()
		];
		return new TemplateResponse('effectcash', 'index', $parameters);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* @return JsonResponse
	*/
	public function groupsLoad() {
		$groups = $this->budgetMapper->getGroups($this->userId, $this->trans);
		return new JsonResponse($groups);
	}

}
