<?php

namespace App\Livewire\Fichas\Concerns\FichaForm;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

trait HandlesFichaFormSaveActions
{
    use HandlesFichaFormSaveDataHelpers;

    public function save(): void
    {
        try {
            Log::info('[FichaForm] Intentando guardar ficha', [
                'isEdit' => $this->isEdit,
                'ficha_codigo' => $this->ficha_codigo,
                'programa_formacion_id' => $this->programa_formacion_id,
            ]);

            $this->validate();

            DB::beginTransaction();

            $data = $this->buildFichaData();

            if ($this->isEdit && $this->ficha) {
                $message = $this->updateExistingFicha($data);
            } else {
                $message = $this->createNewFicha($data);

                if ($message === null) {
                    DB::rollBack();

                    return;
                }
            }

            DB::commit();

            $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
            $this->dispatch('closeModal');

            if (! $this->isEdit) {
                $this->resetForm();
            }
        } catch (ValidationException $e) {
            Log::error('[FichaForm] Error de validación', [
                'errors' => $e->errors(),
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error de validación: '.$e->getMessage(),
            ]);
            DB::rollBack();
        } catch (Exception $e) {
            Log::error('[FichaForm] Error guardando ficha', [
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Error al guardar la ficha: '.$e->getMessage(),
            ]);
            DB::rollBack();
        }
    }
}
