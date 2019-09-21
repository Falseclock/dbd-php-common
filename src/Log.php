<?php

namespace Falseclock\DBD\Common;

/** @noinspection PhpFullyQualifiedNameUsageInspection */

class Log extends \Logger
{
	public function debug($message, $throwable = null) {
		if (defined("DBD_USE_LOG")) {
			/** @noinspection PhpFullyQualifiedNameUsageInspection */
			$this->log(\LoggerLevel::getLevelDebug(), $message, $throwable);
		}
	}
}