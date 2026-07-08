<?php

namespace App\Livewire\Programas\Concerns;

use App\Models\ProgramaFormacion;

trait HandlesProgramaFormStoreActions
{
    use HandlesProgramaFormNormalizeHelpers;

    public function store()
    {
        try {
            $this->normalizeProgramaFormFields();

            $validated = $this->validate();

            if ($this->programaCodigoDuplicado($validated['codigo'])) {
                $this->addError('codigo', 'El código ya está siendo utilizado por otro programa.');

                return;
            }

            $validated['status'] = true;
            $validated['user_create_id'] = auth()->id();

            ProgramaFormacion::create($validated);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Programa creado correctamente',
            ]);

            $this->dispatch('programaCreado');
            $this->reset();
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al crear el programa: '.$e->getMessage(),
            ]);
        }
    }
}
