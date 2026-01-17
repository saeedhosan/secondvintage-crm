<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldForceJson($request, $response)) {
            return $response;
        }

        return $this->forceJsonResponse($response);
    }

    private function shouldForceJson(Request $request, Response $response): bool
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return true;
        }

        if ($request->is('api/*')) {
            return true;
        }

        if ($response instanceof JsonResponse) {
            return false;
        }

        return !empty($request->header('Accept')) &&  str_contains($request->header('Accept'), 'application/json');
    }

    private function forceJsonResponse(Response $response): JsonResponse
    {
        $originalContent = $response->getContent();
        $data = [];

        if (!empty($originalContent)) {
            $data = json_decode($originalContent, true) ?? ['content' => $originalContent];
        }

        return response()->json($data)
            ->setStatusCode($response->getStatusCode())
            ->withHeaders($response->headers->all());
    }
}
