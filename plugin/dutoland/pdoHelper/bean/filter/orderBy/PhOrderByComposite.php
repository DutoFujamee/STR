<?php

namespace plugin\dutoland\pdoHelper\bean\filter\orderBy;

use plugin\dutoland\pdoHelper\bean\filter\orderBy\astract\APhOrderBy;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhUtils;

class PhOrderByComposite extends APhOrderBy {

	/** @var APhOrderBy[] */
	private $orderBys;

	/**
	 * @param APhOrderBy[] $orderBys
	 */
	public function __construct(array $orderBys) {
		$this->orderBys = $orderBys;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return PhUtils::mergePhPreparedQueryFromPhFilters($this->orderBys, false, ', ');
	}
}