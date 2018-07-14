<?php

namespace plugin\dutoland\pdoHelper\bean\filter\groupBy;

use plugin\dutoland\pdoHelper\bean\filter\groupBy\astract\APhGroupBy;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\util\PhUtils;

class PhGroupByComposite extends APhGroupBy {

	/** @var APhGroupBy[] */
	private $groupBys;

	/**
	 * @param APhGroupBy[] $groupBys
	 */
	public function __construct(array $groupBys) {
		$this->groupBys = $groupBys;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return PhUtils::mergePhPreparedQueryFromPhFilters($this->groupBys, false, ', ');
	}
}