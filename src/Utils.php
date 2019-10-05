<?php

namespace Falseclock\DBD\Common;

use Falseclock\DBD\Common\DBDException as Exception;
use Falseclock\DBD\DBD;
use Falseclock\DBD\Entity\Table;
use Falseclock\DBD\Pg;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;

class Utils
{
	/**
	 * Converts to Camel Case
	 *
	 * @param       $string
	 * @param bool  $capitalizeFirstCharacter
	 *
	 * @param array $search
	 *
	 * @return string
	 */
	public static function toCamelCase($string, $capitalizeFirstCharacter = false, array $search = [ '_' ]) {
		$str = str_replace($search, '', ucwords($string, implode("", $search)));

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