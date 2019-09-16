<?php

namespace Falseclock\DBD\Common;

use DBD\DBD;
use DBD\Pg;
use Falseclock\DBD\Common\DBDException as Exception;
use Falseclock\DBD\Entity\Table;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;

class Utils
{
	/**
	 * Converts to Camel Case
	 *
	 * @param      $string
	 * @param bool $capitalizeFirstCharacter
	 *
	 * @return string
	 */
	public static function dashesToCamelCase($string, $capitalizeFirstCharacter = false) {
		$str = str_replace('_', '', ucwords($string, '_'));

		if(!$capitalizeFirstCharacter) {
			$str = lcfirst($str);
		}

		return $str;
	}

	/**
	 * Returns structure of table
	 *
	 * @param DBD    $db
	 * @param string $table
	 * @param string $scheme
	 *
	 * @return Table
	 * @throws Exception
	 * @throws InvalidArgumentException
	 * @throws ReflectionException
	 */
	public static function tableStructure(DBD $db, string $table, string $scheme) {
		switch(true) {
			case $db instanceof Pg:
				$utils = new PgUtils($db);

				return $utils->tableStructure($table, $scheme);
				break;
			default:
				throw new Exception("Not implemented for this driver yet");
		}
	}
}