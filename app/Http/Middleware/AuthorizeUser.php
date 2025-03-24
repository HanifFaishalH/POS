<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ... $role): Response
    {
        $user_role =$request->user()->getRole();

        if (in_array($user_role, $role)) {
            return $next($request);
        }

        abort(403, 'Forbidden. Kamu tidak punya akses ke halaman ini.');
    }
}
