<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
//        dd($exception->getMessage());

        if ($request->expectsJson()) {
            return response()->json([
                'statusCode' => 500,
                'statusMessage' => $exception->getFile().":".$exception->getLine()." ".$exception->getMessage(),
            ]);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        //dd($request->route()->getPrefix());

        if ($request->expectsJson() ||  strpos($request->route()->getPrefix(), "api") !== false) {
            return response()->json([
                'statusCode' => 401,
                'statusMessage' => "Unauthenticated.",
            ]);
//            return response()->json(['error' => 'Unauthenticated.'],401);
        }

        $guard = array_get($exception->guards(), 0);
        switch ($guard) {
            case 'admin': $login = 'admin.login';
                break;
            default: $login = 'login';
                break;
        }
        return redirect()->guest(route($login));
    }




}
