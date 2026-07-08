<?php

namespace App\Livewire\ResultadosAprendizaje;

use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeFormMountHelpers;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeFormRenderActions;
use App\Livewire\ResultadosAprendizaje\Concerns\HandlesResultadoAprendizajeFormSaveActions;
use Livewire\Component;

class ResultadoAprendizajeForm extends Component
{
    use HandlesResultadoAprendizajeFormMountHelpers;
    use HandlesResultadoAprendizajeFormRenderActions;
    use HandlesResultadoAprendizajeFormSaveActions;

    public $codigo;

    public $nombre;

    public $duracion;

    public $competencia_id;

    public $status = true;

    public $isEdit = false;

    public $resultadoId;

    protected $rules = [
        'codigo' => 'required|string|max:20|unique:resultados_aprendizajes,codigo',
        'nombre' => 'required|string|max:255',
        'duracion' => 'nullable|numeric|min:0|max:9999.99',
        'competencia_id' => 'nullable|exists:competencias,id',
        'status' => 'boolean',
    ];

    protected $messages = [
        'codigo.required' => 'El código es obligatorio',
        'codigo.unique' => 'Este código ya está registrado',
        'nombre.required' => 'El nombre es obligatorio',
        'duracion.numeric' => 'La duración debe ser un número',
        'duracion.min' => 'La duración no puede ser negativa',
        'duracion.max' => 'La duración máxima es 9999.99 horas',
        'competencia_id.exists' => 'La competencia seleccionada no es válida',
    ];
}
