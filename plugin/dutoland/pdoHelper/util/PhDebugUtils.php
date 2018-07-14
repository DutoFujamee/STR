<?php


namespace plugin\dutoland\pdoHelper\util;


use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\exception\PhFatalException;

class PhDebugUtils {
	public static function phPreparedQueryToSql(PhPreparedQuery $phPreparedQuery) {
		$query = $phPreparedQuery->getQuery();
		$args = $phPreparedQuery->getArgs();

		$queryParts = explode('?', $query);
		$queryPartCount = count($queryParts) - 1;

		if ($queryPartCount !== count($args)) {
			throw new PhFatalException(
					'Total arguments (' . implode(', ', $args) . ') differ from total needed arguments in query (' . $query . ')'
			);
		}

		for ($i = 0; $i < $queryPartCount; $i++)
			$queryParts[$i] .= "'" . $args[$i] . "'";

		return implode('', $queryParts);
	}
}