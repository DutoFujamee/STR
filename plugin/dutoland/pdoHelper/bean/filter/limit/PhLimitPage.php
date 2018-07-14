<?php

namespace plugin\dutoland\pdoHelper\bean\filter\limit;

use plugin\dutoland\pdoHelper\bean\filter\limit\astract\APhLimit;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;

class PhLimitPage extends APhLimit {

	/** @var PhLimit */
	private $phLimit;

	/**
	 * @param int $page
	 * @param int $rowPerPage
	 */
	public function __construct(int $page, int $rowPerPage) {
		$this->phLimit = new PhLimit($rowPerPage, ($page - 1) * $rowPerPage);
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return $this->phLimit->getPhPreparedQuery();
	}
}