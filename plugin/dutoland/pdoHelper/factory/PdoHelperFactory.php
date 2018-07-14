<?php

namespace plugin\dutoland\pdoHelper\factory;

use Exception;
use PDO;
use plugin\dutoland\pdoHelper\exception\PhFatalException;
use plugin\dutoland\pdoHelper\PdoHelper;

class PdoHelperFactory {

	public static function constructPdoHelper(
			string $mySqlUrl,
			string $userName,
			string $password,
			?int $mySqlPort = null,
			?string $dbName = null,
			array $options = array()
	) {
		try {
			$dsnParts = array('host' => $mySqlUrl);
			if ($mySqlPort !== null)
				$dsnParts['port'] = $mySqlPort;
			if ($dbName !== null)
				$dsnParts['dbname'] = $dbName;

			foreach ($dsnParts as $key => $dsnPart)
				$dsnParts[$key] = $key . '=' . $dsnPart;

			return new PdoHelper(new PDO(
					'mysql:' . implode(';', $dsnParts),
					$userName,
					$password,
					$options
			));
		} catch (Exception $exception) {
			throw PhFatalException::decorate($exception);
		}
	}
}