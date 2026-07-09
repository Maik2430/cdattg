<?php

namespace App\Http\Controllers\Concerns\CentroFormacion;

use App\Models\CentroFormacion;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesCentroFormacionCrudWriteActions
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionals,id',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'web' => 'nullable|url|max:255',
        ]);

        try {
            DB::beginTransaction();

            CentroFormacion::create([
                'nombre' => $request->nombre,
                'regional_id' => $request->regional_id,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'web' => $request->web,
                'status' => 1,
                'user_create_id' => Auth::id(),
                'user_update_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('centros.index')
                ->with('success', 'Centro de formación creado con éxito');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al crear centro de formación: '.$e->getMessage());

            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al momento de crear el centro de formación']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionals,id',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:255',
            'web' => 'nullable|url|max:255',
            'status' => 'required|in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $centro = CentroFormacion::findOrFail($id);
            $centro->update([
                'nombre' => $request->nombre,
                'regional_id' => $request->regional_id,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'web' => $request->web,
                'status' => $request->status,
                'user_update_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('centros.show', $centro->id)
                ->with('success', 'Centro de formación actualizado con éxito');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar centro de formación: '.$e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', 'Error al momento de actualizar el centro de formación');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $centro = CentroFormacion::findOrFail($id);
            $centro->delete();

            DB::commit();

            return redirect()->route('centros.index')
                ->with('success', 'Centro de formación eliminado exitosamente');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al eliminar centro de formación: '.$e->getMessage());

            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->with('error', 'El centro de formación se encuentra en uso, no se puede eliminar');
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar el centro de formación');
        }
    }
}
