<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MethodMiddleware
{
    private array $allowedMethods = ['GET', 'POST', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), $this->allowedMethods)) {
            return response()->json([
                'success' => false,
                'message' => "Método '{$request->method()}' no permitido. Use: GET, POST o DELETE.",
            ], 405);
        }

        return $next($request);
    }
}
