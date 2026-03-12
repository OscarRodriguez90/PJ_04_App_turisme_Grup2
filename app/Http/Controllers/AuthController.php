<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('geoturismo')->attempt($credentials)) {
            $request->session()->regenerate();

            $usuario = Auth::guard('geoturismo')->user();

            // Redirigir según el rol
            if ($usuario->esAdmin()) {
                return redirect()->route('admin.panel');
            }

            return redirect()->route('usuario.panel');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Mostrar formulario de registro.
     */
    public function showRegistro()
    {
        return view('auth.registro');
    }

    /**
     * Procesar el registro.
     */
    public function registro(Request $request)
    {
        $request->validate([
            'username'  => 'required|string|max:25|unique:tbl_usuarios,username',
            'nombre'    => 'required|string|max:25',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'email'     => 'required|email|max:100|unique:tbl_usuarios,email',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $usuario = Usuario::create([
            'username'  => $request->username,
            'nombre'    => $request->nombre,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'id_rol'    => 2, // Rol de usuario por defecto
        ]);

        Auth::guard('geoturismo')->login($usuario);

        return redirect()->route('usuario.panel');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::guard('geoturismo')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
