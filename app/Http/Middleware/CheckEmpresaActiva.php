<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckEmpresaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $empresaId = session('empresa_id');

        if (! $empresaId) {
            return $next($request);
        }

        $empresa = Empresa::find($empresaId);

        if ($empresa && ! $empresa->tienePlanActivo()) {
            if ($request->routeIs('empresa-inactiva') || $request->routeIs('comprobante.store')) {
                return $next($request);
            }

            return redirect()->route('empresa-inactiva');
        }

        return $next($request);
    }
}
