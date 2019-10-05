<?php

namespace Falseclock\DBD\Common;

use Falseclock\DBD\DBD;
use Falseclock\DBD\Entity\Constraint;
use Falseclock\DBD\Entity\Primitive;
use Falseclock\DBD\Entity\Table;

interface DBDUtils
{
	public function __construct(DBD $dbDriver);

	/**
	 * @param string $type
	 *
	 * @return Primitive
	 */
	function getPrimitive(string $type);

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