<?php

namespace DBD\Common;

use DBD\DBD;
use DBD\Entity\Constraint;
use DBD\Entity\Primitive;
use DBD\Entity\Table;

interface DBDUtils
{
	/**
	 * DBDUtils constructor.
	 *
	 * @param DBD $dbDriver
	 */
	public function __construct(DBD $dbDriver);

	/**
	 * @param string $type
	 *
	 * @return Primitive
	 */
	public static function getPrimitive(string $type);

	/**
	 * @param Table $table
	 *
	 * @return Constraint[]
	 */
	function getTableConstraints(Table $table);

	/**
	 * @param string $tableName
	 * @param string $schemaName
	 *
	 * @return Table
	 */
	function tableStructure(string $tableName, string $schemaName);
}