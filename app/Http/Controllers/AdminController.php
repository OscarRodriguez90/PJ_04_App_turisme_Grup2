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
            'longitud.required' => 'La longitud es obligatoria.',
            'id_categoria.required' => 'La categoría es obligatoria.',
            'id_categoria.exists' => 'La categoría seleccionada no es válida.',
            'numeric' => 'Este campo debe ser un número.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion_completa' => 'required|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'id_categoria' => 'required|exists:tbl_categorias,id',
        ], $messages);

        Lugar::create($request->all());

        return redirect()->back()->with('success', 'Lugar creado correctamente.');
    }

    public function updateLugar(Request $request, $id)
    {
        $lugar = Lugar::findOrFail($id);

        $messages = [
            'nombre.required' => 'El nombre es obligatorio.',
            'direccion_completa.required' => 'La dirección es obligatoria.',
            'latitud.required' => 'La latitud es obligatoria.',
            'longitud.required' => 'La longitud es obligatoria.',
            'id_categoria.required' => 'La categoría es obligatoria.',
            'id_categoria.exists' => 'La categoría seleccionada no es válida.',
            'numeric' => 'Este campo debe ser un número.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'direccion_completa' => 'required|string|max:255',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'id_categoria' => 'required|exists:tbl_categorias,id',
        ], $messages);

        $lugar->update($request->all());

        return redirect()->back()->with('success', 'Lugar actualizado correctamente.');
    }

    public function deleteLugar($id)
    {
        $lugar = Lugar::findOrFail($id);
        $lugar->delete();

        return redirect()->back()->with('success', 'Lugar eliminado correctamente.');
    }

    // ── Categorías CRUD ──────────────────────────────────────────────────────

    public function categorias()
    {
        $user = Usuario::first();
        $categorias = Categoria::withCount('lugares')->get();
        return view('admin.categorias', compact('categorias', 'user'));
    }

    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:100|unique:tbl_categorias,nombre',
            'icono_url'      => 'nullable|string|max:100',
            'color_marcador' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con ese nombre.',
            'color_marcador.regex' => 'El color debe tener formato hexadecimal (#RRGGBB).',
        ]);

        Categoria::create([
            'nombre'         => $request->nombre,
            'icono_url'      => $request->icono_url ?: null,
            'color_marcador' => $request->color_marcador ?: '#0ea5a4',
        ]);

        return redirect()->route('admin.categorias')->with('success', 'Categoría creada correctamente.');
    }

    public function updateCategoria(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre'         => 'required|string|max:100|unique:tbl_categorias,nombre,' . $id,
            'icono_url'      => 'nullable|string|max:100',
            'color_marcador' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con ese nombre.',
            'color_marcador.regex' => 'El color debe tener formato hexadecimal (#RRGGBB).',
        ]);

        $categoria->update([
            'nombre'         => $request->nombre,
            'icono_url'      => $request->icono_url ?: null,
            'color_marcador' => $request->color_marcador ?: '#0ea5a4',
        ]);

        return redirect()->route('admin.categorias')->with('success', 'Categoría actualizada correctamente.');
    }

    public function deleteCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()->route('admin.categorias')->with('success', 'Categoría eliminada correctamente.');
    }
}
