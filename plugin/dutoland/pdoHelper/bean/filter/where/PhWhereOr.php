<?php

namespace plugin\dutoland\pdoHelper\bean\filter\where;

use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhUtils;

class PhWhereOr extends APhWhere {

	/** @var APhWhere[] */
	private $wheres = array();

	/** @param APhWhere[] $wheres */
	public function __construct(array $wheres) {
		$this->wheres = $wheres;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return PhUtils::mergePhPreparedQueryFromPhFilters($this->wheres, false, ') OR (', '(', ')');
	}
}