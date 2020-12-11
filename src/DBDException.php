<?php

namespace DBD\Common;

use Exception;
use Throwable;

class DBDException extends Exception
{
    /** @var string $query */
    protected $query;
    /** @var array $trace */
    protected $trace;
    /** @var array $fullTrace */
    protected $fullTrace;
    /** @var array $shortTrace */
    protected $shortTrace;
    /** @var string $message */
    protected $message;
    /** @var int $code */
    protected $code;
    /** @var string $file */
    protected $file;
    /** @var int $line */
    protected $line;
    /** @var array $arguments */
    protected $arguments;

    /**
     * DBDException constructor.
     *
     * @param string $message
     * @param string|null $query
     * @param null $arguments
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", string $query = null, $arguments = null, Throwable $previous = null)
    {
        $this->query = $query;
        $this->message = $message;
        $this->code = E_ERROR;
        $this->arguments = $arguments;

        $backTrace = parent::getTrace();
        $this->fullTrace = $backTrace;

        foreach ($backTrace as $trace) {
            if (isset($trace['file'])) {
                $pathInfo = pathinfo($trace['file']);
                if ($pathInfo['basename'] == "DBD.php") {
                    array_shift($backTrace);
                    continue;
                } else {
                    break;
                }
            }
        }
        $this->file = $backTrace[0]['file'];
        $this->line = $backTrace[0]['line'];

        $this->shortTrace = $backTrace;

        parent::__construct($message, $this->code, $previous);
    }

    /**
     * @return array|null
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return array
     */
    public function getFullTrace()
    {
        return $this->fullTrace;
    }

    /**
     * @return string|null
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getShortTrace()
    {
        return $this->shortTrace;
    }
}