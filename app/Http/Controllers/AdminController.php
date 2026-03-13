<?php

namespace App\Http\Controllers;

use App\Models\Sala;
use App\Models\Lugar;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\Prueba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'numeric' => 'Este campo debe ser un número.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen.max' => 'La imagen no debe pesar más de 10MB.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|min:10',
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
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres.',
            'numeric' => 'Este campo debe ser un número.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen.max' => 'La imagen no debe pesar más de 10MB.',
        ];

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|min:10',
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

    // --- USUARIOS CRUD (AJAX) ---
    public function usuarios()
    {
        $user = Usuario::first();
        // Return view and pass initial total count
        $totalUsuarios = Usuario::count();
        return view('admin.usuarios', compact('user', 'totalUsuarios'));
    }

    public function apiUsuarios(Request $request)
    {
        $query = Usuario::query();
        
        if ($request->has('nombre') && $request->nombre != '') {
            $nombre = $request->nombre;
            $query->where(function($q) use ($nombre) {
                $q->where('nombre', 'like', "%{$nombre}%")
                  ->orWhere('username', 'like', "%{$nombre}%");
            });
        }
        
        if ($request->has('rol') && $request->rol != '') {
            $query->where('id_rol', $request->rol);
        }

        $usuarios = $query->get();
        return response()->json($usuarios);
    }

    public function apiStoreUsuario(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:25|unique:tbl_usuarios',
            'nombre' => 'required|string|max:25',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'email' => 'required|email|max:100|unique:tbl_usuarios',
            'password' => 'required|string|min:6',
            'id_rol' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['foto']);
        $data['password'] = bcrypt($request->password);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/usuarios'), $filename);
            $data['foto'] = $filename;
        }

        $usuario = Usuario::create($data);

        return response()->json(['success' => true, 'usuario' => $usuario]);
    }

    public function apiUpdateUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:25|unique:tbl_usuarios,username,' . $usuario->id,
            'nombre' => 'required|string|max:25',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'email' => 'required|email|max:100|unique:tbl_usuarios,email,' . $usuario->id,
            'password' => 'nullable|string|min:6',
            'id_rol' => 'required|integer',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['foto', 'password']);
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($usuario->foto && $usuario->foto !== 'default_user.png' && file_exists(public_path('img/usuarios/' . $usuario->foto))) {
                unlink(public_path('img/usuarios/' . $usuario->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalExtension();
            $file->move(public_path('img/usuarios'), $filename);
            $data['foto'] = $filename;
        }

        $usuario->update($data);

        return response()->json(['success' => true, 'usuario' => $usuario]);
    }

    public function apiDeleteUsuario($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        if ($usuario->foto && $usuario->foto !== 'default_user.png' && file_exists(public_path('img/usuarios/' . $usuario->foto))) {
            unlink(public_path('img/usuarios/' . $usuario->foto));
        }
        
        $usuario->delete();

        return response()->json(['success' => true]);
    }

    // --- SALAS (GIMCANAS) CRUD ---

    public function salas()
    {
        $user = Usuario::first();
        $salas = Sala::with(['creador', 'pruebas.lugar'])->get();
        $totalSalas = $salas->count();

        return view('admin.salas', compact('salas', 'user', 'totalSalas'));
    }

    public function createSala()
    {
        $user = Usuario::first();
        $lugaresSeleccionables = Lugar::all();
        
        return view('admin.salas_create', compact('user', 'lugaresSeleccionables'));
    }

    public function storeSala(Request $request)
    {
        $messages = [
            'nombre.required'  => 'El nombre de la gimcana es obligatorio.',
            'nombre.unique'    => 'Ya existe una gimcana con ese nombre.',
            'nombre.max'       => 'El nombre no puede superar los 50 caracteres.',
            'descripcion.max'  => 'La descripción no puede superar los 255 caracteres.',
            'lugares.required' => 'Debes seleccionar exactamente 5 lugares.',
            'lugares.array'    => 'Formato de lugares inválido.',
            'lugares.size'     => 'Debe haber exactamente 5 lugares.',
            'lugares.*.id_lugar.required' => 'Debes seleccionar un lugar válido.',
            'lugares.*.id_lugar.exists'   => 'El lugar seleccionado no existe.',
            'lugares.*.pregunta.required' => 'El reto (pregunta) es obligatorio para cada lugar.',
            'lugares.*.pregunta.min'      => 'El reto debe tener al menos 10 caracteres.',
            'lugares.*.respuesta_correcta.required' => 'La respuesta correcta es obligatoria.',
            'lugares.*.pista.required'    => 'La pista es obligatoria para cada lugar.',
            'lugares.*.pista.min'         => 'La pista debe tener al menos 5 caracteres.',
        ];

        $request->validate([
            'nombre'      => 'required|string|max:50|unique:tbl_salas,nombre',
            'descripcion' => 'nullable|string|max:255',
            'lugares'     => 'required|array|size:5',
            'lugares.*.id_lugar'           => 'required|exists:tbl_lugares,id',
            'lugares.*.pregunta'           => 'required|string|min:10',
            'lugares.*.respuesta_correcta' => 'required|string|max:255',
            'lugares.*.pista'              => 'required|string|min:5',
        ], $messages);

        \DB::beginTransaction();

        try {
            $sala = Sala::create([
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            foreach ($request->lugares as $index => $lugarData) {
                Prueba::create([
                    'id_sala'            => $sala->id,
                    'id_lugar'           => $lugarData['id_lugar'],
                    'orden'              => $index + 1,
                    'pista'              => $lugarData['pista'],
                    'pregunta'           => $lugarData['pregunta'],
                    'respuesta_correcta' => $lugarData['respuesta_correcta']
                ]);
            }

            \DB::commit();

            return redirect()->route('admin.salas')->with('success', 'Gimcana creada correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un error al crear la gimcana: ' . $e->getMessage()])->withInput();
        }
    }

    public function editSala($id)
    {
        $sala = Sala::with('pruebas')->findOrFail($id);
        $user = Usuario::first();
        $lugaresSeleccionables = Lugar::all();
        
        return view('admin.salas_edit', compact('sala', 'user', 'lugaresSeleccionables'));
    }

    public function updateSala(Request $request, $id)
    {
        $sala = Sala::findOrFail($id);

        $messages = [
            'nombre.required'  => 'El nombre de la gimcana es obligatorio.',
            'nombre.unique'    => 'Ya existe una gimcana con ese nombre.',
            'nombre.max'       => 'El nombre no puede superar los 50 caracteres.',
            'descripcion.max'  => 'La descripción no puede superar los 255 caracteres.',
            'lugares.required' => 'Debes seleccionar exactamente 5 lugares.',
            'lugares.array'    => 'Formato de lugares inválido.',
            'lugares.size'     => 'Debe haber exactamente 5 lugares.',
            'lugares.*.id_lugar.required' => 'Debes seleccionar un lugar válido.',
            'lugares.*.id_lugar.exists'   => 'El lugar seleccionado no existe.',
            'lugares.*.pregunta.required' => 'El reto (pregunta) es obligatorio para cada lugar.',
            'lugares.*.pregunta.min'      => 'El reto debe tener al menos 10 caracteres.',
            'lugares.*.respuesta_correcta.required' => 'La respuesta correcta es obligatoria.',
            'lugares.*.pista.required'    => 'La pista es obligatoria para cada lugar.',
            'lugares.*.pista.min'         => 'La pista debe tener al menos 5 caracteres.',
        ];

        $request->validate([
            'nombre'      => 'required|string|max:50|unique:tbl_salas,nombre,' . $sala->id,
            'descripcion' => 'nullable|string|max:255',
            'lugares'     => 'required|array|size:5',
            'lugares.*.id_lugar'           => 'required|exists:tbl_lugares,id',
            'lugares.*.pregunta'           => 'required|string|min:10',
            'lugares.*.respuesta_correcta' => 'required|string|max:255',
            'lugares.*.pista'              => 'required|string|min:5',
        ], $messages);

        \DB::beginTransaction();

        try {
            $sala->update([
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            Prueba::where('id_sala', $sala->id)->delete();

            foreach ($request->lugares as $index => $lugarData) {
                Prueba::create([
                    'id_sala'            => $sala->id,
                    'id_lugar'           => $lugarData['id_lugar'],
                    'orden'              => $index + 1,
                    'pista'              => $lugarData['pista'],
                    'pregunta'           => $lugarData['pregunta'],
                    'respuesta_correcta' => $lugarData['respuesta_correcta']
                ]);
            }

            \DB::commit();

            return redirect()->route('admin.salas')->with('success', 'Gimcana actualizada correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un error al actualizar la gimcana: ' . $e->getMessage()])->withInput();
        }
    }

    public function deleteSala($id)
    {
        $sala = Sala::findOrFail($id);
        
        \DB::beginTransaction();
        try {
            Prueba::where('id_sala', $sala->id)->delete();
            $sala->delete();
            \DB::commit();

            return redirect()->back()->with('success', 'Gimcana eliminada correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un error al eliminar la gimcana: ' . $e->getMessage()]);
        }
    }
}
