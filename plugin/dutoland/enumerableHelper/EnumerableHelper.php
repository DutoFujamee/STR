<?php

namespace plugin\enumerable;

use Exception;
use ReflectionClass;

class EnumerableHelper {
	/**
	 * @param int $searchValue
	 * @return null|string
	 */
	public static function getKeyFromVal(int $searchValue): ?string {
		foreach (self::getValueByKey() as $key => $value) {
			if ($searchValue == $value)
				return $key;
		}
		return null;
	}

	/**
	 * @return int[]
	 */
	public static function getValueByKey(): array {
		return self::constructReflexionClass()->getConstants();
	}

	/**
	 * @param int $searchValue
	 * @param Exception $exceptionToThrow
	 */
	public static function throwIfNotExist(int $searchValue, ?Exception $exceptionToThrow = null) {
		if (self::getKeyFromVal($searchValue) === null) {
			if ($exceptionToThrow !== null)
				throw $exceptionToThrow;
			throw new EnumerableException('Unknown ' . static::class . ' Value: ' . $searchValue);
		}
	}

	/**
	 * @return ReflectionClass
	 */
	private static function constructReflexionClass() {
		return new ReflectionClass(static::class);
	}
}