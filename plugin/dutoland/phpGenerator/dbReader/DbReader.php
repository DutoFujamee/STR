<?php

namespace plugin\dutoland\phpGenerator\dbReader;

use plugin\dutoland\beanSerializer\BeanSerializer;
use plugin\dutoland\fileHelper\bean\FolderEntry;
use plugin\dutoland\pdoHelper\PdoHelper;
use plugin\dutoland\pdoHelper\util\PhSecurity;
use plugin\dutoland\pdoHelper\util\PhUtils;
use plugin\dutoland\phpGenerator\dbReader\bean\DrColumn;
use plugin\dutoland\phpGenerator\dbReader\bean\DrReference;
use plugin\dutoland\phpGenerator\dbReader\bean\DrTable;
use plugin\dutoland\phpGenerator\dbReader\bean\enum\DrColumnTypeEnum;
use plugin\dutoland\phpGenerator\dbReader\exception\DrMissingConfigurationException;
use plugin\dutoland\phpGenerator\dbReader\util\DrUtils;

class DbReader {

	/** @var PdoHelper */
	private $pdoHelper;

	/** @var DrTable[] */
	private $drTables;

	/** @var FolderEntry */
	private $folderEntry;

	public function __construct(PdoHelper $pdoHelper, string $dbBeanFolderPath) {
		$this->pdoHelper = $pdoHelper;
		$this->folderEntry = new FolderEntry($dbBeanFolderPath);

		$this->loadExistingDbBeans();
	}

	public function loadExistingDbBeans() {
		$fileEntries = $this->folderEntry->getFileEntries();

		foreach ($fileEntries as $fileEntry)
			$this->drTables[] = BeanSerializer::getObjectFromBeanSerializerPack(unserialize($fileEntry->getContent()));
	}

	public function testDisplayDie() {
		var_dump($this->drTables);
		die;
	}

	public function test() {
		self::testDisplayDie();
		set_time_limit(360);
		$this->updateDrTables(array(
			'mta\\docExchange' => array(
				'mta_test.edm_document',
				'mta_test.edm_document_revision'/*,
				'sup_doc_document',
				'sup_doc_revision_key',
				'sup_doc_revision_file',
				'sup_doc_revision_meta',
				'sup_doc_revision_data'*/
			)
		));
	}

	public function updateDrTables(array $mixedTableNamesByNamespace) {
		foreach ($mixedTableNamesByNamespace as $namespace => $mixedTableNames) {
			foreach ($mixedTableNames as $mixedTableName) {
				$drTable = $this->getDrTableFromTableName($mixedTableName, $namespace);
				$this->saveDrTable($drTable);
			}
		}
	}

	public function getDrTableFromTableName(string $mixedTableName, string $namespace = '') {
		$drTable = new DrTable();
		$drTable->tableName = PhUtils::getTableNameFromMixedTableName($mixedTableName);
		$drTable->schemaName = PhUtils::getSchemaNameFromMixedTableName($mixedTableName);
		$drTable->beanName = DrUtils::snakeCaseToCleanCamelCase($drTable->tableName);
		$namespaceParts = array();
		if ($namespace !== '')
			$namespaceParts[] = $namespace;
		if ($drTable->schemaName !== null)
			$namespaceParts[] = $drTable->schemaName;
		$drTable->beanNamespace = implode('\\', $namespaceParts);

		$rows = $this->pdoHelper->select("
			SHOW FIELDS FROM " . PhSecurity::escapeMixedTableName($mixedTableName) . "
		");

		$drTable->drColumns = array();
		foreach ($rows as $row) {
			$drColumn = new DrColumn();
			$this->completeCrColumnFromDbType($drColumn, $row['Type']);
			$drColumn->columnName = $row['Field'];
			$drColumn->beanAttributeName = DrUtils::snakeCaseToCleanCamelCase($row['Field'], false);
			$drColumn->defaultValue = $row['Default'];
			$drColumn->isNullable = $row['Null'] !== 'NO';
			$drColumn->isUnique = in_array($row['Key'], array('UNI', 'PRI'));
			$drColumn->isPrimary = $row['Key'] === 'PRI';
			$this->typeDefaultValue($drColumn);
			$drTable->drColumns[] = $drColumn;
		}

		$schemaToSearch = $drTable->schemaName !== null ? $drTable->schemaName : $this->pdoHelper->getActualSchemaName();
		$rows = $this->pdoHelper->select("
			SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
			FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
			WHERE REFERENCED_TABLE_NAME IS NOT NULL
				AND (
					(
						TABLE_SCHEMA = '" . PhSecurity::escapeSchemaName($schemaToSearch, false) . "'
						AND TABLE_NAME = '" . PhSecurity::escapeTableName($drTable->tableName, false) . "'
					) OR (
						REFERENCED_TABLE_SCHEMA = '" . PhSecurity::escapeSchemaName($schemaToSearch, false) . "'
						AND REFERENCED_TABLE_NAME = '" . PhSecurity::escapeTableName($drTable->tableName, false) . "'
					)
				)
			;
		");
		foreach ($rows as $row) {
			$drReference = new DrReference();
			$drReference->schema = $row['TABLE_SCHEMA'];
			$drReference->table = $row['TABLE_NAME'];
			$drReference->column = $row['COLUMN_NAME'];
			$drReference->referenceSchema = $row['REFERENCED_TABLE_SCHEMA'];
			$drReference->referenceTable = $row['REFERENCED_TABLE_NAME'];
			$drReference->referenceColumn = $row['REFERENCED_COLUMN_NAME'];

			$drReference->beanObjectName = DrUtils::snakeCaseToCleanCamelCase($drReference->referenceTable, false);
			$drReference->referenceBeanObjectName = DrUtils::snakeCaseToCleanCamelCase($drReference->table, false) . 's';

			if (($drTable->schemaName === null || $row['TABLE_SCHEMA'] == $drTable->schemaName) && $row['TABLE_NAME'] == $drTable->tableName)
				$drTable->references[] = $drReference;
			else
				$drTable->referencedBys[] = $drReference;
		}

		return $drTable;
	}

	private function saveDrTable(DrTable $drTable) {
		$this->drTables[] = $drTable;
		$this->folderEntry->createNewUniqueFile(serialize(BeanSerializer::getBeanSerializerPackFromObject($drTable)), 'serial');
	}

	private function typeDefaultValue(DrColumn $drColumn) {
		if ($drColumn->isNullable && $drColumn->defaultValue === null)
			return;
		switch ($drColumn->type) {
			case DrColumnTypeEnum::STRING:
			case DrColumnTypeEnum::INT:
				$drColumn->defaultValue = '' . $drColumn->defaultValue;
				break;
			case DrColumnTypeEnum::BOOLEAN:
				$drColumn->defaultValue = (bool) $drColumn->defaultValue;
				break;
			case DrColumnTypeEnum::DATE:
			case DrColumnTypeEnum::DATETIME:
				if ($drColumn->defaultValue)
					throw new DrMissingConfigurationException('Default value for Date / Datetime not yet implemented');
				$drColumn->defaultValue = null;
				break;
			case DrColumnTypeEnum::ENUM:
				$drColumn->defaultValue = (string) $drColumn->defaultValue;
				break;
			default:
				throw new DrMissingConfigurationException('Unknown DrColumnType: ' . $drColumn->type);
		}
	}

	private function completeCrColumnFromDbType(DrColumn $drColumn, string $dbType) {
		$content = '';
		if (strpos($dbType, '(') !== false) {
			$content = substr($dbType, strpos($dbType, '(') + 1, -1);
			$dbType = substr($dbType, 0, strpos($dbType, '('));
		}
		switch ($dbType) {
			case 'int':
				$drColumn->type = DrColumnTypeEnum::INT;
				break;
			case 'varchar':
			case 'text':
				$drColumn->type = DrColumnTypeEnum::STRING;
				$drColumn->maxStringLength = $content;
				break;
			case 'enum':
				$drColumn->type = DrColumnTypeEnum::ENUM;
				$drColumn->enumValues = array();
				foreach (explode(',', $content) as $enumValuePart)
					$drColumn->enumValues[] = substr(trim($enumValuePart), 1, -1);
				break;
			case 'tinyint':
				if ($content == 1)
					$drColumn->type = DrColumnTypeEnum::BOOLEAN;
				else
					$drColumn->type = DrColumnTypeEnum::INT;
				break;
			case 'datetime':
				$drColumn->type = DrColumnTypeEnum::DATETIME;
				break;
			case 'date':
				$drColumn->type = DrColumnTypeEnum::DATE;
				break;
			default:
				throw new DrMissingConfigurationException('Unknown dbType: ' . $dbType . ' (content: ' . $content . ')');
		}

	}

}