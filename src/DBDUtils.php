<?php
/**
 * DBDUtils
 *
 * @author    Nurlan Mukhanov <nurike@gmail.com>
 * @copyright 2020 Nurlan Mukhanov
 * @license   https://en.wikipedia.org/wiki/MIT_License MIT License
 * @link      https://github.com/Falseclock/dbd-common
 */

declare(strict_types=1);

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
	public static function getPrimitive(string $type): Primitive;

	/**
	 * @param Table $table
	 *
	 * @return Constraint[]
	 */
	function getTableConstraints(Table $table): array;

	/**
	 * @param string $tableName
	 * @param string $schemaName
	 *
	 * @return Table
	 */
	function tableStructure(string $tableName, string $schemaName): Table;
}
