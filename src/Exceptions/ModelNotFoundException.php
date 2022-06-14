<?php

namespace Core\Exceptions;

use Exception;

class ModelNotFoundException extends Exception
{
    protected $message = "model not Found";
}
