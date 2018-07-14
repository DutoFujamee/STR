<?php

namespace plugin\dutoland\pdoHelper\bean\filter\where;

use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhSecurity;

class PhWhereOperator extends APhWhere {

	/** @var string */
	private $columnName;

	/** @var string */
	private $operator;

	/** @var string */
	private $value;

	/**
	 * @param string $columnName
	 * @param string $operator
	 * @param string $value
	 */
	public function __construct(string $columnName, string $operator, ?string $value = null) {
		$this->columnName = $columnName;
		$this->operator = $operator;
		$this->value = $value;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return new PhPreparedQuery(
				PhSecurity::escapeMixedColumnName($this->columnName)
						. ' ' . PhSecurity::escapeMySqlOperator($this->operator)
						. ($this->value === null ? '' : ' ?'),
				$this->value === null ? array() : array($this->value)
		);
	}
}