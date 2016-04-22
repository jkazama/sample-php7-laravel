<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof HttpException) {
            Log::error($e);
            $statusCode = $e->getStatusCode();
            return response()->json(['message' => 'HttpException', 'status' => $statusCode], $statusCode);
        } else if ($e instanceof \App\Context\ValidationException) {
            $errors = [];
            foreach ($e->list() as $error) {
                $field = $error->field;
                $message = trans($error->message);
                if ($field) {
                    if (!array_key_exists($field, $errors)) {
                        $errors[$field] = [];
                    }
                    array_push($errors[$field], $message);
                } else {
                    if (!array_key_exists('', $errors)) {
                        $errors[''] = [];
                    }
                    array_push($errors[''], $message);
                }
            }
            Log::warn($errors);
            return response()->json($errors, 400);
        } else if ($e instanceof ValidationException) {
            return parent::render($request, $e);
        } else {
            Log::error($e);
            return response()->json(['message' => 'Internal Server Error', 'status' => 500], 500);
        }
    }
}
