<?php

namespace plugin\dutoland\pdoHelper\bean\filter\where;

use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhSecurity;

class PhWhereCompare extends APhWhere {

	/** @var string */
	private $columnName1;

	/** @var string */
	private $operator;

	/** @var string */
	private $columnName2;

	/**
	 * @param string $columnName1
	 * @param string $operator
	 * @param string $columnName2
	 */
	public function __construct(string $columnName1, string $operator, string $columnName2) {
		$this->columnName1 = $columnName1;
		$this->operator = $operator;
		$this->columnName2 = $columnName2;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return new PhPreparedQuery(
				PhSecurity::escapeMixedColumnName($this->columnName1)
				. ' ' . PhSecurity::escapeMySqlOperator($this->operator)
				. ' ' . PhSecurity::escapeMixedColumnName($this->columnName2)
		);
	}
}