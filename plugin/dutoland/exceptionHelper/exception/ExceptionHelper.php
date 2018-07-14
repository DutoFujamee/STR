<?php

namespace plugin\dutoland\exceptionHelper\exception;

use Exception;
use Throwable;

class ExceptionHelper extends Exception {

	public function __construct($message = '', $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	public static function decorate(Exception $exception) {
		return new self($exception->getMessage(), $exception->getCode(), $exception);
	}

}