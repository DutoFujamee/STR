<?php

namespace plugin\dutoland\pdoHelper\util;

use plugin\dutoland\pdoHelper\exception\PhFatalException;

class PhSecurity {

	private const REGEX_SCHEMA_NAME = '/[^a-z0-9_]/';
	private const REGEX_TABLE_NAME = '/[^a-z0-9_]/';
	private const REGEX_COLUMN_NAME = '/[^a-z0-9_]/';
	private const REGEX_ALIAS_NAME = '/[^a-z0-9_]/';

	private static function escapeName(string $name, string $description, string $regex, bool $addBackQuote = true) {
		$safeName = preg_replace($regex, '', $name);
		if ($safeName != $name)
			throw new PhFatalException('Invalid ' . $description . ' Name: "' . $name . '"');
		return $addBackQuote ? '`' . $safeName . '`' : $safeName;
	}

	public static function escapeSchemaName(string $schemaName, bool $addBackQuote = true) {
		return self::escapeName($schemaName, 'Schema', self::REGEX_SCHEMA_NAME, $addBackQuote);
	}

	public static function escapeTableName(string $tableName, bool $addBackQuote = true) {
		return self::escapeName($tableName, 'Table', self::REGEX_TABLE_NAME, $addBackQuote);
	}

	public static function escapeColumnName(string $columnName) {
		return self::escapeName($columnName, 'Column', self::REGEX_COLUMN_NAME);
	}

	public static function escapeAliasName(string $aliasName) {
		return self::escapeName($aliasName, 'Alias', self::REGEX_ALIAS_NAME);
	}

	public static function escapeMySqlOperator(string $operator) {
		$acceptedOperators = array(
			'<',
			'<=',
			'>',
			'>=',
			'<>',
			'=',
			'!=',
			'LIKE',
			'NOT LIKE'
		);
		if (!array_key_exists(strtoupper($operator), array_flip($acceptedOperators)))
			throw new PhFatalException('Invalid Operator: "' . $operator . '"');
		return $operator;
	}

	public static function escapeMixedTableName(string $mixedName) {
		$safeFirstName = null;
		$safeSecondName = self::escapeColumnName(PhUtils::getTableNameFromMixedTableName($mixedName));

		$firstName = PhUtils::getSchemaNameFromMixedTableName($mixedName);
		if ($firstName !== null)
			$safeFirstName = self::escapeAliasName($firstName);

		return ($safeFirstName !== null ? $safeFirstName . '.' : '') . $safeSecondName;
	}

	public static function escapeMixedColumnName(string $mixedName) {
		$parts = explode('.', $mixedName);
		if (count($parts) > 2)
			throw new PhFatalException('Mixed Column Name can\'t contains more than one "."');

		$safeFirstName = null;
		$safeSecondName = self::escapeColumnName(array_pop($parts));

		$firstName = array_pop($parts);
		if ($firstName !== null)
			$safeFirstName = self::escapeAliasName($firstName);

		return ($safeFirstName !== null ? $safeFirstName . '.' : '') . $safeSecondName;
	}

	public static function escapeSchemaNames(array $names) {
		$safeNames = array();
		foreach ($names as $key => $name)
			$safeNames[$key] = self::escapeSchemaName($name);
		return $safeNames;
	}

	public static function escapeTableNames(array $names) {
		$safeNames = array();
		foreach ($names as $key => $name)
			$safeNames[$key] = self::escapeTableName($name);
		return $safeNames;
	}

	public static function escapeColumnNames(array $names) {
		$safeNames = array();
		foreach ($names as $key => $name)
			$safeNames[$key] = self::escapeColumnName($name);
		return $safeNames;
	}

	public static function escapeAliasNames(array $names) {
		$safeNames = array();
		foreach ($names as $key => $name)
			$safeNames[$key] = self::escapeAliasName($name);
		return $safeNames;
	}

	public static function escapeMixedTableNames(array $names) {
		$safeNames = array();
		foreach ($names as $key => $name)
			$safeNames[$key] = self::escapeMixedTableName($name);
		return $safeNames;
	}

	public static function escapeMixedColumnNames(array $names) {
		$safeNames = array();
		foreach ($names as $key => $name)
			$safeNames[$key] = self::escapeMixedColumnName($name);
		return $safeNames;
	}
}