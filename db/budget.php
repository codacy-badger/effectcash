<?php
namespace OCA\EffectCash\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Budget extends Entity implements JsonSerializable {

	protected $userId;
	protected $title;
	protected $groupTitle;
	protected $repeat;
	protected $isIncome;
  protected $budgetDate;
	protected $amount;
	protected $description;

    public function __construct() {
        $this->addType('is_income', 'boolean');
        $this->addType('amount', 'float');

        /* Defaults */
        $this->setIsIncome(0);
        $this->setBudgetDate(date('Y-m-d'));
    }

    public function getDateAsFormat($format) {
      $phpdate = strtotime($this->budgetDate);
      return date($format, $phpdate);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'group_title' => $this->groupTitle,
            'repeat' => $this->repeat,
            'is_income' => $this->isIncome,
            'budget_date' => $this->getDateAsFormat('Y-m-d'),
            'amount' => $this->amount,
            'description' => $this->description
        ];
    }

}
