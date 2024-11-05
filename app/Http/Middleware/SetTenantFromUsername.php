<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Resolvers\MultiTenantResolver;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\IdentificationMiddleware;
use Stancl\Tenancy\Tenancy;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SetTenantFromUsername
 */
class SetTenantFromUsername extends IdentificationMiddleware
{
    /** @var callable|null */
    public static $onFail;

    /** @var Tenancy */
    protected $tenancy;

    /** @var MultiTenantResolver */
    protected $resolver;

    public function __construct(Tenancy $tenancy, MultiTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = User::whereRaw('lower(username) = ?', strtolower($request->route('username')))->firstOrfail();

        return $this->initializeTenancy(
            $request, $next, $user->tenant_id
        );
    }
}
