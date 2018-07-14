<?php

namespace plugin\dutoland\phpGenerator;

use plugin\dutoland\pdoHelper\PdoHelper;

class PhpGenerator {

	/** @var PdoHelper */
	private $pdoHelper;

	public function __construct(PdoHelper $pdoHelper) {
		$this->pdoHelper = $pdoHelper;
	}

	public function generate() {
		var_dump('test');
	}
}