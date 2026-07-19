<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCustomerDeleteMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $dni = $request->route('dni') ?? $request->query('dni');

        if (empty($dni)) {
            return response()->json([
                'success' => false,
                'message' => 'El parámetro dni es obligatorio.',
            ], 422);
        }

        return $next($request);
    }
}
