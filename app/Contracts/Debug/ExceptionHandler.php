<?php

namespace App\Contracts\Debug;

use Exception;

interface ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e);
}
