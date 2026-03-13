<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    private const ROLE_MAP = [
        'admin' => 1,
        'cliente' => 2,
        'usuario' => 2,
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (!$usuario) {
            abort(403, 'No autorizado.');
        }

        $rolesPermitidos = array_map(function (string $rol): int {
            $rolNormalizado = strtolower(trim($rol));

            if (array_key_exists($rolNormalizado, self::ROLE_MAP)) {
                return self::ROLE_MAP[$rolNormalizado];
            }

            return (int) $rolNormalizado;
        }, $roles);

        if (!in_array((int) $usuario->id_rol, $rolesPermitidos, true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
