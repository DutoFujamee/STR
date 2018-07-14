<?php

namespace plugin\dutoland\pdoHelper\bean\filter\limit\astract;

use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;

abstract class APhLimit implements IPhFilter {
	public function getKeyWord(): string {
		return 'LIMIT';
	}
}