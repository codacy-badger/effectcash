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
use OCA\EffectCash\Db\Budget;
use OCA\EffectCash\Db\BudgetMapper;

class BudgetController extends Controller {

	private $userId;
	private $budgetMapper;
	private $l10n;
	private $configService;

	public function __construct($appName, IRequest $request, $userId, BudgetMapper $budgetMapper, IL10N $l10n, ConfigService $configService){
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->budgetMapper = $budgetMapper;
		$this->l10n = $l10n;
		$this->configService = $configService;
	}

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @return JsonResponse
	*/
  public function index() {
  	return new JsonResponse($this->budgetMapper->findAll($this->userId));
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param integer $id
  * @return DataResponse
	*/
  public function read($id) {
    return new DataResponse($this->budgetMapper->find($id, $this->userId));
  }

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @return DataResponse
	*/
  public function new() {
		$budget = new Budget();
    return new DataResponse($budget);
  }

  /**
  * @NoAdminRequired
  * @NoCSRFRequired
  * @param string $title
  * @return JsonResponse
  */
  public function search($title) {
    if($title === null) {
      return JsonResponse([]);
    }
    return new JsonResponse($this->budgetMapper->findAllByTitle($this->userId, $title));
  }

  /**
  * @NoAdminRequired
  * @NoCSRFRequired
  * @param string $start
  * @param string $end
  * @return JsonResponse
  */
  public function between($start, $end) {
    $budgets = $this->budgetMapper->findBetween($this->userId, $start, $end);
		$budgets = $this->rrule($budgets, $start, $end);
    return new JsonResponse($budgets);
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param string $title
  * @param string $group_title
  * @param string $repeat
  * @param boolean $is_income
  * @param string $date
  * @param string $amount
  * @param string $description
  * @return DataResponse
	*/
  public function create($title, $group_title, $repeat, $is_income, $date, $amount, $description) {
    $budget = new Budget();
    $budget->setUserId($this->userId);
		$budget->setTitle($title);
		$budget->setGroupTitle($group_title);
		$budget->setRepeat($repeat);
		$budget->setIsIncome($is_income);
		$budget->setDate($date);
		$budget->setAmount($amount);
		$budget->setDescription($description);
    return new DataResponse($this->budgetMapper->insert($budget));
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param integer $id
	* @param string $title
  * @param string $group_title
  * @param string $repeat
  * @param boolean $is_income
  * @param string $date
  * @param string $amount
  * @param string $description
  * @return DataResponse
	*/
  public function update($id, $title, $group_title, $repeat, $is_income, $date, $amount, $description) {
    $budget = $this->budgetMapper->find($id, $this->userId);
    $budget->setTitle($title);
    $budget->setGroupTitle($group_title);
    $budget->setRepeat($repeat);
    $budget->setIsIncome($is_income);
    $budget->setDate($date);
    $budget->setAmount($amount);
    $budget->setDescription($description);
	  return new DataResponse($this->budgetMapper->update($budget));
  }

  /**
  * @NoAdminRequired
  * @NoCSRFRequired
  * @param integer $id
  * @return DataResponse
  */
  public function delete($id) {
    $budget = $this->budgetMapper->find($id, $this->userId);
    return new DataResponse($this->budgetMapper->delete($budget));
  }

  /* private */

	/**
	* @param array $budgets
	* @param string $start
	* @param string $end
	* @return array
	*/
  private function rrule($budgets, $start, $end) {
    $new_budgets = [];

    foreach($budgets as $budget) {
      $repeat = $budget->getRepeat();
			$repeat_type = null;

			if(strlen($repeat) !== 2) {
      	$repeat_type = substr($repeat, 0, 1);
      	$repeat_len = substr($repeat, 1, 1);
			}

      switch ($repeat_type) {
        case 'w':
          $new_budgets = array_merge($new_budgets, $this->build_budgets_by_rrule(new RRule([
            'FREQ' => 'WEEKLY',
            'INTERVAL' => $repeat_len,
            'BYDAY' => substr($budget->getDateAsFormat('D'), 0, -1),
            'DTSTART' => $start,
            'UNTIL' => $end
          ]), $budget));
          break;
        case 'm':
          $new_budgets = array_merge($new_budgets, $this->build_budgets_by_rrule(new RRule([
            'FREQ' => 'MONTHLY',
            'INTERVAL' => $repeat_len,
            'BYMONTHDAY' => $budget->getDateAsFormat('j'),
            'DTSTART' => $start,
            'UNTIL' => $end
          ]), $budget));
          break;
        case 'y':
        $new_budgets = array_merge($new_budgets, $this->build_budgets_by_rrule(new RRule([
          'FREQ' => 'YEARLY',
          'INTERVAL' => $repeat_len,
          'BYMONTH' => $budget->getDateAsFormat('n'),
          'BYMONTHDAY' => $budget->getDateAsFormat('j'),
          'DTSTART' => $start,
          'UNTIL' => $end
        ]), $budget));
        break;
        default:
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
