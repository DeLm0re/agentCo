<?php

namespace App\Controller\Traits;

trait ExceptionHandlerTrait
{
    /**
     * @param \Exception $exception
     *
     * @return string
     */
    protected function getDefaultErrorMsg(\Exception $exception): string
    {
        return "Exception : Message => '{$exception->getMessage()}',
            File => '{$exception->getFile()}', Line => '{$exception->getLine()}'.";
    }
}
