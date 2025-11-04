<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartmentHead
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isDepartmentHead()) {
            abort(403, 'Faqat kafedra mudirlari uchun ruxsat berilgan!');
        }

        if (!$user->teacher || !$user->teacher->department_id) {
            abort(403, 'Siz kafedra mudiriga biriktirilmagansiz!');
        }

        return $next($request);
    }
}
