<?php

namespace Core\Traits\Provider;

use App\Exceptions\Handler;
use Core\Exceptions\CoreHandler;

trait Exception
{

    /**
     * binding global exception handler with new custom handler in the service container
     *
     * @return void
     * @author WeSSaM
     */
    function bindingCustomException()
    {
        $this->app->bind(
            Handler::class,
            CoreHandler::class
        );
    }

}
