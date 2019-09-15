<?php

namespace Falseclock\DBD\Common;

use ReflectionClass;
use ReflectionException;

class Enforcer
{
	/**
	 * @param $class
	 * @param $c
	 *
	 * @throws DBDException
	 * @throws ReflectionException
	 */
	public static function __add($class, $c) {
		$reflection = new ReflectionClass($class);
		$constantsForced = $reflection->getConstants();
		foreach($constantsForced as $constant => $value) {
			if(constant("$c::$constant") == "abstract") {
				throw new DBDException("Undefined $constant in " . (string) $c);
			}
		}
	}
}