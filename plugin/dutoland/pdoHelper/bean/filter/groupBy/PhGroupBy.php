<?php

namespace plugin\dutoland\pdoHelper\bean\filter\groupBy;

use plugin\dutoland\pdoHelper\bean\filter\groupBy\astract\APhGroupBy;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhSecurity;

class PhGroupBy extends APhGroupBy {

	/** @var string */
	private $columnName;

	/** @var bool */
	private $isAsc;

	/**
	 * @param string $columnName
	 * @param bool $isAsc
	 */
	public function __construct(string $columnName, bool $isAsc = true) {
		$this->columnName = $columnName;
		$this->isAsc = $isAsc;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return new PhPreparedQuery(
				PhSecurity::escapeMixedColumnName($this->columnName) . ' ' . ($this->isAsc ? 'ASC' : 'DESC')
		);
	}
}