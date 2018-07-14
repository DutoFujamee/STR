<?php

namespace plugin\dutoland\pdoHelper\bean\filter;

use plugin\dutoland\pdoHelper\bean\IPhPreparedQueryFactory;

interface IPhFilter extends IPhPreparedQueryFactory {
	public function getKeyWord(): string;
}