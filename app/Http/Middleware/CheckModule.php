<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckModule
 */
class CheckModule
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = request()->route()->getName();
        //        dd($route);
        //        dd(Module::where('route',$route)->whereIsActive(1)->first());
        $activeRoutes = Module::whereRoute($route)->whereIsActive(1)->first();
        //        dd($activeRoutes);
        if (! $activeRoutes) {
            abort(404);
        }

        return $next($request);
    }
}
