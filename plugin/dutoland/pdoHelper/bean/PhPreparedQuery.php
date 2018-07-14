<?php

namespace plugin\dutoland\pdoHelper\bean;

class PhPreparedQuery {

	private $query;
	private $args;

	public function getQuery(): string {
		return $this->query;
	}

	public function getArgs(): array {
		return $this->args;
	}

	public function __construct(string $query = '', array $args = array()) {
		$this->query = $query;
		$this->args = $args;
	}

}