<?php

namespace plugin\dutoland\pdoHelper\bean\filter\where;

use plugin\dutoland\pdoHelper\bean\filter\where\astract\APhWhere;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;

class PhWhereAlwaysFalse extends APhWhere {
	public function getPhPreparedQuery(): PhPreparedQuery {
		return new PhPreparedQuery('1 = 0');
	}
}