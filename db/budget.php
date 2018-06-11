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
  protected $date;
	protected $amount;
	protected $description;

    public function __construct() {
        $this->addType('is_income', 'boolean');
        $this->addType('amount', 'float');

        /* Defaults */
        $this->isIncome = 0;
        $this->date = date('Y-m-d');
    }

    public function getDateFormat($format) {
      $phpdate = strtotime($this->date);
      return date($format, $phpdate);
    }

		public function setDateFormat($date, $format) {

		}

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'group_title' => $this->groupTitle,
            'repeat' => $this->repeat,
            'is_income' => $this->isIncome,
            'date' => $this->getDateFormat('Y-m-d'),
            'amount' => $this->amount,
            'description' => $this->description
        ];
    }

}
