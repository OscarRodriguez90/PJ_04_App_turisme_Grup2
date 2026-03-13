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

    // --- SALAS (GIMCANAS) CRUD ---

    public function salas()
    {
        $user = Usuario::first();
        $salas = Sala::with(['creador', 'pruebas.lugar'])->get();
        $totalSalas = $salas->count();
        $lugares = Lugar::all(); // Para el selector de lugares en el modal

        return view('admin.salas', compact('salas', 'user', 'totalSalas', 'lugares'));
    }

    public function storeSala(Request $request)
    {
        $messages = [
            'codigo_sala.required' => 'El código de la sala es obligatorio.',
            'codigo_sala.unique' => 'Este código de sala ya existe.',
            'codigo_sala.max' => 'El código no puede superar los 8 caracteres.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'lugares.required' => 'Debes seleccionar exactamente 5 lugares.',
            'lugares.array' => 'Formato de lugares inválido.',
            'lugares.size' => 'Debe haber exactamente 5 lugares.',
            'lugares.*.id_lugar.required' => 'Debes seleccionar un lugar válido.',
            'lugares.*.id_lugar.exists' => 'El lugar seleccionado no existe.',
            'lugares.*.pregunta.required' => 'El reto (pregunta) es obligatorio para cada lugar.',
            'lugares.*.pregunta.min' => 'El reto debe tener al menos 10 caracteres.',
            'lugares.*.respuesta_correcta.required' => 'La respuesta correcta es obligatoria.',
            'lugares.*.pista.required' => 'La pista es obligatoria para cada lugar.',
            'lugares.*.pista.min' => 'La pista debe tener al menos 5 caracteres.',
        ];

        $request->validate([
            'codigo_sala' => 'required|string|max:8|unique:tbl_salas,codigo_sala',
            'estado' => 'nullable|in:esperando,jugando,finalizada',
            'lugares' => 'required|array|size:5',
            'lugares.*.id_lugar' => 'required|exists:tbl_lugares,id',
            'lugares.*.pregunta' => 'required|string|min:10',
            'lugares.*.respuesta_correcta' => 'required|string|max:255',
            'lugares.*.pista' => 'required|string|min:5',
        ], $messages);

        \DB::beginTransaction();

        try {
            $user = Usuario::first(); // Demo purposes, otherwise use auth user

            $sala = Sala::create([
                'codigo_sala' => $request->codigo_sala,
                'id_creador' => $user->id,
                'estado' => $request->estado ?? 'esperando',
            ]);

            foreach ($request->lugares as $index => $lugarData) {
                Prueba::create([
                    'id_sala' => $sala->id,
                    'id_lugar' => $lugarData['id_lugar'],
                    'orden' => $index + 1,
                    'pista' => $lugarData['pista'],
                    'pregunta' => $lugarData['pregunta'],
                    'respuesta_correcta' => $lugarData['respuesta_correcta']
                ]);
            }

            \DB::commit();

            return redirect()->back()->with('success', 'Gimcana creada correctamente.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un error al crear la gimcana: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateSala(Request $request, $id)
    {
        $sala = Sala::findOrFail($id);

        $messages = [
            'codigo_sala.required' => 'El código de la sala es obligatorio.',
            'codigo_sala.unique' => 'Este código de sala ya existe.',
            'codigo_sala.max' => 'El código no puede superar los 8 caracteres.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'lugares.required' => 'Debes seleccionar exactamente 5 lugares.',
            'lugares.array' => 'Formato de lugares inválido.',
            'lugares.size' => 'Debe haber exactamente 5 lugares.',
            'lugares.*.id_lugar.required' => 'Debes seleccionar un lugar válido.',
            'lugares.*.id_lugar.exists' => 'El lugar seleccionado no existe.',
            'lugares.*.pregunta.required' => 'El reto (pregunta) es obligatorio para cada lugar.',
            'lugares.*.pregunta.min' => 'El reto debe tener al menos 10 caracteres.',
            'lugares.*.respuesta_correcta.required' => 'La respuesta correcta es obligatoria.',
            'lugares.*.pista.required' => 'La pista es obligatoria para cada lugar.',
            'lugares.*.pista.min' => 'La pista debe tener al menos 5 caracteres.',
        ];

        $request->validate([
            'codigo_sala' => 'required|string|max:8|unique:tbl_salas,codigo_sala,' . $sala->id,
            'estado' => 'nullable|in:esperando,jugando,finalizada',
            'lugares' => 'required|array|size:5',
            'lugares.*.id_lugar' => 'required|exists:tbl_lugares,id',
            'lugares.*.pregunta' => 'required|string|min:10',
            'lugares.*.respuesta_correcta' => 'required|string|max:255',
            'lugares.*.pista' => 'required|string|min:5',
        ], $messages);

        \DB::beginTransaction();

        try {
            $sala->update([
                'codigo_sala' => $request->codigo_sala,
                'estado' => $request->estado ?? $sala->estado,
            ]);

            // Delete old pruebas and create new ones based on the updated form
            Prueba::where('id_sala', $sala->id)->delete();

            foreach ($request->lugares as $index => $lugarData) {
                Prueba::create([
                    'id_sala' => $sala->id,
                    'id_lugar' => $lugarData['id_lugar'],
                    'orden' => $index + 1,
                    'pista' => $lugarData['pista'],
                    'pregunta' => $lugarData['pregunta'],
                    'respuesta_correcta' => $lugarData['respuesta_correcta']
                ]);
            }

            \DB::commit();

            return redirect()->back()->with('success', 'Gimcana actualizada correctamente.');
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
