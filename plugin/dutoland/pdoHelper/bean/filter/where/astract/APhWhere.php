<?php

namespace plugin\dutoland\pdoHelper\bean\filter\where\astract;

use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;

abstract class APhWhere implements IPhFilter {
	public function getKeyWord(): string {
		return 'WHERE';
	}
}