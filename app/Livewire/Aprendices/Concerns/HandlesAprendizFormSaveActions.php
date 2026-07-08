<?php

namespace App\Livewire\Aprendices\Concerns;

use App\Models\Aprendiz;
use Illuminate\Support\Facades\DB;

trait HandlesAprendizFormSaveActions
{
    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->isEdit) {
                $this->aprendiz->update([
                    'ficha_caracterizacion_id' => $this->ficha_caracterizacion_id,
                    'estado' => $this->estado,
                ]);

                $message = 'Aprendiz actualizado correctamente';
                $this->dispatch('aprendizActualizado');
            } else {
                Aprendiz::create([
                    'persona_id' => $this->persona_id,
                    'ficha_caracterizacion_id' => $this->ficha_caracterizacion_id,
                    'estado' => $this->estado,
                ]);

                $message = 'Aprendiz creado correctamente';
                $this->dispatch('aprendizCreado');
            }

            DB::commit();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message,
            ]);

            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar el aprendiz: '.$e->getMessage(),
            ]);
        }
    }
}
