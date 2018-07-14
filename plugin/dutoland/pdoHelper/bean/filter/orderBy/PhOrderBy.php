<?php

namespace plugin\dutoland\pdoHelper\bean\filter\orderBy;

use plugin\dutoland\pdoHelper\bean\filter\orderBy\astract\APhOrderBy;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhSecurity;

class PhOrderBy extends APhOrderBy {

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