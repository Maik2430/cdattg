<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

use App\Models\FichaCaracterizacion;
use App\Models\FichaDiasFormacion;
use Illuminate\Support\Facades\Log;

trait HandlesFichaFormSaveDataHelpers
{
    private function buildFichaData(): array
    {
        return [
            'ficha' => $this->ficha_codigo,
            'programa_formacion_id' => $this->programa_formacion_id,
            'sede_id' => $this->sede_id,
            'instructor_id' => $this->instructor_id,
            'ambiente_id' => $this->ambiente_id,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'modalidad_formacion_id' => $this->modalidad_formacion_id,
            'jornada_id' => $this->jornada_id,
            'total_horas' => $this->total_horas,
            'status' => $this->status,
            'user_create_id' => auth()->id(),
            'user_edit_id' => auth()->id(),
        ];
    }

    private function syncDiasFormacion(int $fichaId): void
    {
        FichaDiasFormacion::where('ficha_id', $fichaId)->delete();

        foreach ($this->dias_formacion as $diaId) {
            FichaDiasFormacion::create([
                'ficha_id' => $fichaId,
                'dia_id' => (int) $diaId,
            ]);
        }
    }

    private function updateExistingFicha(array $data): string
    {
        $this->ficha->update($data);
        $this->syncDiasFormacion($this->ficha->id);

        $this->dispatch('fichaActualizada');
        Log::info('[FichaForm] Ficha actualizada correctamente', [
            'ficha_id' => $this->ficha->id,
        ]);

        return 'Ficha actualizada exitosamente';
    }

    private function createNewFicha(array $data): ?string
    {
        $existingFicha = FichaCaracterizacion::where('ficha', $this->ficha_codigo)->first();

        if ($existingFicha) {
            Log::warning('[FichaForm] Ya existe una ficha con este código', [
                'ficha_codigo' => $this->ficha_codigo,
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ya existe una ficha con este código',
            ]);

            return null;
        }

        $ficha = FichaCaracterizacion::create($data);
        $this->syncDiasFormacion($ficha->id);

        $this->dispatch('fichaCreada');
        Log::info('[FichaForm] Ficha creada correctamente', [
            'ficha_id' => $ficha->id,
        ]);

        return 'Ficha creada exitosamente';
    }
}
