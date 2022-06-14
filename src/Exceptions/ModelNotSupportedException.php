<?php

namespace Core\Exceptions;

use Exception;

class ModelNotSupportedException extends Exception
{
    protected $message = "model not supported";
}
