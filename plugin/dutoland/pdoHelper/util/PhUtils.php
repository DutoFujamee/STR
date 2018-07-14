<?php

namespace plugin\dutoland\pdoHelper\util;

use plugin\dutoland\pdoHelper\bean\filter\IPhFilter;
use plugin\dutoland\pdoHelper\bean\PhPreparedQuery;
use plugin\dutoland\pdoHelper\exception\PhFatalException;

class PhUtils {

	public static function mergePhPreparedQuery(
			PhPreparedQuery $phPreparedQuery1,
			PhPreparedQuery $phPreparedQuery2
	): PhPreparedQuery {
		$query1 = $phPreparedQuery1->getQuery();
		$query2 = $phPreparedQuery1->getQuery();

		$queryParts = array();
		if ($query1 != "")
			$queryParts[] = $query1;
		if ($query2 != "")
			$queryParts[] = $query2;
		return new PhPreparedQuery(
				implode(" ", $queryParts),
				array_merge($phPreparedQuery1->getArgs(), $phPreparedQuery2->getArgs())
		);
	}

	/**
	 * @param IPhFilter[] $phFilters
	 * @param bool $useKeyWord
	 * @param string $join
	 * @param string $prefix
	 * @param string $suffix
	 * @return PhPreparedQuery
	 */
	public static function mergePhPreparedQueryFromPhFilters(
			array $phFilters,
			bool $useKeyWord = false,
			string $join = '',
			string $prefix = '',
			string $suffix = ''
	): PhPreparedQuery {
		$queryStringParts = array();
		$args = array();

		foreach ($phFilters as $phFilter) {
			$phPreparedQuery = $phFilter->getPhPreparedQuery();
			$keyWord = $useKeyWord ? $phFilter->getKeyWord() : '';

			$queryStringParts[] = ($keyWord !== '' ? $keyWord . ' ' : '') . $phPreparedQuery->getQuery();
			$args = array_merge($args, $phPreparedQuery->getArgs());
		}

		return new PhPreparedQuery($prefix . implode($join, $queryStringParts) . $suffix, $args);
	}

	public static function getSchemaNameFromMixedTableName(string $mixedName) {
		$parts = explode('.', $mixedName);
		if (count($parts) > 2)
			throw new PhFatalException('Mixed Column Name can\'t contains more than one "."');

		return count($parts) === 1 ? null : $parts[0];
	}

	public static function getTableNameFromMixedTableName(string $mixedName) {
		$parts = explode('.', $mixedName);
		if (count($parts) > 2)
			throw new PhFatalException('Mixed Column Name can\'t contains more than one "."');

		return count($parts) === 1 ? $parts[0] : $parts[1];
	}

}