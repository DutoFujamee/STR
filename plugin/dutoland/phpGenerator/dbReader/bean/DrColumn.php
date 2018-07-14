<?php

namespace plugin\dutoland\phpGenerator\dbReader\bean;

class DrColumn {
	public $columnName;
	public $isNullable;
	public $isUnique;
	public $isPrimary;
	public $defaultValue;
	public $type;
	public $maxStringLength;
	public $enumValues;

	public $beanAttributeName;
}