<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Always return JSON for API requests
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ]
            ], 422);
        }

        // For API routes, always return JSON
        if ($request->is('api/*') || $request->expectsJson()) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ERROR',
                    'message' => $e->getMessage()
                ]
            ], $status);
        }

        return parent::render($request, $e);
    }
}
