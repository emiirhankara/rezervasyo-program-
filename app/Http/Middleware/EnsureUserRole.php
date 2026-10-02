<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_unless($request->user()?->role === $role, 403);

        if ($role === 'organizer') {
            abort_unless($request->user()->organizer_status === 'active', 403, 'Organizatör hesabınız henüz etkinleştirilmedi.');
            $annualFeePaid = DB::table('organizer_fees')
                ->where('organizer_id', $request->user()->id)
                ->where('year', now()->year)
                ->where('status', 'paid')
                ->exists();
            abort_unless($annualFeePaid, 403, 'Bu yılın organizatör üyelik ücreti ödenmemiş.');
        }

        return $next($request);
    }
}