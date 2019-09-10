<?php
/*************************************************************************************
 *   MIT License                                                                     *
 *                                                                                   *
 *   Copyright (C) 2009-2019 by Nurlan Mukhanov <nurike@gmail.com>                   *
 *                                                                                   *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy    *
 *   of this software and associated documentation files (the "Software"), to deal   *
 *   in the Software without restriction, including without limitation the rights    *
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell       *
 *   copies of the Software, and to permit persons to whom the Software is           *
 *   furnished to do so, subject to the following conditions:                        *
 *                                                                                   *
 *   The above copyright notice and this permission notice shall be included in all  *
 *   copies or substantial portions of the Software.                                 *
 *                                                                                   *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR      *
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,        *
 *   FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE     *
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER          *
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,   *
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE   *
 *   SOFTWARE.                                                                       *
 ************************************************************************************/

namespace Falseclock\DBD\Common;

use Exception;

class DBDPHPException extends Exception
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