<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response 
        {
            if (!auth()->check() || auth()->user()->role !== $role) {
                abort(403, 'Dostęp zabroniony. Twoje konto nie posiada wystarczających uprawnień do wyświetlenia tej sekcji.');
            }

            return $next($request);
        }
}
