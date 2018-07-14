<?php

use plugin\dutoland\pdoHelper\bean\filter\where\PhWhereOperator;
use plugin\dutoland\pdoHelper\factory\PdoHelperFactory;
const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR;
include_once (BASE_PATH . 'classAutoLoader.php');

$pdoHelper = PdoHelperFactory::constructPdoHelper('127.0.0.1', 'mta_apps', 'mta', null, 'mta_test', array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_AUTOCOMMIT => 0
));
$dbReader = new \plugin\dutoland\phpGenerator\dbReader\DbReader($pdoHelper, BASE_PATH . 'tmp');
$dbReader->test();
