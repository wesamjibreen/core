<?php

namespace Core\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

use Throwable;
use function PHPUnit\Framework\isEmpty;

class CoreHandler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->isJson() || $request->is('api/*')) {
//            dd($exception);
            $message = $exception->getMessage() ?? trans("core::messages.unknown_error");
            $code = $exception->getCode() ?? SERVER_ERROR;
            $errors = [];
            switch (get_class($exception)) {
                case AuthenticationException::class:
                    $message = trans('Core::messages.un_authenticated');
                    $code = UNAUTHENTICATED_REQUEST;
                    break;
                case  ValidationException::class :
                    $errors = sanitize_validation_errors($exception->validator->errors());
                    $code = VALIDATION_RESPONSE;
                    break;

            }
            return response()->error($message, $code, $errors);
        }
        return parent::render($request, $exception);
    }

}
