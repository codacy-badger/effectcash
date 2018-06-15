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
  * @return array
	*/
  public function index() {
  	return $this->budgetMapper->findAll($this->userId);
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param integer $id
  * @return \OCP\AppFramework\Db\Entity
	*/
  public function read($id) {
    return $this->budgetMapper->find($id, $this->userId);
  }

	/**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @return \OCP\AppFramework\Db\Entity
	*/
  public function new() {
    return new Budget();
  }

  /**
  * @NoAdminRequired
  * @NoCSRFRequired
  * @param string $title
  * @return array
  */
  public function search($title) {
    if($title === null) {
      return [];
    }
    return $this->budgetMapper->findAllByTitle($this->userId, $title);
  }

  /**
  * @NoAdminRequired
  * @NoCSRFRequired
  * @param string $start
  * @param string $end
  * @return array
  */
  public function between($start, $end) {
    $budgets = $this->budgetMapper->findBetween($this->userId, $start, $end);
    $budgets = $this->rrule($budgets, $start, $end);
    return $budgets;
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param string $title
  * @param string $group_title
  * @param string $repeat
  * @param boolean $is_income
  * @param string $budget_date
  * @param string $amount
  * @param string $description
  * @return \OCP\AppFramework\Db\Entity
	*/
  public function create($title, $group_title, $repeat, $is_income, $budget_date, $amount, $description) {
    $budget = new Budget();
    $budget->setUserId($this->userId);
		$budget->setTitle($title);
		$budget->setGroupTitle($group_title);
		$budget->setRepeat($repeat);
		$budget->setIsIncome($is_income);
		$budget->setBudgetDate($budget_date);
		$budget->setAmount($amount);
		$budget->setDescription($description);

    return $this->budgetMapper->insert($budget);
  }

  /**
	* @NoAdminRequired
	* @NoCSRFRequired
  * @param integer $id
	* @param string $title
  * @param string $group_title
  * @param string $repeat
  * @param boolean $is_income
  * @param string $budget_date
  * @param string $amount
  * @param string $description
  * @return \OCP\AppFramework\Db\Entity
	*/
  public function update($id, $title, $group_title, $repeat, $is_income, $budget_date, $amount, $description) {
    $budget = $this->budgetMapper->find($id, $this->userId);
    $budget->setUserId($this->userId);
    $budget->setTitle($title);
    $budget->setGroupTitle($group_title);
    $budget->setRepeat($repeat);
    $budget->setIsIncome($is_income);
    $budget->setBudgetDate($budget_date);
    $budget->setAmount($amount);
    $budget->setDescription($description);

    return $this->budgetMapper->update($budget);
  }

  /**
  * @NoAdminRequired
  * @NoCSRFRequired
  * @param integer $id
  * @return \OCP\AppFramework\Db\Entity
  */
  public function delete($id) {
    $budget = $this->budgetMapper->find($id, $this->userId);
    return $budget;
  }

  /* private */

	/**
	* @param array $budgets
	* @param string $start
	* @param string $end
	*/
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
          $budget->setBudgetDate($budget->getDateAsFormat('Y-m-d'));
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
      $new_budget->setBudgetDate($occurrence->format('Y-m-d'));
      $new_budgets[] = $new_budget;
    }
    return $new_budgets;
  }

}
