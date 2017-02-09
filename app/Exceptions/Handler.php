<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;

//low: API のみを前提にしてしまっているが web の考慮も加える
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
        $e = $this->prepareException($e);
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            $message = (new \ReflectionClass($e))->getShortName();
            Log::warning($message);
            $statusCode = $e->getStatusCode();
            return response()->json(['message' => $message, 'status' => $statusCode], $statusCode);
        } else if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
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
            Log::warning($errors);
            return response()->json($errors, 400);
        } else if ($e instanceof \Illuminate\Validation\ValidationException) {
            $errors = $e->validator->errors()->getMessages();
            return response()->json($errors, 400);
        } else {
            Log::error($e);
            return response()->json(['message' => 'Internal Server Error', 'status' => 500], 500);
        }
    }
}
