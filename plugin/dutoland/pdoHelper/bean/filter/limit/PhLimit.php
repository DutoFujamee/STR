<?php

namespace plugin\dutoland\pdoHelper\bean\filter\limit;

use plugin\dutoland\pdoHelper\bean\filter\limit\astract\APhLimit;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;

class PhLimit extends APhLimit {

	/** @var int */
	private $maxRow;

	/** @var int */
	private $offset;

	/**
	 * @param int $maxRow
	 * @param int $offset
	 */
	public function __construct(int $maxRow, int $offset = 0) {
		$this->maxRow = $maxRow;
		$this->offset = $offset;
	}

	public function getPhPreparedQuery(): PhPreparedQuery {
		return new PhPreparedQuery(
				$this->maxRow . ' OFFSET ' . $this->offset
		);
	}
}