<?php

namespace plugin\dutoland\pdoHelper\bean\filter\orderBy\astract;

use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;

abstract class APhOrderBy implements IPhFilter {
	public function getKeyWord(): string {
		return 'ORDER BY';
	}
}