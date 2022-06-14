<?php

namespace Core\Exceptions;

use Exception;

class DeleteException extends Exception
{
    protected $message = "error_while_delete";
}
