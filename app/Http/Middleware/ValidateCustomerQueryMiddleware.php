<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCustomerQueryMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $dni = $request->query('dni');
        $email = $request->query('email');

        if (empty($dni) && empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Debe enviar al menos un parámetro: dni o email.',
            ], 422);
        }

        return $next($request);
    }
}
