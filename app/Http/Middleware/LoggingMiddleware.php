<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoggingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isProduction = app()->environment('production');

        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'request_body' => $this->sanitizeBody($request),
            'response_status' => null,
            'response_body' => null,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];

        if (! $isProduction) {
            $response = $next($request);

            $logData['response_status'] = $response->getStatusCode();
            $logData['response_body'] = mb_substr($response->getContent(), 0, 1000);

            Log::create($logData);

            return $response;
        }

        Log::create($logData);

        return $next($request);
    }

    private function sanitizeBody(Request $request): ?string
    {
        $body = $request->except(['password', 'token']);

        return empty($body) ? null : json_encode($body);
    }
}
