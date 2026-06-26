<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $allowed = array_map(fn (string $role): string => UserRole::from($role)->value, $roles);

        if (! collect($allowed)->contains(fn (string $r) => in_array($r, $user->roles ?? [], strict: true))) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
