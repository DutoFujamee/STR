<?php

namespace plugin\dutoland\pdoHelper;

use PDO;
use plugin\dutoland\pdoHelper\bean\filter\composite\PhFilterComposite;
use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;
use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\exception\PhFatalException;
use plugin\dutoland\pdoHelper\util\PhSecurity;

class PdoHelper {

	/** @var PDO */
	private $pdo;

	/** @var int */
	private $totalTransactionStillOpen = 0;

	/** @var bool */
	private $isRollBacking = false;

	/** @var PDO */
	private $schemaName;

	/**
	 * @param PDO $pdo
	 */
	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
		$this->schemaName = $this->select("SELECT DATABASE() AS db")[0]['db'];
	}

	public function __destruct() {
		if ($this->totalTransactionStillOpen !== 0)
			throw new PhFatalException('There are open transactions left');
	}

	public function getActualSchemaName() {
		return $this->schemaName;
	}

	private function exec(string $query, array $args = array(), bool $returnFetchedResult = false) {
		$preparedStatement = $this->pdo->prepare($query);
		$preparedStatement->execute($args);
		if ($returnFetchedResult)
			return $preparedStatement->fetchAll(PDO::FETCH_ASSOC);
		return null;
	}

	public function startTransaction() {
		if ($this->isRollBacking)
			throw new PhFatalException('You can\'t start new transaction before all rollbacks are done');
		if ($this->totalTransactionStillOpen === 0)
			$this->exec("START TRANSACTION");
		$this->totalTransactionStillOpen++;
	}

	public function commitTransaction() {
		if ($this->isRollBacking)
			throw new PhFatalException('You can\'t commit transaction before all rollbacks are done');
		if ($this->totalTransactionStillOpen === 1)
			$this->exec("COMMIT");
		$this->totalTransactionStillOpen--;
		if ($this->totalTransactionStillOpen < 0)
			throw new PhFatalException('You commited more transaction that you actually opened !');
	}

	public function rollbackTransaction() {
		if ($this->totalTransactionStillOpen === 0)
			throw new PhFatalException('Trying to rollback transaction, but there is none left open');
		$this->exec("ROLLBACK");
		$this->totalTransactionStillOpen--;
		$this->isRollBacking = $this->totalTransactionStillOpen > 0;
		if ($this->totalTransactionStillOpen < 0)
			throw new PhFatalException('You roll-backed more transaction that you actually opened !');
	}

	public function use(string $schemaName) {
		$this->exec("USE " . PhSecurity::escapeSchemaName($schemaName));
		$this->schemaName = PhSecurity::escapeSchemaName($schemaName, false);
	}

	public function insert(string $mixedTableName, array $valueByMixedColumnName) {
		if (count($valueByMixedColumnName) === 0)
			throw new PhFatalException('Trying to insert with empty data in ' . $mixedTableName);

		$query = "
			INSERT INTO " . PhSecurity::escapeMixedTableName($mixedTableName)
				. " (" . implode(', ', PhSecurity::escapeMixedColumnNames(array_keys($valueByMixedColumnName))) . ")
			VALUES (" . implode(', ', array_fill(0, count($valueByMixedColumnName), '?')) . ")
		";

		$this->exec($query, array_values($valueByMixedColumnName));
		return $this->pdo->lastInsertId();
	}

	public function update(string $mixedTableName, array $valueByMixedColumnName, APhWhere $phWhere) {
		if (count($valueByMixedColumnName) === 0)
			throw new PhFatalException('Trying to update with empty data in ' . $mixedTableName);

		$phPreparedQueryWhere = $phWhere->getPhPreparedQuery();

		$setParts = array();
		foreach ($valueByMixedColumnName as $mixedColumnName => $value)
			$setParts[] = PhSecurity::escapeMixedColumnName($mixedColumnName) . " = ?";

		$query = "
			UPDATE " . PhSecurity::escapeMixedTableName($mixedTableName) . "
			SET " . implode(', ', $setParts) . "
			" . $phWhere->getKeyWord() . " " . $phPreparedQueryWhere->getQuery() . "
		";

		$this->exec($query, array_merge(array_values($valueByMixedColumnName), $phPreparedQueryWhere->getArgs()));
	}

	public function delete(string $mixedTableName, APhWhere $phWhere) {
		$phPreparedQueryWhere = $phWhere->getPhPreparedQuery();

		$query = "
			DELETE FROM " . PhSecurity::escapeMixedTableName($mixedTableName) . "
			" . $phWhere->getKeyWord() . " " . $phPreparedQueryWhere->getQuery() . "
		";

		$this->exec($query, $phPreparedQueryWhere->getArgs());
	}

	/**
	 * @param string $selectFromString
	 * @param IPhFilter $phFilter
	 * @return array
	 */
	public function select(string $selectFromString, ?IPhFilter $phFilter = null) {
		if ($phFilter === null)
			$phFilter = new PhFilterComposite();

		$phPreparedQuery = $phFilter->getPhPreparedQuery();
		$keyWord = $phFilter->getKeyWord();

		$query = $selectFromString . ' ' . ($keyWord !== '' ? $keyWord . ' ' : '') . $phPreparedQuery->getQuery();
		return $this->exec($query, $phPreparedQuery->getArgs(), true);
	}
}