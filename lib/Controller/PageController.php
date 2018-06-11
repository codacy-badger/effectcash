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
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		$parameters = $this->configService->userSettings();
		return new TemplateResponse('effectcash', 'index', $parameters);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* @param string $search
	*/
	public function search($search) {
		$budgets = $this->budgetMapper->findAllByTitle($this->userId, $search);
		return new JsonResponse($budgets);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* @param string $start
	* @param string $end
	*/
	public function budgetsLoad($start, $end) {
		$budgets = $this->budgetMapper->findBetween($id, $this->userId, $start, $end);
		$budgets = $this->rrule($budgets, $start, $end);
		return new JsonResponse($budgets);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function groupsLoad() {
		$groups = $this->budgetMapper->getGroups($this->userId, $this->trans);
		return new JsonResponse($groups);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	*/
	public function budgetNew() {
		$budget = new Budget();
		return new JsonResponse($budget);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* @param int $id
	*/
	public function budgetEdit($id) {
		$budget = $this->budgetMapper->find($id, $this->userId);
		return new JsonResponse($budget);
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* @param int $id
	* @param string $title
	* @param string group_title
	* @param string $repeat
	* @param boolean $is_income
	* @param string $date
	* @param string $amount
	* @param string $description
	*/
	public function budgetSubmit($id, $title, $group_title, $repeat, $is_income, $date, $amount, $description) {
		$new_record = true;

		if (!empty($id)) {
			$budget = $this->budgetMapper->find($id, $this->userId);
			$new_record = false;
		}

		if($new_record) {
			$budget = new Budget();
		}

		$date = (\DateTime::createFromFormat($this->configService->getDateformatPHP(), $date))->format('Y-m-d');

		$budget->setUserId($this->userId);
		$budget->setTitle($title);
		$budget->setGroupTitle($group_title);
		$budget->setRepeat($repeat);
		$budget->setIsIncome($is_income);
		$budget->setDate($date);
		$budget->setAmount($amount);
		$budget->setDescription($description);

		if($new_record) {
			return new DataResponse($this->budgetMapper->insert($budget));
		} else {
			return new DataResponse($this->budgetMapper->update($budget));
		}
	}

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
	* @param string $dateformat
	*/
	public function settingsSave($dateformat) {
		$this->configService->setUserValue('dateformat', $dateformat);
		return new DataResponse();
	}

	private function rrule($budgets, $start, $end) {
		$new_budgets = [];

		foreach($budgets as &$budget) {
			$repeat = $budget->getRepeat();
			$repeat_type = substr($repeat, 0, 1);
			$repeat_len = substr($repeat, 1, 1);

			switch ($repeat_type) {
				case 'w':
					$new_budgets = array_merge($new_budgets, $this->build_budgets_by_rrule(new RRule([
						'FREQ' => 'WEEKLY',
						'INTERVAL' => $repeat_len,
						'BYDAY' => substr($budget->getDateFormat('D'), 0, -1),
						'DTSTART' => $start,
						'UNTIL' => $end
					]), $budget));
					break;
				case 'm':
					$new_budgets = array_merge($new_budgets, $this->build_budgets_by_rrule(new RRule([
						'FREQ' => 'MONTHLY',
						'INTERVAL' => $repeat_len,
						'BYMONTHDAY' => $budget->getDateFormat('j'),
						'DTSTART' => $start,
						'UNTIL' => $end
					]), $budget));
					break;
				case 'y':
				$new_budgets = array_merge($new_budgets, $this->build_budgets_by_rrule(new RRule([
					'FREQ' => 'YEARLY',
					'INTERVAL' => $repeat_len,
					'BYMONTH' => $budget->getDateFormat('n'),
					'BYMONTHDAY' => $budget->getDateFormat('j'),
					'DTSTART' => $start,
					'UNTIL' => $end
				]), $budget));
				break;
				default:
					$budget->setDate($budget->getDateFormat('Y-m-d'));
					$new_budgets[] = $budget;
					break;
			}
		}
		return $new_budgets;
	}

	private function build_budgets_by_rrule($rrule, $budget) {
		$new_budgets = [];
		foreach ($rrule as $occurrence) {
			$new_budget = clone $budget;
			$new_budget->setDate($occurrence->format('Y-m-d'));
			$new_budgets[] = $new_budget;
		}
		return $new_budgets;
	}

}
