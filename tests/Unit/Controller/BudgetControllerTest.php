<?php

namespace OCA\EffectCash\Tests\Unit\Controller;

use PHPUnit_Framework_TestCase;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\IDBConnection;

use OCA\EffectCash\Db\Budget;
use OCA\EffectCash\Db\BudgetMapper;
use OCA\EffectCash\Controller\BudgetController;

class BudgetControllerTest extends PHPUnit_Framework_TestCase {

	private $controller;
	private $userId = 'john';
  private $budgetMapper;
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

		$this->budgetMapper = $this->getMockBuilder('OCA\EffectCash\Db\BudgetMapper')
			->setConstructorArgs([$connection])
			->getMock();

		$this->controller = new BudgetController(
			'effectcash', $request, $this->userId, $this->budgetMapper, $this->il10n, $this->configService
		);
	}

	public function testIndex() {
		$this->budgetMapper->expects($this->once())
			->method('findAll')
			->with($this->userId)
			->willReturn($this->getBudgets());

		$this->assertEquals(new JsonResponse($this->getBudgets()), $this->controller->index());
	}

  public function testRead() {
		$id = 222;

		$this->budgetMapper->expects($this->once())
			->method('find')
			->with($id, $this->userId)
			->willReturn($this->getBudget());

		$this->assertEquals(new DataResponse($this->getBudget()), $this->controller->read($id));
  }

  public function testNew() {
    $this->assertEquals(new DataResponse(new Budget()), $this->controller->new());
  }

  public function testSearch() {
		$title = 'test-title';

		$this->budgetMapper->expects($this->once())
			->method('findAllByTitle')
			->with($this->userId, $title)
			->willReturn($this->getBudgets());

		$this->assertEquals(new JsonResponse($this->getBudgets()), $this->controller->search($title));
  }

  public function testBetween() {
    $start = '2018-01-01';
    $end = '2018-12-31';

		$this->budgetMapper->expects($this->once())
			->method('findBetween')
			->with($this->userId, $start, $end)
			->willReturn($this->getBudgets());

    $this->assertEquals(new JsonResponse($this->getBudgets()), $this->controller->between($start, $end));
  }

	public function testBetweenEmpty() {
		$start = '2018-01-01';
    $end = '2018-12-31';

		$this->budgetMapper->expects($this->once())
			->method('findBetween')
			->with($this->userId, $start, $end)
			->willReturn([]);

		$this->assertEquals(new JsonResponse([]), $this->controller->between($start, $end));
	}

	public function testCreate() {
		$budget = $this->createBudget(222, '2018-01-01', false);

		$this->budgetMapper->expects($this->once())
			->method('insert')
			->with($budget)
			->willReturn($budget);

		$this->assertEquals(new DataResponse($budget), $this->controller->create($budget->getTitle(), $budget->getGroupTitle(), $budget->getRepeat(), $budget->getIsIncome(), $budget->getDate(), $budget->getAmount(), $budget->getDescription()));
	}

	public function testUpdate() {
		$id = 222;
		$budget = $this->createBudget($id, '2018-01-01');

		$this->budgetMapper->expects($this->once())
			->method('find')
			->with($id, $this->userId)
			->willReturn($budget);

		$this->budgetMapper->expects($this->once())
			->method('update')
			->with($budget)
			->willReturn($budget);

		$this->assertEquals(new DataResponse($budget), $this->controller->update($budget->getId(), $budget->getTitle(), $budget->getGroupTitle(), $budget->getRepeat(), $budget->getIsIncome(), $budget->getDate(), $budget->getAmount(), $budget->getDescription()));
	}

	public function testDelete() {
		$id = 222;
		$budget = $this->createBudget($id, '2018-01-01');

		$this->budgetMapper->expects($this->once())
			->method('find')
			->with($id, $this->userId)
			->willReturn($budget);

		$this->budgetMapper->expects($this->once())
			->method('delete')
			->with($budget)
			->willReturn($budget);

		$this->assertEquals(new DataResponse($budget), $this->controller->delete($id));
	}

	public function getBudgets($with_id = true) {
		return [
			$this->createBudget(222, '2018-01-01', $with_id),
			$this->createBudget(223, '2018-01-02', $with_id)
		];
	}

	public function getBudget($with_id = true) {
		return $this->createBudget(222, '2018-01-01', $with_id);
	}

	public function createBudget($id, $date, $with_id = true) {
		$b1 = new Budget();
		$b1->setUserId($this->userId);
		if($with_id) {
			$b1->setId(222);
		}
		$b1->setTitle('title');
		$b1->setGroupTitle('group-title');
		$b1->setRepeat('');
		$b1->setIsIncome(0);
		$b1->setDate($date);
		$b1->setAmount('200');
		$b1->setDescription('description');

		return $b1;
	}
}
