<?php

namespace plugin\dutoland\pdoHelper\bean\filter\composite;

use plugin\dutoland\pdoHelper\bean\filter\groupBy\astract\APhGroupBy;
use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;
use plugin\dutoland\pdoHelper\bean\filter\limit\astract\APhLimit;
use plugin\dutoland\pdoHelper\bean\filter\orderBy\astract\APhOrderBy;
use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\bean\filter\where\PhWhereAnd;
use plugin\dutoland\pdoHelper\exception\PhFatalException;
use plugin\dutoland\pdoHelper\util\PhUtils;

class PhFilterComposite implements IPhFilter {

	/** @var APhWhere */
	private $where = null;

	/** @var APhGroupBy */
	private $groupBy = null;

	/** @var APhWhere */
	private $having = null;

	/** @var APhOrderBy */
	private $orderBy = null;

	/** @var APhLimit */
	private $limit = null;

	public function addWhere(APhWhere $where): void {
		if ($this->where === null)
			$this->where = $where;
		else
			$this->where = new PhWhereAnd(array($this->where, $where));
	}

	public function addHaving(APhWhere $having): void {
		if ($this->having === null)
			$this->having = $having;
		else
			$this->having = new PhWhereAnd(array($this->having, $having));
	}

	public function setOrderBy(APhOrderBy $orderBy, bool $allowOverride = false): void {
		if (!$allowOverride && $this->orderBy !== null)
			throw new PhFatalException('OrderBy Override.');
		$this->orderBy = $orderBy;
	}

	public function setGroupBy(APhGroupBy $groupBy, bool $allowOverride = false): void {
		if (!$allowOverride && $this->groupBy !== null)
			throw new PhFatalException('GroupBy Override.');
		$this->groupBy = $groupBy;
	}

	public function setLimit(APhLimit $limit, bool $allowOverride = false): void {
		if (!$allowOverride && $this->limit !== null)
			throw new PhFatalException('Limit Override.');
		$this->limit = $limit;
	}

	public function getKeyWord(): string {
		return '';
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		$phFilters = array();
		if ($this->where !== null)
			$phFilters[] = $this->where;
		if ($this->groupBy !== null)
			$phFilters[] = $this->groupBy;
		if ($this->having !== null)
			$phFilters[] = $this->having;
		if ($this->orderBy !== null)
			$phFilters[] = $this->orderBy;
		if ($this->limit !== null)
			$phFilters[] = $this->limit;

		return PhUtils::mergePhPreparedQueryFromPhFilters($phFilters, true);
	}
}