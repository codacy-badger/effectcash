<?php
namespace OCA\EffectCash\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Mapper;

class BudgetMapper extends Mapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'effectcash_budgets', '\OCA\EffectCash\Db\Budget');
    }

    public function find($id, $userId) {
        $sql = 'SELECT * FROM *PREFIX*effectcash_budgets WHERE id = ? AND user_id = ?';
        return $this->findEntity($sql, [$id, $userId]);
    }

    public function findAll($userId) {
        $sql = 'SELECT * FROM *PREFIX*effectcash_budgets WHERE user_id = ?';
        return $this->findEntities($sql, [$userId]);
    }

    public function findAllByTitle($userId, $title) {
        $sql = 'SELECT * FROM *PREFIX*effectcash_budgets WHERE user_id = ? AND LOWER(title) LIKE LOWER(?)';
        return $this->findEntities($sql, [$userId, '%'.$title.'%']);
    }

    public function findBetween($userId, $start, $end) {
      $sql = 'SELECT * FROM *PREFIX*effectcash_budgets WHERE user_id = ?'
        . 'AND (DATE(`budget_date`) BETWEEN ? AND ?)'
        . 'OR ((LEFT(`repeat`, 1) = "m" OR LEFT(`repeat`, 1) = "w" OR LEFT(`repeat`, 1) = "y") AND EXTRACT(YEAR_MONTH FROM `budget_date`) <= EXTRACT(YEAR_MONTH FROM NOW()))';
      return $this->findEntities($sql, [$userId, $start, $end]);
    }

    public function getGroups($userId, $trans) {
      $sql = 'SELECT * FROM *PREFIX*effectcash_budgets WHERE user_id = ? GROUP BY id, group_title';
      $budgets = $this->findEntities($sql, [$userId]);

      $groups = [
        $trans->t('Pets'),
        $trans->t('Spare time'),
        $trans->t('Car'),
        $trans->t('Clothing'),
        $trans->t('Live'),
        $trans->t('Insurance'),
        $trans->t('Food'),
        $trans->t('Other expenses'),
        $trans->t('Salary')
      ];

      foreach($budgets as &$budget) {
        $group_title = $budget->getGroupTitle();
        if (!in_array($group_title, $groups)) {
          $groups[] = $group_title;
        }
      }
      return $groups;
    }

}
