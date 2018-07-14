<?php

namespace plugin\dutoland\pdoHelper\bean\filter\groupBy\astract;

use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;

abstract class APhGroupBy implements IPhFilter {
	public function getKeyWord(): string {
		return 'GROUP BY';
	}
}