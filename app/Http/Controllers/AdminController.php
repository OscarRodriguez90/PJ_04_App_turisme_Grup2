<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\Prueba;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Usuario::first(); // Demo purposes
        
        $stats = [
            'lugares' => Lugar::count(),
            'categorias' => Categoria::count(),
            'gimcanas' => Prueba::count(),
            'usuarios' => Usuario::count(),
        ];

        return view('admin.admin_dashboard', compact('stats', 'user'));
    }

    public function perfil()
    {
        // For now, since we don't have a real auth system yet in this task's scope
        // we'll fetch a default admin or the first user for demonstration.
        // If auth() is implemented, use auth()->user()
        $user = Usuario::first(); 
        
        return view('admin.mi_perfil', compact('user'));
    }

    public function updatePerfil(Request $request)
    {
        $user = Usuario::first(); // Demo purposes

        $request->validate([
            'username' => 'required|string|max:25|unique:tbl_usuarios,username,' . $user->id,
            'nombre' => 'required|string|max:25',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['username', 'nombre', 'apellido1', 'apellido2']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/usuarios'), $filename);
            $data['foto'] = $filename;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function lugares()
    {
        $user = Usuario::first();
        $categorias = Categoria::all();
        $lugaresGrouped = Lugar::with('categoria')->get()->groupBy(function($item) {
            return $item->categoria->nombre ?? 'Sin Categoría';
        });
        
        $totalLugares = Lugar::count();
        
        return view('admin.lugares', compact('lugaresGrouped', 'user', 'totalLugares', 'categorias'));
    }

    public function storeLugar(Request $request)
    {
        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'direccion_completa.required' => 'La dirección es obligatoria.',
            'latitud.required' => 'La latitud es obligatoria.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.required' => 'La longitud es obligatoria.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
            'id_categoria.required' => 'La categoría es obligatoria.',
            'id_categoria.exists' => 'La categoría seleccionada no es válida.',
            'numeric' => 'Este campo debe ser un número.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen.max' => 'La imagen no debe pesar más de 10MB.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion_completa' => 'required|string|max:255',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'id_categoria' => 'required|exists:tbl_categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], $messages);

        $data = $request->except(['imagen', '_token', '_method']);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/lugares'), $imageName);
            $data['imagen'] = $imageName;
        }

        Lugar::create($data);

        return redirect()->back()->with('success', 'Lugar creado correctamente.');
    }

    public function updateLugar(Request $request, $id)
    {
        $lugar = Lugar::findOrFail($id);
        
        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'direccion_completa.required' => 'La dirección es obligatoria.',
            'latitud.required' => 'La latitud es obligatoria.',
            'latitud.between' => 'La latitud debe estar entre -90 y 90.',
            'longitud.required' => 'La longitud es obligatoria.',
            'longitud.between' => 'La longitud debe estar entre -180 y 180.',
            'id_categoria.required' => 'La categoría es obligatoria.',
            'id_categoria.exists' => 'La categoría seleccionada no es válida.',
            'numeric' => 'Este campo debe ser un número.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen.max' => 'La imagen no debe pesar más de 10MB.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion_completa' => 'required|string|max:255',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'id_categoria' => 'required|exists:tbl_categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], $messages);

        $data = $request->except(['imagen', '_token', '_method']);

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe y no es la por defecto
            if ($lugar->imagen && $lugar->imagen !== 'default_lugar.jpg' && file_exists(public_path('img/lugares/' . $lugar->imagen))) {
                unlink(public_path('img/lugares/' . $lugar->imagen));
            }

            $file = $request->file('imagen');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/lugares'), $imageName);
            $data['imagen'] = $imageName;
        }

        $lugar->update($data);

        return redirect()->back()->with('success', 'Lugar actualizado correctamente.');
    }

    public function deleteLugar($id)
    {
        $lugar = Lugar::findOrFail($id);
        $lugar->delete();

        return redirect()->back()->with('success', 'Lugar eliminado correctamente.');
    }
}
