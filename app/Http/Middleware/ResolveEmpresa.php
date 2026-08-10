<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveEmpresa
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $empresaId = session('empresa_id');

        if ($empresaId && $user->empresas()->where('empresa_id', $empresaId)->exists()) {
            return $next($request);
        }

        $primeraEmpresa = $user->empresas()->first();

        if ($primeraEmpresa) {
            session(['empresa_id' => $primeraEmpresa->id]);

            return $next($request);
        }

        if ($user->isSuperAdmin() && ! $request->routeIs('super-admin.*')) {
            return redirect()->route('super-admin.empresas.index');
        }

        auth()->logout();

        return redirect()->route('filament.admin.auth.login')
            ->with('error', 'No tenés una empresa asignada. Contactá al administrador.');
    }
}
