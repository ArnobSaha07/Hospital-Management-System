<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (getLoggedInUser()->hasRole('Super Admin')) {
            return redirect('super-admin/dashboard');
        }

        return $next($request);
    }
}
