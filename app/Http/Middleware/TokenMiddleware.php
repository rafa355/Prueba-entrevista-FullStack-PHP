<?php

namespace App\Http\Middleware;

use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tokenHeader = $request->header('Authorization');

        if (! $tokenHeader || ! str_starts_with($tokenHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado.',
            ], 401);
        }

        $plainToken = substr($tokenHeader, 7);

        $token = Token::where('token', $plainToken)
            ->where('status', 'A')
            ->first();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido.',
            ], 401);
        }

        if ($token->isExpired()) {
            $token->update(['status' => 'I']);

            return response()->json([
                'success' => false,
                'message' => 'Token vencido.',
            ], 401);
        }

        $request->merge(['token_data' => $token]);

        return $next($request);
    }
}
