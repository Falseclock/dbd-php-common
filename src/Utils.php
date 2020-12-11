<?php

namespace DBD\Common;

use DBD\Common\DBDException;
use DBD\DBD;
use DBD\Entity\Table;
use DBD\Pg;

class Utils
{
	/**
	 * Returns structure of table
	 *
	 * @param DBD    $db
	 * @param string $table
	 * @param string $scheme
	 *
	 * @return Table
	 * @throws DBDException
	 */
	public static function tableStructure(DBD $db, string $table, string $scheme) {
		switch(true) {
			case $db instanceof Pg:
				$utils = new PgUtils($db);

				return $utils->tableStructure($table, $scheme);
			default:
				throw new DBDException("Not implemented for this driver yet");
		}
	}

	/**
	 * Converts to Camel Case
	 *
	 * @param       $string
	 * @param bool  $capitalizeFirstCharacter
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
}