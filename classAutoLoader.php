<?php

function classAutoLoader($class) {
	if (strpos($class, 'plugin\\dutoland\\') === 0) {
		include(BASE_PATH . 'plugin' . DIRECTORY_SEPARATOR . 'dutoland' . DIRECTORY_SEPARATOR . str_replace('\\', '/', substr($class, 16)) . '.php');
		return;
	}

	include(BASE_PATH . str_replace('\\', '/', $class) . '.php');
}
spl_autoload_register('classAutoLoader');

