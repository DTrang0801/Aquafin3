<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $roleMap = [
            'admin' => Role::ADMIN,
            'stockbeheerder' => Role::STOCKBEHEERDER,
            'technieker' => Role::TECHNIEKER,
        ];

        $roleIds = array_map(fn ($role) => $roleMap[strtolower($role)] ?? null, $roles);
        $roleIds = array_filter($roleIds);

        if (! $request->user() || ! in_array($request->user()->role_id, $roleIds)) {
            abort(403, 'Geen toegang.');
        }

        return $next($request);
    }
}
