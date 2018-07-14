<?php

namespace plugin\dutoland\beanSerializer\bean;

class BeanSerializerPack {

	public $beanName;
	public $valueByPropertyName;

	public function __construct(string $beanName, array $valueByPropertyName = array()) {
		$this->beanName = $beanName;
		$this->valueByPropertyName = $valueByPropertyName;
	}

}