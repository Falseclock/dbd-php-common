<?php

namespace Falseclock\DBD\Common;

use Exception;

class DBDException extends Exception
{
    protected $query;
    protected $trace;
    protected $fullTrace;
    protected $shortTrace;
    protected $message;
    protected $code;
    protected $file;
    protected $line;

    public function __construct(string $message = "", $query = null) {
        $this->query = $query;
        $this->message = $message;
        $this->code = E_ERROR;

        $backTrace = parent::getTrace();
        $this->fullTrace = $backTrace;

        foreach($backTrace as $trace) {
            if(isset($trace['file'])) {
                $pathInfo = pathinfo($trace['file']);
                if($pathInfo['basename'] == "DBD.php") {
                    array_shift($backTrace);
                    continue;
                }
                else {
                    break;
                }
            }
        }
        $this->file = $backTrace[0]['file'];
        $this->line = $backTrace[0]['line'];

        $this->shortTrace = $backTrace;

        parent::__construct($message, $this->code);
    }

    public function getFullTrace() {
        return $this->fullTrace;
    }

    public function getQuery() {
        return $this->query;
    }

    public function getShortTrace() {
        return $this->shortTrace;
    }
}