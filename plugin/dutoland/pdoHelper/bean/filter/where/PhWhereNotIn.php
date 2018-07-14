<?php

namespace plugin\dutoland\pdoHelper\bean\filter\where;

use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhSecurity;

class PhWhereNotIn extends APhWhere {

	/** @var string */
	private $columnName;

	/** @var array */
	private $values;

	/**
	 * @param string $columnName
	 * @param array $values
	 */
	public function __construct(string $columnName, array $values) {
		$this->columnName = $columnName;
		$this->values = $values;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		if (count($this->values) === 0)
			return (new PhWhereAlwaysTrue())->getPhPreparedQuery();
		return new PhPreparedQuery(
				PhSecurity::escapeMixedColumnName($this->columnName) . ' NOT IN (' . implode(', ', array_fill(0, count($this->values), '?')) . ')',
				$this->values
		);
	}
}