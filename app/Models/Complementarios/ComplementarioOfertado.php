<?php

namespace App\Models\Complementarios;

use App\Models\Ambiente;
use App\Models\Competencia;
use App\Models\GuiasAprendizaje;
use App\Models\JornadaFormacion;
use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\ResultadosAprendizaje;
use App\Models\User;
use Database\Factories\ComplementarioOfertadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplementarioOfertado extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ComplementarioOfertadoFactory::new();
    }

    protected $table = 'complementarios_ofertados';

    protected $fillable = [
        'codigo',
        'nombre',
        'justificacion',
        'requisitos_ingreso',
        'duracion',
        'cupos',
        'estado',
        'modalidad_id',
        'jornada_id',
        'ambiente_id',
        'user_create_id',
        'user_edit_id',
    ];

    public function modalidad()
    {
        return $this->belongsTo(ParametroTema::class, 'modalidad_id');
    }

    public function jornada()
    {
        return $this->belongsTo(JornadaFormacion::class, 'jornada_id');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    /**
     * Usuario que creó el registro
     */
    public function usuarioCreador()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Usuario que editó el registro por última vez
     */
    public function usuarioEditor()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function diasFormacion()
    {
        return $this->belongsToMany(Parametro::class, 'complementarios_ofertados_dias_formacion', 'complementario_id', 'dia_id')
                    ->withPivot('hora_inicio', 'hora_fin');
    }

    public function aspirantes()
    {
        return $this->hasMany(AspiranteComplementario::class, 'complementario_id');
    }

    /**
     * Relación muchos a muchos con Competencias
     */
    public function competencias()
    {
        return $this->belongsToMany(
            Competencia::class,
            'competencia_complementario',
            'complementario_id',
            'competencia_id'
        )->withTimestamps()
         ->withPivot('user_create_id', 'user_edit_id');
    }

    /**
     * Relación muchos a muchos con Resultados de Aprendizaje
     */
    public function raps()
    {
        return $this->belongsToMany(
            ResultadosAprendizaje::class,
            'resultado_aprendizaje_complementario',
            'complementario_id',
            'rap_id'
        )->withTimestamps()
         ->withPivot('user_create_id', 'user_edit_id');
    }

    /**
     * Relación muchos a muchos con Guías de Aprendizaje
     */
    public function guiasAprendizaje()
    {
        return $this->belongsToMany(
            GuiasAprendizaje::class,
            'guia_aprendizaje_complementario',
            'complementario_id',
            'guia_aprendizaje_id'
        )->withTimestamps()
         ->withPivot('user_create_id', 'user_edit_id');
    }

    public function getEstadoLabelAttribute()
    {
        return match ($this->estado) {
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
            default => 'Desconocido',
        };
    }

    public function getBadgeClassAttribute()
    {
        return match ($this->estado) {
            0 => 'bg-success',
            1 => 'bg-warning',
            2 => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getIconoAttribute()
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$this->nombre] ?? 'fas fa-graduation-cap';
    }

    /**
     * Calcular tasa de aceptación de aspirantes
     * 
     * Este accessor calcula la tasa de aceptación basándose en los atributos
     * total_aspirantes y aceptados que pueden venir de consultas agregadas.
     * 
     * @return float Tasa de aceptación en porcentaje (0-100)
     */
    public function getTasaAceptacionAttribute(): float
    {
        // Los atributos agregados se almacenan directamente en $this->attributes
        $totalAspirantes = $this->attributes['total_aspirantes'] ?? 0;
        $aceptados = $this->attributes['aceptados'] ?? 0;

        if ($totalAspirantes > 0 && is_numeric($aceptados) && is_numeric($totalAspirantes)) {
            return round(($aceptados / $totalAspirantes) * 100, 1);
        }

        return 0.0;
    }
}
