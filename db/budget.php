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
        $this->setDate(date('Y-m-d'));
    }

    public function getDateAsFormat($format) {
      $datetime = date_create_from_format('Y-m-d H:i:s', $this->getBudgetDate());
      return date_format($datetime, $format);
    }

		public function getDate() {
			$datetime = date_create_from_format('Y-m-d H:i:s', $this->getBudgetDate());
			return date_format($datetime, 'Y-m-d');
		}

		public function setDate($date) {
			$datetime = date_create_from_format('Y-m-d', $date);
			$this->setBudgetDate(date_format($datetime, 'Y-m-d H:i:s'));
		}

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'group_title' => $this->groupTitle,
            'repeat' => $this->repeat,
            'is_income' => $this->isIncome,
            'date' => $this->getDate(),
            'amount' => $this->amount,
            'description' => $this->description
        ];
    }

}
