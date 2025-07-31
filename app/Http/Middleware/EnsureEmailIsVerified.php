<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->email_verificado_em) {
            return response()->json([
                'message' => 'Por favor verifique seu email para acessar este recurso.',
                'verified' => false
            ], 403);
        }

        return $next($request);
    }
}
