<?php

namespace Core\Exceptions;

use Exception;

class UploadingException extends Exception
{
    protected $message = "error_while_uploading";
}
