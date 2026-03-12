<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Introduce un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return back()
                ->withErrors(['credenciales' => 'Email o contraseña incorrectos.'])
                ->withInput($request->only('email'));
        }

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre'    => ['required', 'string', 'min:3', 'max:25'],
            'apellido1' => ['required', 'string', 'min:3', 'max:50'],
            'username'  => ['required', 'string', 'min:3', 'max:25', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:tbl_usuarios,username'],
            'email'     => ['required', 'email', 'max:100', 'unique:tbl_usuarios,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'nombre.min'         => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'         => 'El nombre no puede superar 25 caracteres.',
            'apellido1.required' => 'El apellido es obligatorio.',
            'apellido1.min'      => 'El apellido debe tener al menos 3 caracteres.',
            'apellido1.max'      => 'El apellido no puede superar 50 caracteres.',
            'username.required'  => 'El nombre de usuario es obligatorio.',
            'username.min'       => 'El usuario debe tener al menos 3 caracteres.',
            'username.max'       => 'El usuario no puede superar 25 caracteres.',
            'username.regex'     => 'El usuario solo puede contener letras, números y guión bajo.',
            'username.unique'    => 'Este nombre de usuario ya está en uso.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Introduce un correo electrónico válido.',
            'email.unique'       => 'Este correo ya tiene una cuenta registrada.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        Usuario::create([
            'nombre'    => $request->nombre,
            'apellido1' => $request->apellido1,
            'apellido2' => null,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'id_rol'    => 2,
        ]);

        return redirect()->route('login');
    }

    public function checkUsername(Request $request)
    {
        $exists = Usuario::where('username', $request->query('value', ''))->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkEmail(Request $request)
    {
        $exists = Usuario::where('email', $request->query('value', ''))->exists();
        return response()->json(['exists' => $exists]);
    }
}
