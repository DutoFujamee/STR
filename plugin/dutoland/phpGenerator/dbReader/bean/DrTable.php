<?php

namespace plugin\dutoland\phpGenerator\dbReader\bean;

use plugin\dutoland\beanSerializer\BeanSerializer;
use plugin\dutoland\phpGenerator\dbReader\bean\DrColumn;

class DrTable {

	public $schemaName;
	public $tableName;

	/** @var DrColumn[] */
	public $drColumns = null;

	/** @var DrReference[] */
	public $referencedBys = array();

	/** @var DrReference[] */
	public $references = array();

	public $beanName;
	public $beanNamespace;

}