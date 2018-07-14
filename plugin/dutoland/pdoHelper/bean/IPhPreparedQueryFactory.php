<?php

namespace plugin\dutoland\pdoHelper\bean;

interface IPhPreparedQueryFactory {
	public function getPhPreparedQuery(): PhPreparedQuery;
}