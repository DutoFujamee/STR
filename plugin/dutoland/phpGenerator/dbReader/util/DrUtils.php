<?php

namespace plugin\dutoland\phpGenerator\dbReader\util;

class DrUtils {
	public static function snakeCaseToCleanCamelCase(string $snakeCaseString, bool $upperCaseFirstLetter = true) {
		$cleanCamelCase = implode('', array_filter(explode(' ', ucwords(strtolower(preg_replace("/[^A-Za-z0-9]/", ' ', $snakeCaseString))))));
		while (strlen($cleanCamelCase) > 0 && is_int($cleanCamelCase[0]))
			$cleanCamelCase = substr($cleanCamelCase, 1);
		if (!$upperCaseFirstLetter)
			$cleanCamelCase[0] = strtolower($cleanCamelCase[0]);
		return $cleanCamelCase;
	}
}