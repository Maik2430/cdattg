<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\ProgramaFormacion;

trait HandlesProgramaFormUpdateActions
{
    use HandlesProgramaFormNormalizeHelpers;

    public function update()
    {
        try {
            $this->normalizeProgramaFormFields();

            $validated = $this->validate();

            if ($this->programaCodigoDuplicado($validated['codigo'], $this->programaId)) {
                $this->addError('codigo', 'El código ya está siendo utilizado por otro programa.');

                return;
            }

            if (($validated['horas_etapa_lectiva'] + $validated['horas_etapa_productiva']) != $validated['horas_totales']) {
                $this->addError('horas_totales', 'La suma de horas lectiva y productiva debe coincidir con el total.');

                return;
            }

            $programa = ProgramaFormacion::find($this->programaId);
            if (! $programa) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Programa no encontrado',
                ]);

                return;
            }

            $validated['user_edit_id'] = auth()->id();

            $programa->update($validated);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Programa actualizado correctamente',
            ]);

            $this->dispatch('programaActualizado');
            $this->reset();
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al actualizar el programa: '.$e->getMessage(),
            ]);
        }
    }
}
